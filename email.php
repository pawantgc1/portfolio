<?php
/**
 * send-mail.php
 * Handles the portfolio contact form and emails submissions to Pawan.
 * Requires a PHP-enabled host (this will NOT work on static hosts like GitHub Pages).
 */

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// ---- Destination ----
$to = 'pawanboura9696@gmail.com';

// ---- Collect & trim input ----
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// ---- Basic validation ----
if ($name === '' || $email === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// ---- Simple honeypot spam check (optional field, ignored if empty) ----
if (!empty($_POST['website'] ?? '')) {
    // Likely a bot filling hidden fields — pretend success, send nothing.
    echo json_encode(['success' => true, 'message' => 'Thanks! Your message has been sent.']);
    exit;
}

// ---- Strip header-injection characters from user-controlled header values ----
$safeName  = str_replace(["\r", "\n"], '', $name);
$safeEmail = str_replace(["\r", "\n"], '', $email);

// ---- Build the email ----
$subject = "New portfolio contact from {$safeName}";

$body  = "You received a new message from your portfolio contact form.\n\n";
$body .= "Name:  {$safeName}\n";
$body .= "Email: {$safeEmail}\n\n";
$body .= "Message:\n{$message}\n";

$host = $_SERVER['SERVER_NAME'] ?? 'yourdomain.com';
$headers  = "From: no-reply@{$host}\r\n";
$headers .= "Reply-To: {$safeEmail}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ---- Send ----
$sent = @mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Thanks! Your message has been sent.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sorry, something went wrong sending your message. Please email pawanboura9696@gmail.com directly instead.']);
}