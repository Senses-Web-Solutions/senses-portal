<?php

namespace Database\Factories;

use App\Enums\Colour;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $titles = ['Issue', 'Problem', 'On Hold', 'Active', 'Critical', 'HR', 'Draft', 'Submitted', 'Complete'];

        return [
            'title' => $this->faker->randomElement($titles),
            'colour' => $this->faker->randomElement(Colour::toArray()),
        ];
    }
}
