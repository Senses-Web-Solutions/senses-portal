<?php

namespace App\Events\Companies;

use App\Models\Company;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CompanyUpdated implements ShouldBroadcastNow
{
    public Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [];
    }
}
