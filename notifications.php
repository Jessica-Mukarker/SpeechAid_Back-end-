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







// Notifications API
// GET all notifications
// URL: /notifications
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/notifications') {
    $sql = "SELECT * FROM notifications";
    $result = $conn->query($sql);
    $notifications = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    echo json_encode($notifications);
}

// POST new notification
// URL: /notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/notifications') {
    $data = json_decode(file_get_contents("php://input"), true);
    $therapist_id = $data['therapist_id'];
    $patient_id = $data['patient_id'];
    $notification_type = $data['notification_type'];
    $notification_message = $data['notification_message'];

    $sql = "INSERT INTO notifications (therapist_id, patient_id, notification_type, notification_message) VALUES ('$therapist_id', '$patient_id', '$notification_type', '$notification_message')";
    if ($conn->query($sql) === TRUE) {
        echo "New notification created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}




$conn->close();

?>
