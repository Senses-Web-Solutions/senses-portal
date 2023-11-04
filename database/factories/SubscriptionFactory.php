<?php

namespace Database\Factories;

use App\Models\Subscription;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
			'company_id' => $this->faker->numberBetween(1,10),
			'type' => $this->faker->text(10),
			'data' => null,
        ];
    }
}

//Generated 04-11-2023 16:09:38
