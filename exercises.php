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

// Exercises API
// GET all exercises
// URL: /exercises
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['letter'])) {
    $letter = $conn->real_escape_string($_GET['letter']); // Get the letter from the query string and sanitize it
    $sql = "SELECT * FROM exercises WHERE exercise_letter LIKE '$letter%'";
    $result = $conn->query($sql);
    $exercises = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $exercises[] = $row;
        }
    }
    echo json_encode($exercises);
} else {
    echo json_encode([]);
}




// POST new exercise2
// URL: /exercises
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $data = json_decode(file_get_contents("php://input"), true);
    $exercise_letter = $data['exercise_letter'];
    $exercise_description = $data['exercise_description'];
    $exercise_level = $data['exercise_level'];

    $sql = "INSERT INTO exercises (exercise_letter, exercise_description, exercise_level) VALUES ('$exercise_letter', '$exercise_description', '$exercise_level')";
    if ($conn->query($sql) === TRUE) {
        echo "New exercise created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


// PATCH exercise video
// URL: /exercises/{exercise_id}/video
if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && preg_match('/\/exercises\/(\d+)\/video/', $_SERVER['REQUEST_URI'], $matches)) {
    $exercise_id = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    $exercise_file = $data['exercise_url'];

    // First, delete the old video file if it exists
    $sql_select = "SELECT exercise_url FROM exercises WHERE exercise_id = $exercise_id";
    $result = $conn->query($sql_select);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $old_file_path = $row['exercise_url'];
        if ($old_file_path) {
            unlink($old_file_path);
        }
    }

    // Then, update the exercise details with the new video file
    $sql = "UPDATE exercises SET exercise_url = '$exercise_file' WHERE exercise_id = $exercise_id";
    if ($conn->query($sql) === TRUE) {
        echo "Exercise video updated successfully";
    } else {
        echo "Error updating exercise video: " . $conn->error;
    }
}
$conn->close();

?>