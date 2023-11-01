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
			'hostname' => $this->faker->text(10),
			'ip_address' => $this->faker->text(10),
			'os' => $this->faker->text(10),
			'architecture' => $this->faker->text(10),
			'cpu_cores' => $this->faker->randomDigit,
			'cpu_threads' => $this->faker->randomDigit,
			'distro' => $this->faker->text(10),
			'distro_version' => $this->faker->text(10),
			'kernel' => $this->faker->text(10),
			'kernel_version' => $this->faker->text(10),
        ];
    }
}

//Generated 01-11-2023 11:27:41
