<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class Locked implements Rule
{
    protected $model;
    protected $id; 

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $model, string $id = 'id')
    {
        $this->model = $model;
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //https://dev.to/secmohammed/laravel-form-request-tips-tricks-2p12 feed model into request via trait?

        $model = $value;
        if(is_int($value) || is_string($value)) {
            return $this->model::where($this->id, $value)->whereNull('locked_at')->exists();
        }

        if ($model && is_object($model) && isset($model->locked)) {
            return $model->locked;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return Str::headline(app($this->model)?->object) . ' is locked and cannot be updated.';
    }
}
