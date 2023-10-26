<?php

namespace App\Support\Services;

use Carbon\Carbon;
use App\Models\Rate;
use App\Models\Task;
use App\Models\Asset;
use App\Models\Invoice;
use App\Enums\RevenueType;
use App\Models\InvoiceLine;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use App\Interfaces\Services\FinanceService;
use App\Actions\Invoices\MarkInvoiceSentToAccounts;
use League\OAuth2\Client\Provider\AbstractProvider;
use App\Actions\InvoiceLines\MarkInvoiceLineSentToAccounts;

class AX implements FinanceService
{
    public function getUrl() {
        return config('client.services.data.ax.url');
    }

    public function provider(): AbstractProvider|null
    {
        return null;
    }

    public function redirectToProvider(): RedirectResponse|null
    {
        return null;
    }

    public function handleProviderCallback(Request $request)
    {
        return null;
    }

    public function refreshAccessTokenIfNecessary()
    {
        return null;
    }

    public function importCompanies() {
        throw new \Exception('AX does not support importing companies.');
    }

    public function syncInvoice(Invoice|int $invoice)
    {
        if(is_int($invoice)) {
            $invoice = Invoice::findOrFail($invoice);
        }

        $template = 'standard';

        $data = [
            "object" => "invoice",
            "id" => $invoice->id,
            "layout_template" => $template,
            "title" => $invoice->title,
            "reference" => $invoice->reference ?? $invoice->task->title,
            "invoice_date" => $invoice->invoice_date?->format('d-m-Y'),
            "due_date" => $invoice->due_date?->format('d-m-Y'),
            "po_number" => $invoice->po_number,
            "vat_number" => $invoice->vat_number,
            "contents" =>  $invoice->contents ? $invoice->contents : '',
            "validity" => $invoice->validity,
            "sub_total" => $invoice->sub_total,
            "vat_total" => $invoice->vat_total,
            "total" => $invoice->total,
            "sage_reference" => $invoice->payment_reference,
            'cost_center' => null,
            "lines" => []
        ];

        if (isset($invoice->task->meta['cost_center'])) {
            $data['cost_center'] = $invoice->task->meta['cost_center'];
        }

        if (!($invoice->task->completed_at && $invoice->task->completed_at->lte(Carbon::parse('2022-04-01 00:00:00'))) && isset($data['cost_center'])) {
            if ($data['cost_center'] == 'STWREACT') {
                $data['cost_center'] = $invoice->task->depot->payment_reference;
            }
        }

        $invoice->invoiceLines()->whereNull('finance_reference')->lazy()->each(function($invoiceLine) use(&$invoice, &$data, &$template) {       
            $invoiceLineData = [
                "object" => "invoice-line-" . $template,
                "id"     => $invoiceLine->id,
                'type' => 'earning',
                "category" => $invoiceLine->accountType->finance_code,
                "title"  => $invoiceLine->title,
                "code"   => $invoiceLine->reference,
                "unit_amount" => $invoiceLine->amount,
                "quantity" => $invoiceLine->quantity,
                "vat" => $invoiceLine->vat,
                "sub_total" => $invoiceLine->sub_total,
                "vat_total" => $invoiceLine->vat_total,
                "total" => $invoiceLine->total,
                "asset" => null,
                "assigned_users" => [],
                "earning_type" => $invoiceLine->revenue?->revenueType?->title
            ];

            if ($invoiceLine->revenue) {

                if($invoiceLine->revenue->type == RevenueType::INDIVIDUAL_EARNING) {
                    $invoiceLineData["type"] .= 'basic';
                }

                if ($invoiceLine->revenue->type == RevenueType::INTRACOMPANY) {
                    $invoiceLineData["type"] .= '-intercompany';
                }
                
                if ($invoiceLine->revenue->revenuable instanceof Asset) {
                    $invoiceLineData['asset'] = [
                        "object" => "invoice-asset",
                        "id"     => $invoiceLine->revenue->revenuable->id,
                        "title"  => $invoiceLine->revenue->revenuable->title,
                    ];
                }

                if ($invoiceLine->revenue->assignment && $invoiceLine->revenue->assignment->assignmentGroup) {
                    $assignmentGroupLoginStatuses = $invoiceLine->revenue->assignment->assignmentGroup->assignmentGroupLoginStatuses;

                    $startDate = $invoiceLine->revenue->assignment->assignmentGroup->start_date->format('d-m-Y');
                    $endDate = $invoiceLine->revenue->assignment->assignmentGroup->end_date->format('d-m-Y');
                    $duration = $invoiceLine->revenue->assignment->assignmentGroup->start_date->diffInMinutes($invoiceLine->revenue->assignment->assignmentGroup->end_date);

                    foreach ($invoiceLine->revenue->assignment->assignmentGroup->userAssignments as $userAssignment) {
                        $assignedUser = [
                            "object" => "invoice-user",
                            "id"     => $userAssignment->assignable->id,
                            "first_name" => $userAssignment->assignable->first_name,
                            "last_name" => $userAssignment->assignable->last_name,
                            "start_date" => $startDate,
                            "end_date" => $endDate,
                            "duration" => $duration,
                            "travel_time" => null,
                            "site_logins" => []
                        ];

                        $siteLogins = $assignmentGroupLoginStatuses->sortBy('login_date')->filter(function ($assignmentGroupLoginStatus, $key) use (&$userAssignment) {
                            return $assignmentGroupLoginStatus->user_id == $userAssignment->assignable->id;
                        });

                        foreach ($siteLogins as $siteLogin) {
                            if($siteLogin->logout_date) {
                                array_push($assignedUser["site_logins"], [
                                    "object" => "invoice-site-login",
                                    "type" => 'login',
                                    "log_date" => $siteLogin->login_date?->format('d-m-Y')
                                ]);
                            }

                            if($siteLogin->logout_date) {
                                array_push($assignedUser["site_logins"], [
                                    "object" => "invoice-site-login",
                                    "type" => 'logout',
                                    "log_date" => $siteLogin->login_date?->format('d-m-Y')
                                ]);
                            }
                        }
                        array_push($invoiceLineData['assigned_users'], $assignedUser);
                    }
                }
            }
            
            array_push($data['lines'], $invoiceLineData);
        });
        
        $response = $this->post('/api/addinvoice', $data);
       
        $responseBody = $response->json();

        $linesCreated = 0;
        if (isset($responseBody["Result"]) && $responseBody["Result"] == "Success") {
            foreach ($responseBody["lines"] as $index => $line) {
                app(MarkInvoiceLineSentToAccounts::class)->execute($line['id'], $line['result']);
                ++$linesCreated;
            }
        }

        $financeReference = null;
        if ($linesCreated == count($data['lines']))
        {
            $financeReference = (string)Str::uuid();
            $invoice = app(MarkInvoiceSentToAccounts::class)->execute($invoice, $financeReference);
        }

        if($linesCreated < count($data['lines'])) {
            return abort(400, 'Unable to submit all invoice lines. Submitted ' . $linesCreated . ' invoice lines.');
        }

        return $invoice;
    }

    public function post($url, array $data) {
        return $this->fetch('post', $this->getUrl() . $url, $data);
    }


    public function fetch($method, $url, $data) {
        logger()->channel('ax')->info(
            print_r([
                'method' => $method,
                'url' => $url,
                'request' => json_encode($data),
            ], true)
        );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])
        ->$method($url, $data);

       
        logger()->channel('ax')->info(
            print_r([
                'method' => $method,
                'url' => $url,
                'response' => $response->body(),
            ], true)
        );

        $this->handleResponse($response);
        
        return $response;
    }

    public function handleResponse($response) {
        $response?->throw();
    }
}
