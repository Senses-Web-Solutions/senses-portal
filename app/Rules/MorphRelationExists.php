<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Contracts\Validation\Rule as RuleContract;

class MorphRelationExists implements RuleContract
{
    protected $typeField;
    protected $type;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->typeField = Str::replaceLast('_id', '_type', $attribute);

        $this->type = request()->input($this->typeField);
        if(!$this->type) {
            return false; //rely on other validation
        } else if ($this->type == 'default'){ //hijacked for dashboards, shouldn't affect other models!
            return true;
        }

        if(!in_array($this->type, array_keys(Relation::morphMap()))) {
            return false;
        }

        $column = 'id';
        if(!is_numeric($value)) {
            $column = 'uuid';
        }

        $validator = Validator::make(request()->all(), [
            $attribute => [
                Rule::exists(Relation::getMorphedModel($this->type), $column)
            ]
        ]);

        return $validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = 'The selected :attribute is invalid';
        if($this->typeField) {
            $message .= ' for the field ' . $this->typeField;
        }
        else {
            $message .= ' as there was no type specified';
        }
        return $message. '.';
    }
}
