<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Public Works',
                'slug' => 'public-works',
                'description' => 'Handles road maintenance, infrastructure, and public facilities',
                'email' => 'publicworks@fixmystreet.cm',
                'phone' => '+237 222 123 456',
                'is_active' => true,
            ],
            [
                'name' => 'Sanitation',
                'slug' => 'sanitation',
                'description' => 'Manages waste collection, street cleaning, and environmental health',
                'email' => 'sanitation@fixmystreet.cm',
                'phone' => '+237 222 123 457',
                'is_active' => true,
            ],
            [
                'name' => 'Utilities',
                'slug' => 'utilities',
                'description' => 'Oversees water, electricity, and street lighting',
                'email' => 'utilities@fixmystreet.cm',
                'phone' => '+237 222 123 458',
                'is_active' => true,
            ],
            [
                'name' => 'Parks & Recreation',
                'slug' => 'parks-recreation',
                'description' => 'Maintains parks, playgrounds, and recreational facilities',
                'email' => 'parks@fixmystreet.cm',
                'phone' => '+237 222 123 459',
                'is_active' => true,
            ],
            [
                'name' => 'Safety & Security',
                'slug' => 'safety-security',
                'description' => 'Addresses public safety concerns and security issues',
                'email' => 'safety@fixmystreet.cm',
                'phone' => '+237 222 123 460',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
