<?php

namespace Database\Factories;

use App\Models\ServerMetric;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerMetricFactory extends Factory
{
    protected $model = ServerMetric::class;

    public function definition(): array
    {
        return [
			'server_id' => $this->faker->numberBetween(1,10),
			'company_id' => $this->faker->numberBetween(1,10),
			'timestamp' => null,
			'uptime' => null,
			'cpu_use' => null,
			'cpu_idle' => null,
			'load_1' => null,
			'load_5' => null,
			'load_15' => null,
			'ram_free' => $this->faker->randomDigit,
			'ram_used' => $this->faker->randomDigit,
			'disk_free' => $this->faker->randomDigit,
			'disk_used' => $this->faker->randomDigit,
			'swap_free' => $this->faker->randomDigit,
			'swap_used' => $this->faker->randomDigit,
        ];
    }
}

//Generated 27-10-2023 10:55:27
