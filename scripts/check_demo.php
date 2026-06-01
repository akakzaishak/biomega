<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$phones = [
    '0710000001',
    '0710000002',
    '0710000005',
    '0710000006'
];

foreach ($phones as $phone) {
    $tables = ['admin','pharmacy','deliveryperson','stockemployee','commercialservice','deliverymanager'];
    $found = false;
    foreach ($tables as $t) {
        $row = DB::table($t)->where('PhoneNumber', $phone)->first();
        if ($row) {
            echo "Found in: $t\n";
            print_r($row);
            if (isset($row->Password)) {
                $plain = match($phone) {
                    '0710000001' => 'admin123',
                    '0710000002' => 'pharmacy123',
                    '0710000005' => 'driver123',
                    '0710000006' => 'stock123',
                    default => null,
                };
                if ($plain !== null) {
                    echo 'Hash check: ' . (Hash::check($plain, $row->Password) ? 'OK' : 'FAIL') . "\n";
                }
            }
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "No row found for phone: $phone\n";
    }
    echo "---\n";
}
 