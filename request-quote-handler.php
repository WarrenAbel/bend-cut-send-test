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
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$street = isset($_POST['street']) ? trim($_POST['street']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$building_type = isset($_POST['building_type']) ? trim($_POST['building_type']) : '';
$postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
$material = isset($_POST['material']) ? trim($_POST['material']) : '';
$surface_finish = isset($_POST['surface_finish']) ? trim($_POST['surface_finish']) : '';
$thickness = isset($_POST['thickness']) ? trim($_POST['thickness']) : '';
$comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// ==================== Validation ====================
$errors = [];

// Required fields
if (empty($first_name)) $errors[] = 'First name is required';
if (empty($last_name)) $errors[] = 'Last name is required';
if (empty($email)) $errors[] = 'Email is required';
if (empty($phone)) $errors[] = 'Phone is required';

// Length limits
if (strlen($first_name) > 100) $errors[] = 'First name too long (max 100 chars)';
if (strlen($last_name) > 100) $errors[] = 'Last name too long (max 100 chars)';
if (strlen($email) > 320) $errors[] = 'Email too long (max 320 chars)';
if (strlen($phone) > 20) $errors[] = 'Phone too long (max 20 chars)';
if (strlen($street) > 200) $errors[] = 'Street address too long (max 200 chars)';
if (strlen($city) > 100) $errors[] = 'City too long (max 100 chars)';
if (strlen($postal_code) > 4) $errors[] = 'Postal code too long (max 4 chars)';
if (strlen($comments) > 5000) $errors[] = 'Comments too long (max 5000 chars)';

// Email validation
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// Quantity validation
if ($quantity < 1) $quantity = 1;
if ($quantity > 1000000) $quantity = 1000000;

if (!empty($errors)) {
    http_response_code(400);
    exit('Validation error: ' . implode(', ', $errors));
}

// ==================== Material/Finish Mapping ====================
$materialLabels = [
    'mild_steel' => 'Mild Steel',
    '3cr12' => '3CR12 Stainless Steel',
    '430' => '430 Stainless Steel',
    '304' => '304 Stainless Steel',
    '316' => '316 Stainless Steel',
    'aluminium_1050' => 'Aluminium 1050'
];

$finishLabels = [
    'cold_rolled' => 'Cold Rolled',
    'hot_rolled' => 'Hot Rolled',
    'galvanised' => 'Galvanised',
    '2d' => '2D',
    'no1' => 'No.1',
    'ba' => 'BA',
    '2b' => '2B',
    'pvc_ba' => 'PVC BA',
    '2b_pvc' => '2B PVC',
    'no4_pvc' => 'No.4 PVC',
    'mill' => 'Mill',
    'mill_pvc' => 'Mill PVC'
];

$materialLabel = isset($materialLabels[$material]) ? $materialLabels[$material] : $material;
$finishLabel = isset($finishLabels[$surface_finish]) ? $finishLabels[$surface_finish] : $surface_finish;

// ==================== Sanitize Email for Headers ====================
// Strip CR/LF to prevent header injection
$safe_email = preg_replace('/[\r\n]+/', '', $email);

// ==================== File Upload Handling ====================
$uploadedFile = null;
$uploadedFileName = null;
$uploadedMime = 'application/octet-stream';

if (isset($_FILES['design_file']) && $_FILES['design_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['design_file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        exit('File upload error code: ' . $file['error']);
    }
    
    // Size check (25MB)
    $maxSize = 25 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        exit('File too large. Maximum size is 25MB.');
    }
    
    // Extension whitelist
    $allowedExts = ['dwg', 'dwt', 'dxf', 'dws', 'dwf', 'dwfx', 'dxb', 'pdf', 'stl', 'jpeg', 'jpg', 'png', 'tiff', 'bmp'];
    $originalName = $file['name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowedExts)) {
        http_response_code(400);
        exit('File type not allowed.');
    }
    
    // Determine MIME type
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $uploadedMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    }
    
    // Save to temp directory with safe filename
    $safeFilename = 'upload_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
    $uploadedFile = sys_get_temp_dir() . '/' . $safeFilename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadedFile)) {
        http_response_code(500);
        exit('Failed to save uploaded file.');
    }
    
    $uploadedFileName = $originalName;
}

// ==================== Build Email ====================
$to = 'admin@bendcutsend.net';
$from = 'no-reply@bendcutsend.net';
$subject = 'New Quote Request from ' . $first_name . ' ' . $last_name;

$body = "New quote request received:\n\n";
$body .= "Name: $first_name $last_name\n";
$body .= "Email: $email\n";
$body .= "Phone: $phone\n";
$body .= "Address: $street, $city, $postal_code\n";
$body .= "Building Type: $building_type\n";
$body .= "Material: $materialLabel\n";
$body .= "Surface Finish: $finishLabel\n";
$body .= "Thickness: {$thickness}mm\n";
$body .= "Quantity: $quantity\n";
if (!empty($comments)) {
    $body .= "Comments:\n$comments\n";
}

// ==================== Send Email ====================
$mailSuccess = false;

if ($uploadedFile && file_exists($uploadedFile)) {
    // Send email with attachment
    $boundary = md5(uniqid(time()));
    
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $safe_email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    
    $message = "--$boundary\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $body . "\r\n";
    
    // Attach file
    $fileContent = chunk_split(base64_encode(file_get_contents($uploadedFile)));
    $message .= "--$boundary\r\n";
    $message .= "Content-Type: $uploadedMime; name=\"$uploadedFileName\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"$uploadedFileName\"\r\n\r\n";
    $message .= $fileContent . "\r\n";
    $message .= "--$boundary--";
    
    $mailSuccess = mail($to, $subject, $message, $headers);
    
    // Clean up temp file
    unlink($uploadedFile);
} else {
    // Send plain text email
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $safe_email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $mailSuccess = mail($to, $subject, $body, $headers);
}

// ==================== Response ====================
if ($mailSuccess) {
    header('Location: thank-you.html');
    exit();
} else {
    http_response_code(500);
    exit('Failed to send email. Please try again later.');
}
