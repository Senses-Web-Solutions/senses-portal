<?php

namespace App\Support\Services;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Interfaces\Services\FinanceService;
use League\OAuth2\Client\Provider\AbstractProvider;

class NullFinance implements FinanceService
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
        throw new \Exception('A finance service is not available');
    }

    public function syncInvoice(Invoice|int $invoice)
    {
        throw new \Exception('A finance service is not available');
    }
}
