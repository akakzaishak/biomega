<?php
// Public endpoint used by delivery_gps.js to poll whether admin forced GPS for a driver.
// Returns JSON: { forced: bool, admin: string|null }

chdir(__DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: application/json; charset=utf-8');

$phone = $_GET['phone'] ?? null;
if (empty($phone)) {
    echo json_encode(['forced' => false]);
    exit;
}

try {
    $row = DB::table('delivery_location')
        ->where('PhoneNumber', $phone)
        ->first();

    if (!$row) {
        echo json_encode(['forced' => false]);
        exit;
    }

    $forced = (int) ($row->GpsForced ?? 0) === 1;
    $admin = $forced ? ($row->ForcedByAdmin ?? null) : null;

    echo json_encode(['forced' => $forced, 'admin' => $admin]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['forced' => false]);
}
