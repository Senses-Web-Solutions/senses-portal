<?php

namespace Database\Factories;

use App\Models\StatusGroup;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusGroupFactory extends Factory
{
    protected $model = StatusGroup::class;

    public function definition(): array
    {
        return [
			'title' => $this->faker->text(10),
        ];
    }
}

//Generated 09-10-2023 12:05:02
