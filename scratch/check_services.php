<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Service::all() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Price: {$s->price} | Tiers: " . json_encode($s->pricing_tiers) . "\n";
}
