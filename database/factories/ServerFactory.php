<?php

namespace Database\Factories;

use App\Models\Server;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition(): array
    {
        return [
			'company_id' => $this->faker->numberBetween(1,10),
			'title' => $this->faker->text(10),
			'slug' => $this->faker->text(10),
			'ip' => $this->faker->text(10),
			'os' => $this->faker->text(10),
			'priority' => $this->faker->randomDigit,
        ];
    }
}

//Generated 27-10-2023 10:53:42
