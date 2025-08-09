<?php
/** @phpstan-ignore-file */
// Simple current-user endpoint for SPA/clients
// Returns session-based auth status and role information

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\User;

$response = [
    'loggedIn' => false,
    'role' => 'guest',
];

try {
    if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $response['loggedIn'] = true;
        $response['role'] = $_SESSION['role'] ?? 'guest';
        $response['userId'] = $_SESSION['user_id'] ?? null;
        $response['username'] = $_SESSION['username'] ?? null;
        $response['email'] = $_SESSION['email'] ?? null;
        $response['name'] = trim((string)($_SESSION['first_name'] ?? '') . ' ' . (string)($_SESSION['last_name'] ?? ''));
        $response['partner'] = strtolower((string)$response['role']) === 'business';

        // Optionally enrich from DB if needed
        if (!empty($response['userId'])) {
            $userModel = new User();
            $user = $userModel->getById($response['userId']);
            if ($user) {
                $response['status'] = $user['status'] ?? null;
                $response['company'] = $user['company'] ?? ($user['company_name'] ?? null);
            }
        }
    }

    http_response_code(200);
    echo json_encode($response);
    exit;
} catch (\Throwable $e) {
    http_response_code(200);
    echo json_encode($response);
    exit;
}


