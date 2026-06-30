<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\TicketTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTierFactory extends Factory
{
    protected $model = TicketTier::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'quantity' => $this->faker->numberBetween(10, 500),
            'sales_channels' => null,
            'is_published' => false,
            'is_active' => true,
        ];
    }

    public function onChannels(array $channels): static
    {
        return $this->state(fn () => ['sales_channels' => $channels]);
    }

    public function published(): static
    {
        return $this->state(fn () => ['is_published' => true]);
    }
}
