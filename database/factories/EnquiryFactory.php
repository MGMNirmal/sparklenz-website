<?php

namespace Database\Factories;

use App\Enums\EnquiryStatus;
use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => '+94 77 123 4567',
            'service_interest' => 'Video editing',
            'message' => fake()->paragraph(),
            'status' => EnquiryStatus::New,
        ];
    }
}
