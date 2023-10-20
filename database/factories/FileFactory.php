<?php

namespace Database\Factories;

use App\Models\File;

use App\Enums\Colour;
use App\Enums\Format;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
			'name' => $this->faker->text(10),
			'stored_name' => $this->faker->text(10),
			'path' => $this->faker->text(10),
			'folder' => $this->faker->text(10),
			'mime_type' => $this->faker->text(10),
			'extension' => $this->faker->text(10),
			'size' => $this->faker->randomDigit,
			'disk' => $this->faker->text(10),
			'preview_path' => $this->faker->text(10),
			'preview_disk' => $this->faker->text(10),
			'print_path' => $this->faker->text(10),
			'print_disk' => $this->faker->text(10),
        ];
    }
}

//Generated 09-10-2023 13:46:51
