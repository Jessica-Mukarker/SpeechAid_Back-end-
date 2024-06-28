<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "speech_aid_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the signup form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if(isset($data['username']) && isset($data['email']) && isset($data['password']) && isset($data['age']) && isset($data['id']) && isset($data['user_type'])) {
        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];
        $age = $data['age'];
        $id = $data['id'];
        $user_type = $data['user_type']; // 'patient' or 'therapist'
        
        // Check if the email is already registered
        if ($user_type === 'patient') {
            $sql_check_email = "SELECT * FROM patients WHERE patient_email = '$email'";
        } elseif ($user_type === 'therapist') {
            $sql_check_email = "SELECT * FROM therapists WHERE therapist_email = '$email'";
        } else {
            // Invalid user type
            echo json_encode(["error" => "Invalid user type"]);
            exit;
        }
        
        $result_check_email = $conn->query($sql_check_email);
        
        if ($result_check_email->num_rows > 0) {
            // Email already exists
            echo json_encode(["error" => "user Exist"]);
        } else {
            // Insert new user into the database based on user type
            if ($user_type === 'patient') {
                $sql_insert_user = "INSERT INTO patients (patient_email, patient_password, patient_name, patient_unique_identifier, age) VALUES ('$email', '$password', '$username', '$id', '$age')";
            } elseif ($user_type === 'therapist') {
                $sql_insert_user = "INSERT INTO therapists (therapist_name, therapist_email, therapist_password, therapist_unique_identifier) VALUES ('$username', '$email', '$password', '$id')";
            }

            if ($conn->query($sql_insert_user) === TRUE) {
                // User successfully registered
                echo json_encode(["success" => "User registered successfully"]);
            } else {
                // Error inserting user
                echo json_encode(["error" => "Error: " . $conn->error]);
            }
        }
    } else {
        // Invalid request format
        echo json_encode(["error" => "Invalid request format"]);
    }
} else {
    // Invalid request method or content type
    echo json_encode(["error" => "Invalid request"]);
}

$conn->close();
?>
