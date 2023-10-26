<?php

namespace App\Support\Services;

use Throwable;
use App\Models\File;
use App\Models\Rate;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Revenue;
use App\Models\InvoiceLine;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\Services\TaskService;
use App\Interfaces\Services\FinanceService;
use App\Actions\Invoices\MarkInvoiceSentToAccounts;
use App\Actions\Revenues\MarkRevenueSentToAccounts;
use League\OAuth2\Client\Provider\AbstractProvider;
use App\Actions\InvoiceLines\MarkInvoiceLineSentToAccounts;

class Dynamics implements FinanceService, TaskService
{
    protected $legacyInvoiceTask = false;

    public function getUrl()
    {
        return config('client.services.data.dynamics.url');
    }

    public function getClientID()
    {
        return config('client.services.data.dynamics.client_id');
    }

    public function getClientSecret()
    {
        return config('client.services.data.dynamics.client_secret');
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

    public function importCompanies()
    {
        throw new \Exception('Dynamics does not support importing companies.');
    }

    public function syncInvoice(Invoice|int $invoice)
    {


        if (is_int($invoice))
        {
            $invoice = Invoice::findOrFail($invoice);
        }

        if ($this->invoiceIsNotReadyForDynamics($invoice))
        {
            $this->logIssue($invoice);
            abort(400, 'Unable to submit the invoice as its not ready for Dynamics.');
        }

        $this->legacyInvoiceTask = $this->isLegacyInvoiceTask($invoice);


        //Get invoice lines

        // $now = now()->toIso8601ZuluString();
        $now = $invoice->invoice_date?->setTime(12, 0, 0)->toIso8601ZuluString();

        $concatInvoiceLines = [];

        //We will mark what we send and group items against that id we sent.
        //Dynamics can't handle multiple ids, so send one and we will store what was against that one id for later marking that line as sent.
        $markedDynamicsLines = [];

        foreach($invoice->invoiceLines as $line)
        {
            $partialKey = 'account_type_'.$line->account_type_id . '_vat_'. $line->vat;
            // $arrayKey = $partialKey . '_rate_id_'. $line->rate_id;
            // if($this->legacyInvoiceTask) {
                $arrayKey = $partialKey . '_revenue_id_' . $line->revenue_id;
            // }

            if(isset($concatInvoiceLines[$arrayKey]))
            {
                $concatInvoiceLines[$arrayKey]["quantity"] = strval(floatval($concatInvoiceLines[$arrayKey]["quantity"]) + $line->quantity);

                array_push($markedDynamicsLines[$arrayKey]['invoice_line_ids'], $line->id);
                if($line->revenue_id) {
                    array_push($markedDynamicsLines[$arrayKey]['revenue_ids'], $line->revenue_id);
                }
            }

            if(!isset($concatInvoiceLines[$arrayKey]))
            {
                // $rate = $line?->revenue?->rate;
                // if($this->legacyInvoiceTask) {
                    $rate = $this->getLegacyInvoiceRate($line);
                // }

                $markedDynamicsLines[$arrayKey] = [
                    'revenue_ids' => [],
                    'intracompany_revenue_ids' => [],
                    'invoice_line_ids' => [$line->id],
                ];

                if($line->revenue_id) {
                    array_push($markedDynamicsLines[$arrayKey]['revenue_ids'], $line->revenue_id);
                }

                $concatInvoiceLines[$arrayKey] = [
                    "projId" => (string)'',
                    "projCategoryId" => (string)'',
                    "dirPersonFKPartyNumber" => "000000033",
                    "sensesTaskId" => (string)$invoice->task_id,
                    "depoId" => null,
                    "quantity" => (string)$line->quantity,
                    "unit" => $rate?->unit_of_measure,
                    "rateId" => (string)($rate?->id),
                    "projTaxItemGroup" => (string)($line->vat * 100),
                    "description" => $line->title,
                    "projSalesPrice" => $line->amount,
                    "depoChargableCost" => 0,
                    "sensesInvoiceId" => (string)$invoice->id,
                    "sensesInvoiceLineId" => (string)$line->id,
                    "sensesRevenueId" => (string)$line->revenue_id,
                    "sensesInvoiceUUID" => $invoice->uuid,
                    "sensesInvoiceLineUUID" => $line->uuid,
                    "sensesRevenueUUID" => $line->revenue?->uuid,
                    "postingDate" => $now
                ];
            }
        }


        $unslottableIntracompanyRevenue = false;
        $legacyInvoiceTask = $this->legacyInvoiceTask;
        // Can we slot this one in anywhere?
        $invoice->task->intracompanyRevenues()->whereNull('finance_reference')->lazy()->each(function ($revenue) use (&$wasAbleToSlotIn, &$concatInvoiceLines, &$markedDynamicsLines, &$unslottableIntracompanyRevenue)
        {
            if($unslottableIntracompanyRevenue) {
                return; //skip all lines now because we can't slot one in anyway.
            }

            $keys = array_keys($concatInvoiceLines);

            $arrayKey = 'account_type_' . $revenue->account_type_id  . '_vat_'. $revenue->vat;//.'__UnitValue_' . $revenue->amount;

            $foundKey = null;
            foreach($keys as $key) {
                if(str_contains($key, $arrayKey)) {
                    $foundKey = $key;
                }
            }


            if ($foundKey && isset($concatInvoiceLines[$foundKey]))
            {
                $concatInvoiceLines[$foundKey]["depoId"] = (string)$revenue->intracompany_depot_id;
                $concatInvoiceLines[$foundKey]["depoChargableCost"] += abs($revenue->amount);
                array_push($markedDynamicsLines[$foundKey]['intracompany_revenue_ids'], $revenue->id);
            }
            else {
                $unslottableIntracompanyRevenue = true;
            }

        });

        if($unslottableIntracompanyRevenue)
        {
            $this->logIssue($invoice);
            abort(400, 'Unable to submit the invoice as its not ready for Dynamics - unable to link intercompany line. Contact accounts.');
        }


        $concatInvoiceLines = array_values($concatInvoiceLines);

        logger()->channel('dynamics')->info('syncInvoice');
        logger()->channel('dynamics')->info('--> '.$invoice->id);
        logger()->channel('dynamics')->info(print_r($concatInvoiceLines, 1));


        //------------------------------------------------------------------
        //------------------------------------------------------------------
        //------------------------------------------------------------------
        //------------------------------------------------------------------
        //------------------------------------------------------------------
        //------------------------------------------------------------------


        $response = $this->post('/api/v1/serviceOrderLines', $concatInvoiceLines);

        return $this->processReponse($response, $invoice, $concatInvoiceLines, $markedDynamicsLines);
    }

    public function post($url, array $data)
    {
        return $this->fetch('post', $this->getUrl() . $url, $data);
    }


    public function fetch($method, $url, $data)
    {
        logger()->channel('dynamics')->info(
            print_r([
                'method' => $method,
                'url' => $url,
                'request' => json_encode($data),
            ], true)
        );

        $response = Http::withHeaders([
            'client_id' => $this->getClientID(),
            'client_secret' => $this->getClientSecret()
        ])
            ->$method($url, $data);

        $this->handleResponse($response);
        logger()->channel('dynamics')->info(
            print_r([
                'method' => $method,
                'url' => $url,
                'response' => $response->body(),
            ], true)
        );

        return $response;
    }

    public function handleResponse($response)
    {
        $response?->throw();
    }

    public function processReponse($response, $invoice, $concatInvoiceLines, $markedDynamicsLines) {
        $linesCreated = 0;
        $responseBody = $response->json();
        $taskMissing = false;

        if (is_array($responseBody))
        {
            foreach ($responseBody as $result)
            {

                if ($result['statusCode'] != 201 && isset($result['statusMessage']))
                {
                    logger()->channel('dynamics')->error($result['statusMessage']);

                    if(str_contains(strtolower($result['statusMessage']), 'the task with id ' . $invoice->task_id . ' does not exist')) {
                        $taskMissing = true;
                    }
                    continue;
                }

                $lineCreated = false;
                if (isset($result['sensesInvoiceLineId']))
                {
                    $markedLine = $this->findMarkedItem($markedDynamicsLines, 'invoice_line_ids', $result['sensesInvoiceLineId']);
                    if(!$markedLine) {
                        logger()->channel('dynamics')->error('Cannot find marked invoice line for result of ' . $result['sensesInvoiceLineId']);
                        continue;
                    }

                    foreach($markedLine['invoice_line_ids'] as $invoiceLineID) {
                        app(MarkInvoiceLineSentToAccounts::class)->execute($invoiceLineID, $result['transactionId']);
                        $lineCreated = true;
                    }

                    if(isset($markedLine['intracompany_revenue_ids'])) {
                        foreach($markedLine['intracompany_revenue_ids'] as $revenueID) {
                            app(MarkRevenueSentToAccounts::class)->execute($revenueID, $result['transactionId']);
                            $lineCreated = true;
                        }
                    }
                }

                if (isset($result['sensesRevenueId']))
                {
                    $markedLine = $this->findMarkedItem($markedDynamicsLines, 'revenue_ids', $result['sensesRevenueId']);
                    if(!$markedLine) {
                        logger()->channel('dynamics')->error('Cannot find marked revenue for result of ' . $result['sensesRevenueId']);
                        continue;
                    }

                    foreach($markedLine['revenue_ids'] as $revenueID) {
                        app(MarkRevenueSentToAccounts::class)->execute($revenueID, $result['transactionId']);
                        $lineCreated = true;
                    }
                }

                if ($lineCreated)
                {
                    ++$linesCreated;
                }
            }
        }

        if ($linesCreated > 0)
        {
            $financeReference = null;
            if ($linesCreated == count($concatInvoiceLines))
            {
                $financeReference = (string)Str::uuid();
                $invoice = app(MarkInvoiceSentToAccounts::class)->execute($invoice, $financeReference);
            }
        }

        if ($linesCreated < count($concatInvoiceLines))
        {
            if($taskMissing) {
                $this->logMissingTask($invoice);
                return abort(400, 'Unable to submit as Dynamics is missing the task. Please contact IT. Submitted ' . $linesCreated . ' invoice lines.');
            }
            return abort(400, 'Unable to submit all invoice lines. Submitted ' . $linesCreated . ' invoice lines.');
        }

        return $invoice;
    }

    public function findMarkedItem($markedDynamicsLines, $key, $value) {
        return collect($markedDynamicsLines)->first(function($markedLine) use($key, $value){
            return in_array(intval($value), $markedLine[$key]);
        });
    }

    public function invoiceIsNotReadyForDynamics(Invoice $invoice)
    {
        $paymentReference = $invoice?->company?->payment_reference ?? $invoice?->task?->payment_reference;
        return ($paymentReference == null);
    }

    public function logIssue(Invoice $invoice)
    {
        logger()->channel('dynamics_issues')->error("Unable to submit invoice " . $invoice->id . ' for task ' . $invoice->task?->id . ' and company ' . $invoice->task?->company?->id);
    }

    public function logMissingTask(Invoice $invoice)
    {
        logger()->channel('dynamics_missing_tasks')->error("Unable to submit invoice " . $invoice->id . ' for task ' . $invoice->task?->title);
    }

    public function submitTask(Task|int $task)
    {
        //todo
        $data = [
            "agreementId" => $task->service_agreement,
            "projectId" => "000058",
            "customerAccount" => "US-025",
            "description" => "EgTest",
            "serviceAddressDescription" => $task?->venue?->description,
            "serviceAddressStreet" => $task?->venue?->street,
            "serviceAddressZipCode" => $task?->venue?->postcode,
            "serviceAddressCity" => $task?->venue?->city,
            "serviceAddressCounty" => $task?->venue?->county,
            "serviceAddressCountryRegion" => $task?->venue?->country ?? "UK",
            "serviceAddressState" => null,
            "serviceAddressName" => $task?->venue?->name,
            "jobCreationDateTime" => $task->created_at->toIso8601ZuluString(),
            "slaBreakDown" => "48 Hours of Contact",
            "kpiStatus" => "active",
            "kpiCost" => 203,
            "kpiFailReason" => "None",
            "jobStatus" => "Active",
            "jobTypeCode" => "axareact",
            "clientReference" => $task->client_reference,
            "purchaseOrderNumber" => $task->po_number,
            "depoId" => (string)$task->depot_id,
            "depoUser" => $task?->creator?->full_name,
            "memo" => $task->work_instruction,
            "sensesTaskId" => (string)$task->id,
            "sensesURL" => url('/tasks/' . $task->id)
        ];

        $response = $this->post('/api/v1/serviceOrderLines', $data);
    }

    public function isLegacyInvoiceTask(Invoice $invoice) {
        if(is_null($invoice->task->external_reference)) {
            return true;
        }

        return false;
    }

    public function getLegacyInvoiceRate(InvoiceLine $invoiceLine) {
        $rate = null;

        //demo ids for now
        $rateLookup = [
            //demo rates
            // 1 => 2775,
            // 2 => 2776,
            // 5 => 2777,
            // 3 => 2778,
            // 8 => 2779,
            // 4 => 2780,
            // 10 => 2781,

            //live rates
            1 => 4188,
            2 => 4189,
            5 => 4190,
            3 => 4191,
            8 => 4192,
            4 => 4193,
            10 => 4194,
        ];

        if($invoiceLine->account_type_id && isset($rateLookup[$invoiceLine->account_type_id])) {
            $rate = Rate::find($rateLookup[$invoiceLine->account_type_id]);
        }

        return $rate;
    }

    public function syncTaskFile(Task $task, File $file, $notes) {

        $data = [
            'serviceOrderId' => $task->external_reference,
            'fileType' => $file->extension,
            'attachmentDescription' => $notes,
            'notes' => $notes,
            'fileName' => $file->name,
            'attachment' => base64_encode(Storage::disk($file->disk)->get($file->path))
        ];

        $response = $this->post('/api/v1/serviceOrderDocumentAttachment', $data);
    }
}
