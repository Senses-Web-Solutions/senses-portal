<?php
namespace Clients\base\Support;

use Illuminate\Container\Container;
use Faker\Generator;

class Factory {
    
    protected $faker;

    public function __construct() {
        $this->faker = Container::getInstance()->make(Generator::class);
    }

    public function venueDefinition(array $definition) {
        return $definition;
    }
}