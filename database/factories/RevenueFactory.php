<?php

namespace Database\Factories;

use App\Models\Revenue;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class RevenueFactory extends Factory
{
    protected $model = Revenue::class;

    public function definition(): array
    {
        return [
			'company_id' => $this->faker->numberBetween(1,10),
			'revenue_date' => $this->faker->dateTimeThisMonth->format(Format::DATETIME->value),
			'reference' => $this->faker->text(10),
			'description' => $this->faker->text(100),
			'amount' => $this->faker->randomFloat(2,1,6),
			'quantity' => $this->faker->randomFloat(2,1,6),
			'vat' => 0,
			'sub_total' => $this->faker->randomFloat(2,1,6),
			'vat_total' => $this->faker->randomFloat(2,1,6),
			'total' => $this->faker->randomFloat(2,1,6),
        ];
    }
}

//Generated 04-11-2023 16:09:26
