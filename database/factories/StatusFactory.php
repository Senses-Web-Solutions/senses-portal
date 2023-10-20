<?php

namespace Database\Factories;

use App\Models\Status;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
			'title' => $this->faker->text(10),
			'colour' => $this->faker->randomElement(Colour::toArray()),
        ];
    }
}

//Generated 09-10-2023 12:35:29
