<?php
// Set header untuk memberitahu klien bahwa responnya adalah JSON
header("Content-Type: application/json");

// Dapatkan method HTTP yang digunakan (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Contoh data dummy (simulasi database)
$users = [
    1 => ['id' => 1, 'name' => 'Budi', 'email' => 'budi@example.com'],
    2 => ['id' => 2, 'name' => 'Siti', 'email' => 'siti@example.com']
];

// --- FUNGSI UTAMA BERDASARKAN METHOD HTTP ---

switch ($method) {
    case 'GET':
        // OPERASI READ (GET)
        handle_get($users);
        break;

    case 'POST':
        // OPERASI CREATE (POST)
        handle_post($users);
        break;

    case 'PUT':
        // OPERASI UPDATE (PUT)
        handle_put($users);
        break;

    case 'DELETE':
        // OPERASI DELETE (DELETE)
        handle_delete($users);
        break;

    default:
        // Method tidak diizinkan
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}

// --- DEFINISI FUNGSI ---

function handle_get($users) {
    // Cek apakah ID user diminta dari URL (misal: /index.php?id=1)
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if (isset($users[$id])) {
            echo json_encode(["status" => "success", "data" => $users[$id]]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    } else {
        // GET ALL USERS
        echo json_encode(["status" => "success", "data" => array_values($users)]);
    }
}

function handle_post(&$users) {
    // POST (CREATE)
    // Ambil data JSON dari body request
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['name']) || empty($input['email'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Name and email are required"]);
        return;
    }

    // Simulasi penambahan data baru
    $newId = max(array_keys($users)) + 1;
    $newUser = [
        'id' => $newId,
        'name' => $input['name'],
        'email' => $input['email']
    ];
    $users[$newId] = $newUser; // Tambahkan ke data dummy

    http_response_code(201); // 201 Created
    echo json_encode(["status" => "success", "message" => "User created successfully", "data" => $newUser]);
}

function handle_put(&$users) {
    // PUT (UPDATE)
    // PUT biasanya memerlukan ID dari URL (misal: /index.php?id=1)
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "User ID is required for update"]);
        return;
    }

    $id = (int)$_GET['id'];
    $input = json_decode(file_get_contents('php://input'), true);
