<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Member;
use App\Models\Service;
use App\Models\Order;
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
            ['name' => 'Shalwar Kameez', 'description' => 'Custom tailored shalwar kameez with premium stitching', 'price' => 1500, 'estimated_days' => 3, 'is_active' => true],
            ['name' => 'Suit Stitching', 'description' => 'Complete two-piece or three-piece suit stitching', 'price' => 3500, 'estimated_days' => 5, 'is_active' => true],
            ['name' => 'Shirt Stitching', 'description' => 'Formal and casual shirt stitching', 'price' => 800, 'estimated_days' => 2, 'is_active' => true],
            ['name' => 'Trouser', 'description' => 'Custom trouser stitching with perfect fit', 'price' => 600, 'estimated_days' => 2, 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $customerIds = Customer::pluck('id')->toArray();
        $serviceIds = Service::pluck('id')->toArray();
        $statuses = ['pending', 'in_progress', 'completed'];

        for ($i = 0; $i < 10; $i++) {
            $customerId = $customerIds[array_rand($customerIds)];
            $membersForCustomer = Member::where('customer_id', $customerId)->pluck('id')->toArray();
            $memberId = !empty($membersForCustomer) ? $membersForCustomer[array_rand($membersForCustomer)] : null;

            Order::create([
                'customer_id' => $customerId,
                'member_id' => $memberId,
                'service_id' => $serviceIds[array_rand($serviceIds)],
                'price' => rand(500, 8000),
                'status' => $statuses[array_rand($statuses)],
                'order_date' => Carbon::now()->subDays(rand(0, 30)),
                'due_date' => Carbon::now()->addDays(rand(1, 14)),
            ]);
        }
    }
}
