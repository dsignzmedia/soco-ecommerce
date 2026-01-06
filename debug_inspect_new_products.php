<?php
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $ids = [161, 162, 163];
    $products = \App\Models\Admin\Master\ProductMapping::whereIn('id', $ids)->get();

    $output = "DEBUG_OUTPUT_START\n";
    foreach ($products as $p) {
        $output .= "ID: {$p->id} | Name: {$p->product_name} | Type: {$p->product_type} | SchoolID: " . json_encode($p->school_id) . " | Status: " . json_encode($p->status) . " | Gender: " . json_encode($p->gender) . " | Stock: {$p->inventory_stock}\n";
    }
    $output .= "DEBUG_OUTPUT_END";
    
    file_put_contents('debug_output.txt', $output);
    echo "Done writing to debug_output.txt";
} catch (\Throwable $e) {
    file_put_contents('debug_output.txt', "ERROR: " . $e->getMessage());
}
