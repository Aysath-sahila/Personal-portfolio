<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

$receiving_email_address = 'aysathsahila@gmail.com';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
  http_response_code(400);
  exit('Please fill in all required fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  exit('Please enter a valid email address.');
}

$safe_name = preg_replace("/[\r\n]+/", ' ', $name);
$safe_email = filter_var($email, FILTER_SANITIZE_EMAIL);
$safe_subject = preg_replace("/[\r\n]+/", ' ', $subject);

$mail_subject = 'Portfolio Contact: ' . $safe_subject;
$mail_body = "You received a new message from your portfolio contact form.\n\n"
  . "Name: {$safe_name}\n"
  . "Email: {$safe_email}\n"
  . "Subject: {$safe_subject}\n\n"
  . "Message:\n{$message}\n";

$headers = [
  'MIME-Version: 1.0',
  'Content-Type: text/plain; charset=UTF-8',
  'From: Portfolio Contact <no-reply@localhost>',
  'Reply-To: ' . $safe_email,
  'X-Mailer: PHP/' . phpversion(),
];

$sent = mail($receiving_email_address, $mail_subject, $mail_body, implode("\r\n", $headers));

if (!$sent) {
  http_response_code(500);
  exit('Message could not be sent. Make sure your site is hosted on a PHP server with mail support.');
}

exit('OK');
