<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Spatie\QueueableAction\QueueableAction;

class DeleteCompany
{
    use QueueableAction;

    public function execute(int $id)
    {
        $company = Company::findOrFail($id);

        $company->delete();

        return $company;
    }
}

//Generated 27-10-2023 10:55:45
