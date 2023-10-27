<?php

namespace Database\Factories;

use App\Models\Company;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
			'title' => $this->faker->text(10),
			'slug' => $this->faker->text(10),
        ];
    }
}

//Generated 27-10-2023 10:55:45
