<?php
namespace App\Traits;

use App\Actions\Forms\ValidateFormContent;
use Illuminate\Support\Str;

trait FormValidation
{
    public function getFormValidationRules(array $formContent, int $formStructureId = null): array
    {
        if(!$formStructureId) {
            return [];
        }
       $rules = app(ValidateFormContent::class)->execute($formContent, $formStructureId, true);

        return array_combine(array_map(fn($key) => 'content.'.$key, array_keys($rules)), $rules);
    }
}
