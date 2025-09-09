<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            "Abia State", "Adamawa State", "Akwa Ibom State", "Anambra State", "Bauchi State",
            "Bayelsa State", "Benue State", "Borno State", "Cross River State", "Delta State",
            "Ebonyi State", "Edo State", "Ekiti State", "Enugu State", "FCT Abuja",
            "Gombe State", "Imo State", "Jigawa State", "Kaduna State", "Kano State",
            "Katsina State", "Kebbi State", "Kogi State", "Kwara State", "Lagos State",
            "Nasarawa State", "Niger State", "Ogun State", "Ondo State", "Osun State",
            "Oyo State", "Plateau State", "Rivers State", "Sokoto State", "Taraba State",
            "Yobe State", "Zamfara State"
        ];

        foreach ($locations as $location) {
            \App\Models\Location::create([
                'name' => $location,
                'is_active' => true,
            ]);
        }
    }
}
