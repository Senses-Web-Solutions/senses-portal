<?php

namespace App\Actions\Companies;

use App\Models\Company;
use Spatie\QueueableAction\QueueableAction;

class UpdateCompany
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $company = Company::findOrFail($id);

        $company->fill($data);

        if (!$company->isDirty()) {
            $company->emitUpdated();
        }

        $company->save();

        return $company;
    }
}

//Generated 27-10-2023 10:55:45
