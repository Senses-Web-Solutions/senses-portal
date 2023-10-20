<?php

namespace Database\Factories;

use App\Models\TagGroup;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagGroupFactory extends Factory
{
    protected $model = TagGroup::class;

    public function definition(): array
    {
        return [
			'title' => $this->faker->text(10),
			'slug' => $this->faker->text(10),
        ];
    }
}

//Generated 09-10-2023 10:26:55
