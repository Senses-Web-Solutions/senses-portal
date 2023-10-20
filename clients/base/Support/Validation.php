<?php
namespace Clients\base\Support;

use App\Interfaces\Validation as ValidationInterface;

class Validation implements ValidationInterface
{
    public function serviceRules(array $rules, string $action = null) : array
    {
        return $rules;
    }

    public function serviceMessages(array $messages, string $action = null) : array
    {
        return $messages;
    }

    public function eventRules(array $rules, string $action = null) : array
    {
        return $rules;
    }

    public function eventMessages(array $messages, string $action = null) : array
    {
        return $messages;
    }
}
