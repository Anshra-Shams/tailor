<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Member;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class CleanSeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:cleanup-seeds {--all-customers : Also delete all customers even if custom named}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove seeded dummy customers and deduplicate services on live/local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database cleanup...');

        // 1. Remove Dummy Seeded Customers
        $dummyNames = [
            'Khan Family',
            'Ali Family',
            'Raza Family',
            'Noor House',
            'Ahmed Traders'
        ];

        $dummyQuery = Customer::query();
        if ($this->option('all-customers')) {
            $deletedCount = $dummyQuery->count();
            Member::truncate();
            $dummyQuery->delete();
            $this->info("✓ All {$deletedCount} customers and their members deleted.");
        } else {
            $dummyCustomers = Customer::whereIn('name', $dummyNames)->get();
            $count = $dummyCustomers->count();
            foreach ($dummyCustomers as $cust) {
                // Cascading delete members
                $cust->members()->delete();
                $cust->delete();
            }
            $this->info("✓ Deleted {$count} dummy seeded customer(s) (" . implode(', ', $dummyNames) . ").");
        }

        // 2. Remove Duplicate Services
        $services = Service::all();
        $seen = [];
        $duplicatesRemoved = 0;

        foreach ($services as $service) {
            $normalizedName = trim(strtolower($service->name));
            if (isset($seen[$normalizedName])) {
                // Duplicate found: delete this duplicate
                $service->delete();
                $duplicatesRemoved++;
            } else {
                $seen[$normalizedName] = $service->id;
            }
        }

        $this->info("✓ Removed {$duplicatesRemoved} duplicate service(s). Unique services retained: " . count($seen) . ".");

        $this->newLine();
        $this->info('Cleanup completed successfully!');
        return Command::SUCCESS;
    }
}
