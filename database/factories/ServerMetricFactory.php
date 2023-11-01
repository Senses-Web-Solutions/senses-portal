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
			'timestamp' => $this->faker->numberBetween(1,50),
			'uptime' => $this->faker->numberBetween(1,50),
			'cpu_use' => null,
			'cpu_us' => null,
			'cpu_sy' => null,
			'cpu_ni' => null,
			'cpu_id' => null,
			'cpu_wa' => null,
			'cpu_hi' => null,
			'cpu_si' => null,
			'cpu_st' => null,
			'load_1' => null,
			'load_5' => null,
			'load_15' => null,
			'ram_total' => null,
			'ram_free' => null,
			'ram_buffer' => null,
			'ram_cache' => null,
			'ram_used' => null,
			'swap_total' => null,
			'swap_free' => null,
			'swap_used' => null,
			'swap_cache' => null,
			'disk_total' => $this->faker->numberBetween(1,50),
			'disk_free' => $this->faker->numberBetween(1,50),
			'disk_used' => $this->faker->numberBetween(1,50),
			'disk_read' => $this->faker->numberBetween(1,50),
			'disk_write' => $this->faker->numberBetween(1,50),
        ];
    }
}

//Generated 01-11-2023 11:22:36
