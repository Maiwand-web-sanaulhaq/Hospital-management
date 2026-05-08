<?php
require_once "config.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $sql = "SELECT d.*, dep.Department_Name 
            FROM Doctors d
            LEFT JOIN Department dep ON d.Department_ID = dep.Department_ID";
    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(["error" => "Query failed: " . $conn->error]);
        exit;
    }
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    echo json_encode($doctors);
} 
elseif ($method == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(["error" => "Invalid JSON input"]);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO Doctors (First_Name, Last_Name, Gender, Phone_Number, Address, Specialization, Medical_license_number, Hire_Date, Department_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", 
        $input['firstName'], 
        $input['lastName'], 
        $input['gender'], 
        $input['phone'], 
        $input['address'], 
        $input['specialization'], 
        $input['licenseNo'], 
        $input['hireDate'], 
        $input['departmentId']
    );
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Doctor added successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to add doctor: " . $stmt->error]);
    }
    $stmt->close();
}
elseif ($method == 'PUT') {
    // PUT ډاټا له php://input څخه لوستل کیږي
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['id'])) {
        echo json_encode(["error" => "Invalid input or missing ID"]);
        exit;
    }
    
    $id = $input['id'];
    $stmt = $conn->prepare("UPDATE Doctors SET First_Name=?, Last_Name=?, Gender=?, Phone_Number=?, Address=?, Specialization=?, Medical_license_number=?, Hire_Date=?, Department_ID=? WHERE Doctor_ID=?");
    $stmt->bind_param("ssssssssii", 
        $input['firstName'], 
        $input['lastName'], 
        $input['gender'], 
        $input['phone'], 
        $input['address'], 
        $input['specialization'], 
        $input['licenseNo'], 
        $input['hireDate'], 
        $input['departmentId'],
        $id
    );
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Doctor updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update doctor: " . $stmt->error]);
    }
    $stmt->close();
}
elseif ($method == 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(["error" => "ID required"]);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM Doctors WHERE Doctor_ID=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["message" => "Doctor deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete doctor: " . $stmt->error]);
    }
    $stmt->close();
}
?>