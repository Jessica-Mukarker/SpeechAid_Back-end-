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
if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && preg_match('/\/exercises\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $exercise_id = $matches[1];
    $data = $_POST;
    $exercise_letter = $data['exercise_letter'];
    $exercise_description = $data['exercise_description'] ;
    $exercise_level = $data['exercise_level'] ;
    $therapist_id= $data['therapist_id'] ;

    // File handling for video
    if(isset($_FILES['exercise_video'])) {
        $exerciseDir = 'exercises/';
        $fileName = basename($_FILES['exercise_video']['name']);
        $uploadPath = $exerciseDir . $fileName;

        if (move_uploaded_file($_FILES['exercise_video']['tmp_name'], $uploadPath)) {
            // File uploaded successfully, save its path in the database
            $exercise_video = $uploadPath;
        } else {
            echo "Error uploading video file";
            exit();
        }
    }

    // Prepare update query
    $update_fields = [];
    if (isset($exercise_video)) {
        $update_fields[] = "exercise_video = '$exercise_video'";
    }

    $update_query = implode(", ", $update_fields);
    $sql = "UPDATE exercises SET $update_query WHERE exercise_id = $exercise_id";
    if ($conn->query($sql) === TRUE) {
        echo "Exercise details updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}


// POST exercise video
// URL: /exercises/{exercise_id}/video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && preg_match('/\/exercises\/(\d+)\/video/', $_SERVER['REQUEST_URI'], $matches)) {
    $exercise_id = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    $exercise_file = $data['exercise_file'];

   
    if (move_uploaded_file($_FILES['recording_file']['tmp_name'], $uploadPath)) {
        // File uploaded successfully, save its path in the database
        $recording_url = $uploadPath;

        $sql = "UPDATE exercises SET exercise_url = '$exercise_file' WHERE exercise_id = $exercise_id";         if ($conn->query($sql) === TRUE) {
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
