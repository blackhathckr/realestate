<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Function to safely return JSON response
function sendJsonResponse($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST
    $recipientEmail = 'kartikeyarealty@gmail.com';
    $recipientName = 'Kartikeya Realty';
    
    // Get form data with validation
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Simple validation
    if (empty($name) || empty($phone) || empty($email) || empty($subject) || empty($message)) {
        sendJsonResponse('error', 'All fields are required');
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse('error', 'Invalid email format');
    }

    // Validate recipient email
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse('error', 'Configuration error: Invalid recipient email');
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kartikeyarealty@gmail.com';     
        $mail->Password   = 'your_password_here';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('kartikeyarealty@gmail.com', 'Kartikeya Realty');
        $mail->addAddress($recipientEmail, $recipientName);
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Enquiry";
        
        // Create HTML body
        $htmlBody = "
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Phone:</strong> {$phone}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Subject:</strong> {$subject}</p>
        <p><strong>Message:</strong></p>
        <p>" . nl2br(htmlspecialchars($message)) . "</p>
        ";
        
        // Create plain text version
        $textBody = "New Contact Form Submission\n\n" .
                    "Name: {$name}\n" .
                    "Phone: {$phone}\n" .
                    "Email: {$email}\n" .
                    "Subject: {$subject}\n" .
                    "Message:\n{$message}";
        
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
        sendJsonResponse('success', 'Your message has been sent successfully!');
    } catch (Exception $e) {
        sendJsonResponse('error', 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
} else {
    // Not a POST request
    sendJsonResponse('error', 'Invalid request method');
}