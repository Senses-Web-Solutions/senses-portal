<?php

namespace App\Support\Services;

use Carbon\Carbon;
use App\Models\File;
use App\Models\Rate;
use App\Models\Task;
use App\Models\Asset;
use App\Models\Invoice;
use App\Enums\RevenueType;
use App\Models\InvoiceLine;
use Illuminate\Support\Str;
use App\Support\Services\AX;
use Illuminate\Http\Request;
use App\Support\Services\Dynamics;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use App\Interfaces\Services\FinanceService;
use App\Actions\Invoices\MarkInvoiceSentToAccounts;
use League\OAuth2\Client\Provider\AbstractProvider;
use App\Actions\Invoices\MarkInvoiceLineSentToAccounts;

class AXOrDynamics implements FinanceService
{
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
        throw new \Exception('AX/Dynamics does not support importing companies.');
    }

    public function syncInvoice(Invoice|int $invoice)
    {
        if(is_int($invoice)) {
            $invoice = Invoice::findOrFail($invoice);
        }

        // if($invoice->invoice_date->lte(Carbon::parse('2023-08-01 00:00:00'))) {
        // if($invoice->invoice_date->lte(Carbon::parse('2000-01-01 00:00:00'))) {
        //     return app(AX::class)->syncInvoice($invoice);
        // }

        return app(Dynamics::class)->syncInvoice($invoice);
    }

    public function syncTaskFile(Task $task, File $file, $notes) {
        return app(Dynamics::class)->syncTaskFile($task, $file, $notes);
    }
}
