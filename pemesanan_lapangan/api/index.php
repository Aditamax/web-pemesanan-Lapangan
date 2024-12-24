<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../database.php";

// Fungsi respon untuk API
function response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

// Pastikan koneksi database berhasil
if (!$koneksi) {
    response(["error" => "Database connection failed"], 500);
}

// Parsing URL
$url = $_SERVER['REQUEST_URI'];
$parts = explode("/", trim($url, "/"));
$apiIndex = array_search("api", $parts);
if ($apiIndex === false || $parts[$apiIndex + 1] !== "index.php") {
    response(["error" => "Invalid API endpoint"], 404);
}

// Ambil nama tabel dan ID dari URL
$table = $parts[$apiIndex + 2] ?? null;
$id = $parts[$apiIndex + 3] ?? null;

// Pemetaan nama tabel dari URL ke database
$tableMap = [
    "users" => "users",
    "fields" => "fields",
    "bookings" => "bookings",
    "payments" => "payments",
    "schedules" => "schedules"
];

$idColumnMap = [
    "users" => "id",
    "fields" => "id",
    "bookings" => "id",
    "payments" => "id",
    "schedules" => "id"
];

// Validasi tabel
if (!isset($tableMap[$table])) {
    response(["error" => "Table not found", "received_table" => $table, "available_tables" => array_keys($tableMap)], 404);
}
$table = $tableMap[$table];

// Metode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Operasi CRUD berdasarkan metode
switch ($method) {
    case "GET":
        if ($id) {
            $idColumn = $idColumnMap[$table] ?? null;
            if (!$idColumn) response(["error" => "Invalid table ID column"], 500);

            $query = "SELECT * FROM $table WHERE $idColumn = ?";
            $stmt = $koneksi->prepare($query);
            if (!$stmt) response(["error" => "Failed to prepare statement"], 500);
            $stmt->bind_param("i", $id);
        } else {
            $query = "SELECT * FROM $table";
            $stmt = $koneksi->prepare($query);
            if (!$stmt) response(["error" => "Failed to prepare statement"], 500);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        response($data);
        break;

    case "POST":
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) response(["error" => "Invalid input"], 400);

        $fields = implode(",", array_keys($input));
        $placeholders = implode(",", array_fill(0, count($input), "?"));
        $values = array_values($input);
        $types = str_repeat("s", count($values));

        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $koneksi->prepare($query);
        if (!$stmt) response(["error" => "Failed to prepare statement"], 500);
        $stmt->bind_param($types, ...$values);
        if ($stmt->execute()) {
            response(["message" => "Data created", "id" => $stmt->insert_id], 201);
        } else {
            response(["error" => "Failed to create data"], 400);
        }
        break;

    case "PUT":
        if (!$id) response(["error" => "ID is required"], 400);
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) response(["error" => "Invalid input"], 400);

        $fields = implode(" = ?, ", array_keys($input)) . " = ?";
        $values = array_values($input);
        $types = str_repeat("s", count($values));
        $values[] = $id;
        $types .= "i";

        $idColumn = $idColumnMap[$table] ?? null;
        if (!$idColumn) response(["error" => "Invalid table ID column"], 500);

        $query = "UPDATE $table SET $fields WHERE $idColumn = ?";
        $stmt = $koneksi->prepare($query);
        if (!$stmt) response(["error" => "Failed to prepare statement"], 500);
        $stmt->bind_param($types, ...$values);
        if ($stmt->execute()) {
            response(["message" => "Data updated"]);
        } else {
            response(["error" => "Failed to update data"], 400);
        }
        break;

    case "DELETE":
        if (!$id) response(["error" => "ID is required"], 400);

        $idColumn = $idColumnMap[$table] ?? null;
        if (!$idColumn) response(["error" => "Invalid table ID column"], 500);

        $query = "DELETE FROM $table WHERE $idColumn = ?";
        $stmt = $koneksi->prepare($query);
        if (!$stmt) response(["error" => "Failed to prepare statement"], 500);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            response(["message" => "Data deleted"]);
        } else {
            response(["error" => "Failed to delete data"], 400);
        }
        break;

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
