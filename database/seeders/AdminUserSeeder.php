<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Member;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tailor.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@tailor.com',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
            ]
        );

        $families = [
            [
                'name' => 'Khan Family',
                'phone' => '0301-1234567',
                'gender' => 'male',
                'address' => '123 Main Street, Lahore',
                'members' => [
                    ['name' => 'Asad Khan', 'gender' => 'male', 'relation' => 'son'],
                    ['name' => 'Sara Khan', 'gender' => 'female', 'relation' => 'daughter'],
                    ['name' => 'Rafiq Khan', 'gender' => 'male', 'relation' => 'brother'],
                ],
            ],
            [
                'name' => 'Ali Family',
                'phone' => '0321-7654321',
                'gender' => 'male',
                'address' => '456 Model Town, Karachi',
                'members' => [
                    ['name' => 'Ayesha Ali', 'gender' => 'female', 'relation' => 'wife'],
                    ['name' => 'Usman Ali', 'gender' => 'male', 'relation' => 'son'],
                ],
            ],
            [
                'name' => 'Raza Family',
                'phone' => '0333-9876543',
                'gender' => 'male',
                'address' => '789 Gulberg, Islamabad',
                'members' => [
                    ['name' => 'Hina Raza', 'gender' => 'female', 'relation' => 'wife'],
                    ['name' => 'Tariq Raza', 'gender' => 'male', 'relation' => 'son'],
                    ['name' => 'Zain Raza', 'gender' => 'male', 'relation' => 'son'],
                    ['name' => 'Mariam Raza', 'gender' => 'female', 'relation' => 'daughter'],
                ],
            ],
            [
                'name' => 'Noor House',
                'phone' => '0345-5432167',
                'gender' => 'female',
                'address' => '321 DHA Phase 5, Lahore',
                'members' => [
                    ['name' => 'Bilal Noor', 'gender' => 'male', 'relation' => 'husband'],
                ],
            ],
            [
                'name' => 'Ahmed Traders',
                'phone' => '0312-6789012',
                'gender' => 'male',
                'address' => '654 Faisal Town, Rawalpindi',
                'members' => [],
            ],
        ];

        foreach ($families as $family) {
            $members = $family['members'];
            unset($family['members']);
            $customer = Customer::create($family);

            foreach ($members as $member) {
                $customer->members()->create($member);
            }
        }

        $services = [
            [
                'name' => 'Shalwar Kameez',
                'description' => 'Custom tailored shalwar kameez with premium stitching',
                'price' => 1500,
                'estimated_days' => 3,
                'is_active' => true,
                'measurement_fields' => [
                    ['key' => 'kurta_length', 'label' => 'Kurta Length', 'required' => true],
                    ['key' => 'chest', 'label' => 'Chest', 'required' => true],
                    ['key' => 'shoulder', 'label' => 'Shoulder', 'required' => true],
                    ['key' => 'sleeve_len', 'label' => 'Sleeve Length', 'required' => true],
                    ['key' => 'neck', 'label' => 'Neck', 'required' => false],
                    ['key' => 'shalwar_len', 'label' => 'Shalwar Length', 'required' => true],
                    ['key' => 'pancha', 'label' => 'Pancha', 'required' => false],
                ],
            ],
            [
                'name' => 'Suit Stitching',
                'description' => 'Complete two-piece or three-piece suit stitching',
                'price' => 3500,
                'estimated_days' => 5,
                'is_active' => true,
                'measurement_fields' => [
                    ['key' => 'chest', 'label' => 'Chest', 'required' => true],
                    ['key' => 'waist', 'label' => 'Waist', 'required' => true],
                    ['key' => 'shoulder', 'label' => 'Shoulder', 'required' => true],
                    ['key' => 'sleeve_len', 'label' => 'Sleeve Length', 'required' => true],
                    ['key' => 'collar', 'label' => 'Collar', 'required' => false],
                    ['key' => 'jacket_len', 'label' => 'Jacket Length', 'required' => true],
                    ['key' => 'trouser_len', 'label' => 'Trouser Length', 'required' => true],
                    ['key' => 'trouser_waist', 'label' => 'Trouser Waist', 'required' => true],
                ],
            ],
            [
                'name' => 'Shirt Stitching',
                'description' => 'Formal and casual shirt stitching',
                'price' => 800,
                'estimated_days' => 2,
                'is_active' => true,
                'measurement_fields' => [
                    ['key' => 'chest', 'label' => 'Chest', 'required' => true],
                    ['key' => 'shoulder', 'label' => 'Shoulder', 'required' => true],
                    ['key' => 'sleeve_len', 'label' => 'Sleeve Length', 'required' => true],
                    ['key' => 'neck', 'label' => 'Neck', 'required' => true],
                    ['key' => 'shirt_len', 'label' => 'Shirt Length', 'required' => true],
                ],
            ],
            [
                'name' => 'Trouser',
                'description' => 'Custom trouser stitching with perfect fit',
                'price' => 600,
                'estimated_days' => 2,
                'is_active' => true,
                'measurement_fields' => [
                    ['key' => 'waist', 'label' => 'Waist', 'required' => true],
                    ['key' => 'length', 'label' => 'Length', 'required' => true],
                    ['key' => 'thigh', 'label' => 'Thigh', 'required' => false],
                    ['key' => 'knee', 'label' => 'Knee', 'required' => false],
                    ['key' => 'pancha', 'label' => 'Pancha', 'required' => true],
                ],
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
