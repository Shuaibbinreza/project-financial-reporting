<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\District;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 8 Divisions of Bangladesh
        $divisions = [
            'Dhaka' => ['Dhaka', 'Gazipur', 'Narsingdi', 'Tangail', 'Manikganj', 'Madaripur', 'Munshiganj', 'Narayanganj', 'Faridpur', 'Gopalganj'],
            'Chittagong' => ['Chattogram', 'Cox\'s Bazar', 'Comilla', 'Noakhali', 'Feni', 'Brahmanbaria', 'Chandpur', 'Lakshmipur', 'Rangamati', 'Khagrachari', 'Bandarban'],
            'Khulna' => ['Khulna', 'Jessore', 'Satkhira', 'Bagerhat', 'Narail', 'Kushtia', 'Jhenaidah', 'Magura', 'Meherpur'],
            'Barisal' => ['Barishal', 'Barguna', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'],
            'Sylhet' => ['Sylhet', 'Moulvibazar', 'Habiganj', 'Sunamganj'],
            'Rajshahi' => ['Rajshahi', 'Bogra', 'Natore', 'Pabna', 'Naogaon', 'Joypurhat', 'Sirajganj', 'Chapainawabganj'],
            'Rangpur' => ['Rangpur', 'Dinajpur', 'Thakurgaon', 'Panchagarh', 'Lalmonirhat', 'Kurigram', 'Nilphamari', 'Gaibandha'],
            'Mymensingh' => ['Mymensingh', 'Jamalpur', 'Netrokona', 'Sherpur'],
        ];

        foreach ($divisions as $divisionName => $districts) {
            $division = Division::create(['name' => $divisionName]);
            foreach ($districts as $districtName) {
                District::create([
                    'division_id' => $division->id,
                    'name' => $districtName,
                ]);
            }
        }
    }
}
