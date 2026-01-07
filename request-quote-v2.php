<?php
// Define a response array
$response = ["status" => "error", "message" => ""];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $errors = [];

    // Validate name
    if (empty($_POST['name'])) {
        $errors[] = 'Name is required.';
    }

    // Validate email
    if (empty($_POST['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    // Validate message
    if (empty($_POST['message'])) {
        $errors[] = 'Message is required.';
    }

    // If no errors, process the data
    if (empty($errors)) {
        // Example processing: Here you can save the data to a database or send an email
        // $name = $_POST['name'];
        // $email = $_POST['email'];
        // $message = $_POST['message'];

        $response['status'] = 'success';
        $response['message'] = 'Your request has been received.';
    } else {
        $response['message'] = 'Validation errors occurred: ' . implode(' ', $errors);
    }
} else {
    $response['message'] = 'Invalid request method. Please use POST.';
}

// Set the content-type to JSON
header('Content-Type: application/json');

// Print the response in JSON format
echo json_encode($response);
?>