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
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $data = $_POST;
    $exercise_letter = $data['exercise_letter'];
    $exercise_description = $data['exercise_description'];
    $exercise_level = $data['exercise_level'];
    $therapist_id= $data['therapist_id'];

     // File handling
     $exerciseDir = 'exercises/';
     $fileName = basename($_FILES['exercise_file']['name']);
     $uploadPath = $exerciseDir . $fileName;

     if (move_uploaded_file($_FILES['exercise_file']['tmp_name'], $uploadPath)) {
        // File uploaded successfully, save its path in the database
        $exercise_url = $uploadPath;

        $sql = "INSERT INTO exercises (exercise_letter, exercise_description, exercise_level,therapist_id,exercise_url) VALUES ('$exercise_letter', '$exercise_description', '$exercise_level','$therapist_id','$exercise_url')";
        if ($conn->query($sql) === TRUE) {
            echo "New exercise created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        } 

    }else {
    echo "Error uploading file";
    }
}







$conn->close();

?>