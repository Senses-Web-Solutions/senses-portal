<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Spatie\QueueableAction\QueueableAction;

class CreateCompany
{
    use QueueableAction;

    public function execute(array $data)
    {
        $company = new Company($data);

        $company->save();

        return $company;
    }
}

//Generated 27-10-2023 10:55:45
