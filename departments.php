<?php
require_once "config.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // ټول څانګې ترلاسه کړئ
    $result = $conn->query("SELECT * FROM Department");
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    echo json_encode($departments);
} 
elseif ($method == 'POST') {
    // نوی څانګه اضافه کړئ
    $input = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("INSERT INTO Department (Department_Name, Phone_Number, Location, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", 
        $input['name'], 
        $input['phone'], 
        $input['location'], 
        $input['status']
    );
    if ($stmt->execute()) {
        echo json_encode(["message" => "Department added successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to add department: " . $stmt->error]);
    }
    $stmt->close();
}
elseif ($method == 'PUT') {
    // څانګه تازه کړئ
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $stmt = $conn->prepare("UPDATE Department SET Department_Name=?, Phone_Number=?, Location=?, status=? WHERE Department_ID=?");
    $stmt->bind_param("ssssi", 
        $input['name'], 
        $input['phone'], 
        $input['location'], 
        $input['status'],
        $id
    );
    if ($stmt->execute()) {
        echo json_encode(["message" => "Department updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update department: " . $stmt->error]);
    }
    $stmt->close();
}
elseif ($method == 'DELETE') {
    // څانګه ړنګ کړئ
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM Department WHERE Department_ID=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Department deleted successfully"]);
        } else {
            echo json_encode(["error" => "Failed to delete department: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID required"]);
    }
}
?>