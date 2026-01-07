<?php

// Converted request-quote.php script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $message) {
        $to = 'example@example.com';
        $subject = 'Quote Request';
        $body = "Name: $name\nEmail: $email\nMessage: $message";
        $headers = 'From: ' . $email;

        if (mail($to, $subject, $body, $headers)) {
            echo 'Your quote request has been sent successfully.';
        } else {
            echo 'Failed to send your quote request. Please try again later.';
        }
    } else {
        echo 'Please fill in all fields correctly.';
    }
} else {
    echo 'Invalid request method.';
}

?>