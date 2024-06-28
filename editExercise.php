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


// POST exercise video
// URL: /exercises/{exercise_id}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercise_id = $_POST["exercise_id"];
     // File handling
     $uploadDir = 'exercises/';
     $fileName = basename($_FILES['exercise_file']['name']);
     $uploadPath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['exercise_file']['tmp_name'], $uploadPath)) {
        // File uploaded successfully, save its path in the database
        $recording_url = $uploadPath;

        $sql = "UPDATE exercises SET exercise_url = '$recording_url' WHERE exercise_id = $exercise_id";
     if ($conn->query($sql) === TRUE) {
            echo "Exercise video updated successfully";
        } else {
            echo "Error updating exercise video: " . $conn->error;
        }
    } else {
        echo "Error uploading file";
    }

}

$conn->close();

?>
