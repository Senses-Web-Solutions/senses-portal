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
		$disk_total = $this->faker->numberBetween(500000, 4000000);
		$disk_free = $disk_total - $this->faker->numberBetween(0, $disk_total);
		$disk_used = $disk_total - $disk_free;

		$ram_total = $this->faker->numberBetween(500000, 4000000);
		$ram_free = $ram_total - $this->faker->numberBetween(0, $ram_total);
		$ram_used = $ram_total - $ram_free;

		$swap_total = $this->faker->numberBetween(500000, 4000000);
		$swap_free = $this->faker->numberBetween(0, $swap_total);
		$swap_used = $swap_total - $swap_free;


        return [
			'server_id' => $this->faker->numberBetween(1,10),
			'company_id' => $this->faker->numberBetween(1,10),
			'timestamp' => now()->timestamp,
			'uptime' => now()->timestamp,
			'cpu_use' => $this->faker->numberBetween(0, 100),
			'cpu_us' => $this->faker->numberBetween(0, 100),
			'cpu_sy' => $this->faker->numberBetween(0, 100),
			'cpu_ni' => $this->faker->numberBetween(0, 100),
			'cpu_id' => $this->faker->numberBetween(0, 100),
			'cpu_wa' => $this->faker->numberBetween(0, 100),
			'cpu_hi' => $this->faker->numberBetween(0, 100),
			'cpu_si' => $this->faker->numberBetween(0, 100),
			'cpu_st' => $this->faker->numberBetween(0, 100),
			'load_1' => $this->faker->randomFloat(2, 0, 4),
			'load_5' => $this->faker->randomFloat(2, 0, 4),
			'load_15' => $this->faker->randomFloat(2, 0, 4),
			'ram_total' => $ram_total,
			'ram_free' => $ram_free,
			'ram_buffer' => $this->faker->numberBetween(500000, 4000000),
			'ram_cache' => $this->faker->numberBetween(500000, 4000000),
			'ram_used' => $ram_used,
			'swap_total' => $swap_total,
			'swap_free' => $swap_free,
			'swap_used' => $swap_used,
			'swap_cache' => $this->faker->numberBetween(500000, 4000000),
			'disk_total' => $disk_total,
			'disk_free' => $disk_free,
			'disk_used' => $disk_used,
			'disk_read' => $this->faker->numberBetween(500000, 4000000),
			'disk_write' => $this->faker->numberBetween(500000, 4000000),
        ];
    }
}

//Generated 01-11-2023 11:22:36
