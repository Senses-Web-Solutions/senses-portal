<?php

namespace App\Observers;

use App\Actions\Companies\GenerateCompanyShowCache;
use App\Models\Company;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Companies\CompanyCreated;
use App\Events\Companies\CompanyDeleted;
use App\Events\Companies\CompanyUpdated;

class CompanyObserver
{
    public function created(Company $company)
    {
        //app(GenerateCompanyShowCache::class)->onQueue('low')->execute($company->id);
        broadcast_safely(new CompanyCreated($company));
    }

    public function updated(Company $company)
    {
        TaggedCache::forgetWithTag($company->cacheKey);
        //app(GenerateCompanyShowCache::class)->onQueue('low')->execute($company->id);
        broadcast_safely(new CompanyUpdated($company));
    }

    public function locked(Company $company)
    {
        TaggedCache::forgetWithTag($company->cacheKey);
        //app(GenerateCompanyShowCache::class)->onQueue('low')->execute($company->id);
        broadcast_safely(new CompanyUpdated($company));
    }

    public function unlocked(Company $company)
    {
        TaggedCache::forgetWithTag($company->cacheKey);
        //app(GenerateCompanyShowCache::class)->onQueue('low')->execute($company->id);
        broadcast_safely(new CompanyUpdated($company));
    }

    public function deleted(Company $company)
    {
        TaggedCache::forgetWithTag($company->cacheKey);
        broadcast_safely(new CompanyDeleted($company));
    }

    public function restored(Company $company)
    {
        TaggedCache::forgetWithTag($company->cacheKey);
        //app(GenerateCompanyShowCache::class)->onQueue('low')->execute($company->id);
        broadcast_safely(new CompanyUpdated($company));
    }

    public function forceDeleted(Company $company)
    {
        TaggedCache::forgetWithTag($company->cacheKey);
        broadcast_safely(new CompanyDeleted($company));
    }
}

//Generated 27-10-2023 10:55:45
