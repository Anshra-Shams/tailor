<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$o = App\Models\Order::find(16);
if ($o) {
    App\Models\Order::where('customer_id', $o->customer_id)
        ->where('order_date', $o->order_date)
        ->update(['created_at' => $o->created_at]);
    echo "Successfully synced Order #16 batch timestamps.\n";
} else {
    echo "Order #16 not found.\n";
}
