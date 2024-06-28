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


// Patients API
// GET all patients
// URL: /patients
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // $sql = "SELECT * FROM patients";
    // $result = $conn->query($sql);
    // $patients = [];
    // if ($result->num_rows > 0) {
    //     while ($row = $result->fetch_assoc()) {
    //         $patients[] = $row;
    //     }
    // }
    // echo json_encode($patients);
// }
// Patients API
// GET all patients
// URL: /login
// URL: /login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['email']) && isset($data['password']) && isset($data['type'])) {
        $email = $data['email'];
        $password = $data['password'];
        $type = $data['type'];

        if ($type == 'p') {
            // Query for patients
            $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_email = ? AND patient_password = ?");
            $stmt->bind_param("ss", $email, $password);
        } else {
            // Query for therapists
            $stmt = $conn->prepare("SELECT * FROM therapists WHERE therapist_email = ? AND therapist_password = ?");
            $stmt->bind_param("ss", $email, $password);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            if ($type == 'p') {
                echo json_encode(["who" => "patient", "patient" => $userData]);
            } else {
                echo json_encode(["who" => "therapist", "therapist" => $userData]);
            }
        } else {
            echo json_encode(["error" => "No user found"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
}
        // Check if the user is a patient
//         $sql_patient = "SELECT * FROM patients WHERE patient_email = '$email' AND patient_password = '$password'";
//         $result_patient = $conn->query($sql_patient);
        
//         if ($result_patient === false) {
//             // Handle query error
//             echo json_encode(["error" => "Query error: " . $conn->error]);
//         } elseif ($result_patient->num_rows > 0) {
//             // Login successful for patient
//             echo json_encode(["who" => "patient"]);
//         } else {
//             // Check if the user is a therapist
//             $sql_therapist = "SELECT * FROM therapists WHERE therapist_email = '$email' AND therapist_password = '$password'";
//             $result_therapist = $conn->query($sql_therapist);
            
//             if ($result_therapist === false) {
//                 // Handle query error
//                 echo json_encode(["error" => "Query error: " . $conn->error]);
//             } elseif ($result_therapist->num_rows > 0) {
//                 // Login successful for therapist
//                 echo json_encode(["who" => "therapist"]);
//             } else {
//                 // No login found
//                 echo json_encode(["error" => "No user found"]);
//             }
//         }
//     } else {
//         // Invalid request format
//         echo json_encode(["error" => "Invalid request format"]);
//     }
// } else {
//     // Invalid request method or content type
//     echo json_encode(["error" => "Invalid request"]);
// }
// POST new patient
// URL: /patients
// if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
//     $data = json_decode(file_get_contents("php://input"), true);
//     $therapist_id = $data['therapist_id'];
//     $patient_name = $data['patient_name'];
//     $patient_age = $data['patient_age'];
//     $patient_diagnosis = $data['patient_diagnosis'];

//     $sql = "INSERT INTO patients (therapist_id, patient_name, patient_age, patient_diagnosis) VALUES ('$therapist_id', '$patient_name', '$patient_age', '$patient_diagnosis')";
//     if ($conn->query($sql) === TRUE) {
//         echo "New patient created successfully";
//     } else {
//         echo "Error: " . $sql . "<br>" . $conn->error;
//     }
// }

// PATCH patient details
// URL: /patients/{patient_id}
if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && preg_match('/\/patients\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $patient_id = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    $update_fields = [];
    foreach ($data as $key => $value) {
        $update_fields[] = "$key = '$value'";
    }
    $update_query = implode(", ", $update_fields);
    $sql = "UPDATE patients SET $update_query WHERE patient_id = $patient_id";
    if ($conn->query($sql) === TRUE) {
        echo "Patient details updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// DELETE patient
// URL: /patients/{patient_id}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && preg_match('/\/patients\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $patient_id = $matches[1];
    $sql = "DELETE FROM patients WHERE patient_id = $patient_id";
    if ($conn->query($sql) === TRUE) {
        echo "Patient deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();

?>