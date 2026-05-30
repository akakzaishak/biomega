<?php
// Public endpoint used by delivery_gps.js to submit GPS coordinates.
// Accepts JSON POST with: phone, password, lat, lng, status (0|1), clear_force (optional boolean)

chdir(__DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$phone = $data['phone'] ?? null;
$password = $data['password'] ?? null;
$lat = isset($data['lat']) ? (float) $data['lat'] : null;
$lng = isset($data['lng']) ? (float) $data['lng'] : null;
$status = isset($data['status']) ? (int) $data['status'] : 0;
$clearForce = !empty($data['clear_force']);

if (empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit;
}

try {
    // Basic auth check against deliveryperson table. Passwords in this project may be plaintext.
    $dp = DB::table('deliveryperson')
        ->where('PhoneNumber', $phone)
        ->where('Password', $password)
        ->first();

    if (!$dp) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }

    $now = date('Y-m-d H:i:s');

    // Upsert delivery_location
    $exists = DB::table('delivery_location')->where('PhoneNumber', $phone)->exists();
    $payload = [
        'PhoneNumber' => $phone,
        'Latitude' => $lat ?? 0,
        'Longitude' => $lng ?? 0,
        'Status' => $status,
        'UpdatedAt' => $now,
    ];

    if ($clearForce) {
        $payload['GpsForced'] = 0;
        $payload['ForcedAt'] = null;
        $payload['ForcedByAdmin'] = null;
    }

    if ($exists) {
        DB::table('delivery_location')->where('PhoneNumber', $phone)->update($payload);
    } else {
        DB::table('delivery_location')->insert($payload);
    }

    // Insert into history if lat/lng provided
    if ($lat !== null && $lng !== null) {
        DB::table('delivery_location_history')->insert([
            'PhoneNumber' => $phone,
            'Latitude' => $lat,
            'Longitude' => $lng,
            'UpdatedAt' => $now,
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    Log::error('update_location.php error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false]);
}
