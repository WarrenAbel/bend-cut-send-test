<?php
// Disable error display for production
ini_set('display_errors', 0);
error_reporting(0);

// Enforce POST-only access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// ==================== Data Collection ====================
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// ==================== Validation ====================
$errors = [];

// Required fields
if (empty($name)) $errors[] = 'Name is required';
if (empty($email)) $errors[] = 'Email is required';
if (empty($message)) $errors[] = 'Message is required';

// Length limits
if (strlen($name) > 100) $errors[] = 'Name too long (max 100 chars)';
if (strlen($email) > 320) $errors[] = 'Email too long (max 320 chars)';
if (strlen($phone) > 20) $errors[] = 'Phone too long (max 20 chars)';
if (strlen($subject) > 200) $errors[] = 'Subject too long (max 200 chars)';
if (strlen($message) > 5000) $errors[] = 'Message too long (max 5000 chars)';

// Email validation
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (!empty($errors)) {
    http_response_code(400);
    exit('Validation error: ' . implode(', ', $errors));
}

// ==================== Sanitize Email for Headers ====================
// Strip CR/LF to prevent header injection
$safe_email = preg_replace('/[\r\n]+/', '', $email);

// ==================== Build Email ====================
$to = 'admin@bendcutsend.net';
$from = 'no-reply@bendcutsend.net';
$emailSubject = 'Question from ' . $name;
if (!empty($subject)) {
    $emailSubject .= ': ' . $subject;
}

$body = "New question received:\n\n";
$body .= "Name: $name\n";
$body .= "Email: $email\n";
if (!empty($phone)) {
    $body .= "Phone: $phone\n";
}
if (!empty($subject)) {
    $body .= "Subject: $subject\n";
}
$body .= "\nMessage:\n$message\n";

// ==================== Send Email ====================
$headers = "From: $from\r\n";
$headers .= "Reply-To: $safe_email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mailSuccess = mail($to, $emailSubject, $body, $headers);

// ==================== Response ====================
if ($mailSuccess) {
    header('Location: /thank-you.html');
    exit();
} else {
    http_response_code(500);
    exit('Failed to send email. Please try again later.');
}
