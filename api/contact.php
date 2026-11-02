<?php
header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, ['error' => 'Method not allowed.']);
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || strlen($name) > 200 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254 || $message === '' || strlen($message) > 10000) {
    respond(400, ['error' => 'Enter a valid name, email address, and message.']);
}

$recipient = 'kabbasidi1@gmail.com';
$headers = [
    'From' => 'Website contact form <no-reply@localhost>',
    'Reply-To' => $email,
    'Content-Type' => 'text/plain; charset=UTF-8',
];
$body = "Name: {$name}\nEmail: {$email}\n\n{$message}";

if (!mail($recipient, 'New website contact message', $body, $headers)) {
    respond(503, ['error' => 'Email delivery is not configured on the server yet.']);
}

respond(200, ['message' => 'Thanks for your message. It has been sent.']);
