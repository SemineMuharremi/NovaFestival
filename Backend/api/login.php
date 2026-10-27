<?php

session_start();
include "../config/db.php";

header('Content-Type: application/json');

if(isset($_POST['email']) && isset($_POST['password'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT
            users.id,
            users.name,
            users.email,
            users.password,
            roles.role_name
        FROM users
        INNER JOIN user_roles ON users.id = user_roles.user_id
        INNER JOIN roles ON roles.id = user_roles.role_id
        WHERE users.email = ?
    ");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if($user){

        if(password_verify($password, $user['password'])){

            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = strtolower($user['role_name']);

            if($_SESSION['role'] == 'admin'){

                echo json_encode([
                    "success" => true,
                    "redirect" => "admin.php"
                ]);

            } else {

                echo json_encode([
                    "success" => true,
                    "redirect" => "profile.php"
                ]);

            }

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Invalid email or password."
            ]);

        }

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password."
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);

}
?>