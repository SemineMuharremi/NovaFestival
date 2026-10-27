<?php

session_start();
include "../config/db.php";

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $data = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    if(!isset($data['name']) || !isset($data['email']) || !isset($data['password'])){
        echo json_encode([
            "success" => false,
            "message" => "Name, email and password are required."
        ]);
        exit;
    }

    $name = trim($data['name']);
    $email = trim($data['email']);
    $phone = !empty($data['phone']) ? trim($data['phone']) : NULL;
    $password = $data['password'];

    // Validate inputs
    if(strlen($name) < 2){
        echo json_encode([
            "success" => false,
            "message" => "Name must be at least 2 characters long."
        ]);
        exit;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format."
        ]);
        exit;
    }

    if(strlen($password) < 6){
        echo json_encode([
            "success" => false,
            "message" => "Password must be at least 6 characters long."
        ]);
        exit;
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param('s', $email);
    $checkEmail->execute();
    if($checkEmail->get_result()->num_rows > 0){
        echo json_encode([
            "success" => false,
            "message" => "This email is already registered."
        ]);
        exit;
    }

    // Check if phone number already exists (if phone is provided)
    if($phone){
        $checkPhone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $checkPhone->bind_param('s', $phone);
        $checkPhone->execute();
        if($checkPhone->get_result()->num_rows > 0){
            echo json_encode([
                "success" => false,
                "message" => "This phone number is already registered."
            ]);
            exit;
        }
    }

    // Hash password with bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the user and assign the default 'user' role (role_id = 2) in one transaction
    $conn->begin_transaction();

    try {
        $insertUser = $conn->prepare("
            INSERT INTO users (name, email, password, phone, registered_date)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $insertUser->bind_param('ssss', $name, $email, $hashedPassword, $phone);
        $insertUser->execute();

        $userId = $conn->insert_id;

        $insertRole = $conn->prepare("
            INSERT INTO user_roles (user_id, role_id, assigned_date)
            VALUES (?, 2, NOW())
        ");
        $insertRole->bind_param('i', $userId);
        $insertRole->execute();

        $conn->commit();

        echo json_encode([
            "success" => true,
            "message" => "Account created successfully!"
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "success" => false,
            "message" => "Registration failed. Please try again."
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}

?>
