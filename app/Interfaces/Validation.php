<?php
namespace App\Interfaces;

interface Validation
{
    public function serviceRules(array $rules, string $action = null) : array;

    public function serviceMessages(array $messages, string $action = null) : array;

    public function eventRules(array $rules, string $action = null) : array;

    public function eventMessages(array $messages, string $action = null) : array;
}
