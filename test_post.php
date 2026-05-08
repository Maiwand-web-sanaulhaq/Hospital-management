<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($input) {
        echo json_encode([
            "status" => "success",
            "message" => "POST received",
            "data" => $input
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No JSON data",
            "raw" => file_get_contents('php://input')
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Use POST"]);
}
?>