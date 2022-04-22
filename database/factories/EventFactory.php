<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            "eventName" => "Resurrection Fest",
            "eventDate" => "2022-07-04",
            "eventLocation" => "Viveiro, Galicia",
            "eventImage" => "https://www.resurrectionfest.es/media/Resurrection-Fest-2022-Poster-1-scaled.jpg",
            "ticketsUrl" => "https://www.resurrectionfest.es/entradas",
            "details" => "Vaya pedazo de festi loco"
            //
        ];
    }
}
