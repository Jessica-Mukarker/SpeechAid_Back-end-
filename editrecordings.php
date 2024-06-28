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

// Recordings API
// GET all recordings
// URL: /recordings
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $patientId = isset($_GET['recordingid']) ? $_GET['patientid'] : null;

    if ($patientId) {
        $stmt = $conn->prepare("SELECT * FROM recordings WHERE patient_id = ?");
        $stmt->bind_param("i", $patientId);
    } else {
        $stmt = $conn->prepare("SELECT * FROM recordings");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $recordings = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recordings[] = $row;
        }
    }
    echo json_encode($recordings);
    
    $stmt->close();
}
// POST new recording
// URL: /recordings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $therapist_id = $data['therapist_id'];
    $patient_id = $data['patient_id'];
    $exercise_id = $data['exercise_id'];
    $is_patient_url = $data['is_patient_url'];

    // File handling
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['recording_file']['name']);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['recording_file']['tmp_name'], $uploadPath)) {
        // File uploaded successfully, save its path in the database
        $recording_url = $uploadPath;

        $sql = "INSERT INTO recordings (therapist_id, patient_id, exercise_id, recording_url,is_patient_url) VALUES ('$therapist_id', '$patient_id', '$exercise_id', '$recording_url', '$is_patient_url')";
        if ($conn->query($sql) === TRUE) {
            echo "New recording created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading file";
    }
}
$conn->close();
