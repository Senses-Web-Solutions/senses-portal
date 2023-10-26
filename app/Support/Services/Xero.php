<?php

namespace App\Support\Services;

use Exception;
use XeroPHP\Webhook;
use App\Models\Status;
use App\Models\Company;
use App\Models\Invoice;
use XeroPHP\Application;
use App\Models\InvoiceLine;
use Illuminate\Http\Request;
use XeroPHP\Models\Accounting\Item;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use XeroPHP\Models\Accounting\Account;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\LineItem;
use App\Actions\Companies\CreateCompany;
use App\Actions\Companies\UpdateCompany;
use App\Actions\Invoices\MarkInvoiceLineSentToAccounts;
use App\Actions\Invoices\MarkInvoiceSentToAccounts;
use XeroPHP\Models\Accounting\Item\Sale;
use App\Interfaces\Services\FinanceService;
use League\OAuth2\Client\Token\AccessToken;
use XeroPHP\Models\Accounting\Organisation;
use XeroPHP\Models\Accounting\BrandingTheme;
use App\Support\Providers\Xero as ProvidersXero;
use League\OAuth2\Client\Provider\AbstractProvider;
use Calcinai\OAuth2\Client\Provider\Xero as ProviderXero;
use XeroPHP\Models\Accounting\Invoice as AccountingInvoice;

class Xero implements FinanceService
{
    private $colours = [
        'red',
        'yellow',
        'green',
        'blue',
        'purple',
        'gray'
    ];

    public function provider(): ProviderXero
    {
        return ProvidersXero::getInstance();
    }

    public function redirectToProvider(): RedirectResponse
    {
        $url = $this->provider()->getAuthorizationUrl([
            'scope' => 'openid email profile offline_access accounting.settings.read accounting.contacts accounting.transactions'
        ]);

        return redirect()->to($url);
    }

    public function handleProviderCallback(Request $request)
    {
        // Obtain an access token from the callback code.
        $accessToken = $this->provider()->getAccessToken('authorization_code', [
            'code' => $request->input('code')
        ]);

        // Retrieve the list of tenants (typically Xero organisations), and let the user select one.
        $tenants = $this->provider()->getTenants($accessToken);
        $selectedTenant = $tenants[0]; // For example purposes, we're pretending the user selected the first tenant.

        $organisation = null;

        try {
            if ($accessToken && $selectedTenant->tenantId) {
                $xero = new Application(
                    $accessToken,
                    $selectedTenant->tenantId
                );
                $query = $xero->load(Organisation::class);

                $organisation = $query->first();
            }
        } catch (\XeroPHP\Remote\Exception\ForbiddenException $exception) {
            return redirect()->to('/connections')->with('reconnect', 'xero');
        }

        // Store the access token and selected tenant ID in the credentials files.
        $this->storeCredentials($accessToken->jsonSerialize(), $selectedTenant->tenantId, getCurrentUser()->id, $organisation->toStringArray());

        return redirect()->to('/connections');
    }

    public function refreshAccessTokenIfNecessary()
    {

        if (empty(credential('xero.access_token'))) {
            throw new \Exception('A connection to Xero has not been setup');
        } else {
            // Before using the access token, check if it has expired and refresh it if necessary.
            $accessToken = new AccessToken(credential('xero.access_token'));

            if ($accessToken->hasExpired()) {
                $newAccessToken = $this->provider()->getAccessToken('refresh_token', [
                    'refresh_token' => $accessToken->getRefreshToken()
                ]);

                credential()->set('xero.access_token', $newAccessToken->jsonSerialize());
            }
        }

    }

    public function authorizeWebhook($xero, $request)
    {
        $xero->setConfig(['webhook' => ['signing_key' => env('XERO_CONTACT_WEBHOOK_KEY')]]);
        $webhook = new Webhook($xero, $request->getContent());

        if (!$webhook->validate($request->headers->get('X-Xero-Signature'))) {
            return abort(401);
        }

        return $webhook;
    }

    public function syncCompany($xeroContact)
    {
        // Try to find company in our db that has a finance_reference == $event->resourceID
        $existingCompany = Company::where('finance_reference', $xeroContact->ContactID)
            ->orWhere('title', $xeroContact->Name)
            ->first();

        $colour = array_rand($this->colours);
        $addresses = $xeroContact->Addresses;
        $streetAddress = null;
        foreach ($addresses as $address) {
            if ($address->AddressType == 'STREET') {
                $streetAddress = $address;
            }
        }

        $phones = $xeroContact->Phones;
        $defaultPhone = null;
        foreach ($phones as $phone) {
            if ($phone->PhoneType == 'DEFAULT') {
                $defaultPhone = $phone;
            }
        }
        $telephone = $defaultPhone->PhoneCountryCode . ' ' . $defaultPhone->PhoneAreaCode . ' ' . $defaultPhone->PhoneNumber;

        $companyData = [
            'title' => $xeroContact->Name,
            'finance_reference' => $xeroContact->ContactID,
            'vat_number' => $xeroContact->TaxNumber,
            'company_number' => $xeroContact->CompanyNumber,
            'email' => $xeroContact->EmailAddress,
            'telephone' => $telephone,
            'colour' => $this->colours[$colour] . '-lighter',
            'text_colour' => $this->colours[$colour] . '-darker',
            // We have to assume that the Xero xeroContact Address has been made in the following format
            // AddressLine1 => name/number
            // AddressLine2 => street
            // AddressLine3 => town
            'name' => $streetAddress->AddressLine1,
            'street' => $streetAddress->AddressLine2,
            'town' => $streetAddress->AddressLine3,
            'city' => $streetAddress->City,
            'county' => $streetAddress->Region,
            'country' => $streetAddress->Country,
            'postcode' => $streetAddress->PostalCode,
            'supplier' => $xeroContact->IsSupplier,
        ];


        if (!$existingCompany) {
            // Create a company on senses
            $colour = array_rand($this->colours);
            $companyData['colour'] = $this->colours[$colour] . '-lighter';
            $companyData['text_colour'] = $this->colours[$colour];
            $newCompany = app(CreateCompany::class)->execute($companyData);
            return $newCompany;
        }

        // Update the existing company on senses
        $updatedCompany = app(UpdateCompany::class)->execute($existingCompany->id, $companyData);
        return $updatedCompany;
    }

    public function importCompanies()
    {
        $this->refreshAccessTokenIfNecessary();

        try {
            $accessToken = new AccessToken(credential('xero.access_token'));
        } catch (\Exception $e) {
            throw new \Exception('Xero access token does not exist.');
        }

        $xero = new Application(
            $accessToken,
            credential('xero.tenant_id')
        );

        $contacts = $xero->load(Contact::class)->execute();


        foreach ($contacts as $index => $contact) {

            $existingCompany = Company::where('finance_reference', $contact->ContactID)
                ->orWhere('title', $contact->Name)
                ->first();

                $addresses = $contact->getAddresses();
                $streetAddress = null;
                foreach ($addresses as $address) {
                    if ($address->AddressType == 'STREET') {
                        $streetAddress = $address;
                    }
                }

                $phones = $contact->getPhones();
                $defaultPhone = null;
                foreach ($phones as $phone) {
                    if ($phone->PhoneType == 'DEFAULT') {
                        $defaultPhone = $phone;
                    }
                }
                $telephone = $defaultPhone->getPhoneCountryCode() . ' ' . $defaultPhone->getPhoneAreaCode() . ' ' . $defaultPhone->getPhoneNumber();

                $colour = array_rand($this->colours);
                $companyData = [
                    'title' => $contact->Name,
                    'finance_reference' => $contact->ContactID,
                    'vat_number' => $contact->TaxNumber,
                    'company_number' => $contact->CompanyNumber,
                    'email' => $contact->EmailAddress,
                    'telephone' => $telephone,
                    'colour' => $this->colours[$colour] . '-lighter',
                    'text_colour' => $this->colours[$colour] . '-darker',
                    // We have to assume that the Xero Contact Address has been made in the following format
                    // AddressLine1 => name/number
                    // AddressLine2 => street
                    // AddressLine3 => town
                    'name' => $streetAddress->AddressLine1,
                    'street' => $streetAddress->AddressLine2,
                    'town' => $streetAddress->AddressLine3,
                    'city' => $streetAddress->City,
                    'county' => $streetAddress->Region,
                    'country' => $streetAddress->Country,
                    'postcode' => $streetAddress->PostalCode,
                    'supplier' => $contact->IsSupplier,
                ];

            if (!$existingCompany) {
                app(CreateCompany::class)->execute($companyData);
            } else {
                app(UpdateCompany::class)->execute($existingCompany->id, $companyData);
            }
        }
    }

    public function syncInvoice(Invoice|int $invoice)
    {

        $this->refreshAccessTokenIfNecessary();

        try {
            $accessToken = new AccessToken(credential('xero.access_token'));
        } catch (\Exception $e) {
            throw new \Exception('Xero access token does not exist.');
        }

        $xero = new Application(
            $accessToken,
            credential('xero.tenant_id')
        );

        if(is_int($invoice)) {
            $invoice = Invoice::findOrFail($invoice);
        }

        $existingXeroInvoice = $xero->load(AccountingInvoice::class)
            ->where('Type', 'ACCREC')
            ->where('Reference', $invoice->reference)
            ->where('BrandingThemeID', '5d4dd402-c851-497e-aae1-9ff265c0d15a')
            ->where('Url', url('/invoices/' . $invoice->id))
            ->where('CurrencyCode', 'GBP')
        ->setParameter('Statuses', 'SUBMITTED,AUTHORISED,PAID')
        ->first();

        if (!$existingXeroInvoice) {
            return $this->createInvoice($xero, $invoice);
        } else {
            return $existingXeroInvoice;
        }
    }

    private function createInvoice($xero, $invoice)
    {
        // Get all invoice lines and start new XERO Invoice (may or may not be saved)
        $invoiceLines = InvoiceLine::where('invoice_id', $invoice->id)->with('accountType', 'revenue', 'revenue.revenueType')->get();
        $xeroInvoice = new AccountingInvoice($xero);

        foreach ($invoiceLines as $invoiceLine) {
            // I'm writing these comments to hopefully help me understand it all when I inevitably come back to it ;)
            // 1 A - C) Account code
            // 1A) If an account type doesn't have a finance code (XERO Account Code) saved towards it, throw an error
            $accountCode = null; // This requires an accountType to have a finance_code
            if (isset($invoiceLine->accountType->finance_code)) {
                $accountCode = $invoiceLine->accountType->finance_code;
            } else {
                abort(404, 'The account type associated with invoice line ' . $invoiceLine->id . ' does not have a finance code');
            }

            $existingXeroAccount = $xero->load(Account::class)
                ->where('Code', $accountCode)
                ->execute();

            // 1B -> 2) If there is an account on XERO, use that
            if (count($existingXeroAccount) > 0) {
                $taxType = $this->findTaxType($invoiceLine->vat);

                $xeroItemCode = null;
                // 2 A - G) Items
                // 2A) If an invoice_line doesn't have a finance_reference, set it
                if (!$invoiceLine->finance_reference) {
                    $exactMatchXeroItem = $xero->load(Item::class)
                        ->where('Code.Contains("' . $invoiceLine->title . '")')
                        ->where(
                            'SalesDetails.UnitPrice',
                            $invoiceLine->amount
                        )
                        ->execute();

                    $xeroItem = null;
                    // 2B) If XERO has an item that matches this invoice_line, use it
                    if (count($exactMatchXeroItem) == 1) {
                        $xeroItem = $exactMatchXeroItem[0];
                    } else if (count($exactMatchXeroItem) > 1) {
                        // 2C) If XERO has multiple items that match this invoice_line, throw an error. XERO shouldn't
                        return abort(500, "There are multiple 'products and services' on XERO that have a Code containing " . $invoiceLine->title . ' and a sale price of ' . $invoiceLine->amount);
                    } else {
                        // 2D -> 3) If XERO doesn't have an item that matches this invoice_line, make one
                        $xeroItem = $this->makeXeroItem($xero, $invoiceLine);
                    }

                    // 2E) Set the XERO Item Code from the newly made XERO Item
                    $xeroItemCode = $xeroItem->getCode();
                    $xeroItemID = $xeroItem->getItemID();

                    // 2F) Save the newly made XERO Item ItemID to Senses's invoice_line finance_reference
                    app(MarkInvoiceLineSentToAccounts::class)->execute($invoiceLine, $xeroItemID);
                } else {
                    // 2G -> 4) If this invoice line has a finance_reference, it means there is an Item on XERO. So fetch the Item's XERO Code
                    $xeroItem = $xero->load(Item::class)
                    ->where('ItemID', $invoiceLine->finance_reference)
                    ->first();

                    $xeroItemCode = $xeroItem->Code;
                }

                // 4 A - B) Line Items
                // 4A) Make a new XERO LineItem with all of the necessary data
                $lineItem = new LineItem($xero);
                $lineItem->setDescription($invoiceLine->title)
                    ->setQuantity($invoiceLine->quantity)
                    ->setUnitAmount($invoiceLine->amount)
                    ->setItemCode($xeroItemCode)
                    ->setAccountCode($invoiceLine->accountType->finance_code)
                    ->setTaxType($taxType)
                    ->setTaxAmount($invoiceLine->vat_total)
                    ->setLineAmount($invoiceLine->total);

                // 4B) Add the LineItem to the XERO Invoice
                $xeroInvoice->addLineItem($lineItem);
            } else {
                // 1C) If an account can't be found on XERO throw an error
                return abort(404, 'An account with a code of ' . $accountCode . ' cannot be found on XERO.');
            }
        }


        // 5 A - D) Invoice
        // 5A) Find the company this invoice will be sent to
        $company = Company::findOrFail($invoice->company_id);
        $xeroContact = $xero->load(Contact::class)->where('ContactID', $company->finance_reference)->first();

        // 5B) Create a XERO Invoice with all of the necessary data and save it to XERO
        $xeroInvoice->setType('ACCREC')
        ->setContact($xeroContact)
        ->setDate($invoice->invoice_date)
        ->setDueDate($invoice->due_date)
            ->setLineAmountType('Exclusive')
            ->setReference($invoice->reference)
            ->setBrandingThemeID('5d4dd402-c851-497e-aae1-9ff265c0d15a')
            ->setUrl(url('/invoices/' . $invoice->id))
            ->setCurrencyCode('GBP')
            ->setStatus('SUBMITTED')
            ->setExpectedPaymentDate($invoice->due_date);

        $xeroInvoice->save();

        app(MarkInvoiceSentToAccounts::class)->execute($invoice, $xeroInvoice->getInvoiceID());

        return $invoice;
    }

    private function findTaxType($vat)
    {
        // The account on XERO should have the correct TaxType. Do I let XERO figure it out based off the account?
        // Or should I pass Senses's TaxType as an override?
        $taxType = 'EXEMPTOUTPUT';
        if ($vat == 0.05) {
            $taxType = 'RROUTPUT';
        } else if ($vat == 0.15) {
            $taxType = 'SROUTPUT';
        } else if ($vat == 0.20) {
            $taxType = 'OUTPUT2';
        }

        return $taxType;
    }

    private function makeXeroItem($xero, $invoiceLine)
    {
        // 3 A - D) Making a XERO item
        // 3A) Check how many XERO items have a Code that contains Senses's invoice_line's title.
        $xeroItems = $xero->load(Item::class)
            ->where('Code.Contains("' . $invoiceLine->title . '")')
            ->last();

        // 3B) If there are none, start at 1. Otherwise get the last XERO item's Code number (eg 'Shift Earning Paid 1 <- this is the number')
        $codeNumber = null;
        if (!$xeroItems) {
            $codeNumber = 1;
        } else {
            $exploded = explode(' ', $xeroItems->Code);
            $codeNumber = intval($exploded[count($exploded) - 1]) + 1;
        }

        $code = $invoiceLine->title . ' ' . $codeNumber;
        $description = $code . ' description';

        // 3C) Make a new XERO Sale to save on the XERO item. (This just contains the unit price)
        $xeroSale = new Sale($xero);
        $xeroSale->setUnitPrice($invoiceLine->amount);

        // 3D -> 4) Make a new XERO Item with all of the necessary data and save it to XERO
        $xeroItem = new Item($xero);
        $xeroItem->setCode($code)
        ->setName($code)
        ->setIsSold(true) // ? Would this ever be false?
            ->setIsPurchased(false) // ? Would this ever be true?
            ->setDescription($description)
            ->setSalesDetails($xeroSale);

        $xeroItem->save();

        return $xeroItem;
    }

    public static function storeCredentials(array $accessToken, string $tenantID, int $authorisedByID, array $organisation)
    {
        credential()->set('xero.access_token', $accessToken);
        credential()->set('xero.tenant_id', $tenantID);
        credential()->set('xero.authorised_by', $authorisedByID);
        credential()->set('xero.organisation', $organisation);
    }

    public static function disconnectXero()
    {
        credential()->delete('xero.access_token');
        credential()->delete('xero.tenant_id');
        credential()->delete('xero.authorised_by');
        credential()->delete('xero.organisation');

        return redirect()->to('/connections');
    }
}
