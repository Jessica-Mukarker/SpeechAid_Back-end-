<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "speech_aid_db";

$conn = new mysqli($servername, $username, $password, $database);
$baseURL = "http://localhost/speechaidApi"; 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Therapists API
// GET all therapists
// URL: /therapists
if ($_SERVER['REQUEST_METHOD'] === 'GET' ) {
    $sql = "SELECT * FROM therapists";
    $result = $conn->query($sql);
    $therapists = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $therapists[] = $row;
        }
    }
    echo json_encode($therapists);
}

// POST new therapist
// URL: /therapists
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $data = json_decode(file_get_contents("php://input"), true);
    $therapist_name = $data['therapist_name'];
    $therapist_email = $data['therapist_email'];
    $therapist_password = $data['therapist_password'];
    $therapist_unique_identifier = $data['therapist_unique_identifier'];

    $sql = "INSERT INTO therapists (therapist_name, therapist_email, therapist_password, therapist_unique_identifier) VALUES ('$therapist_name', '$therapist_email', '$therapist_password', '$therapist_unique_identifier')";
    if ($conn->query($sql) === TRUE) {
        echo "New therapist created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// PATCH therapist details
// URL: /therapists/{therapist_id}
if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && preg_match('/\/therapists\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $therapist_id = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    $update_fields = [];
    foreach ($data as $key => $value) {
        $update_fields[] = "$key = '$value'";
    }
    $update_query = implode(", ", $update_fields);
    $sql = "UPDATE therapists SET $update_query WHERE therapist_id = $therapist_id";
    if ($conn->query($sql) === TRUE) {
        echo "Therapist details updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
$conn->close();

?>