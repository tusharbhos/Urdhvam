<?php
// =================== CONFIG ===================
define("RECIPIENT_NAME", "John Doe"); 
define("RECIPIENT_EMAIL", "youremail@mail.com"); 

// DB Connection (XAMPP/WAMP default)
$servername = "localhost";
$username   = "root";   // default XAMPP user
$password   = "";       // default no password
$dbname     = "contactdb"; // create this DB in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// =================== FORM DATA ===================
$userName    = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : "";
$senderEmail = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : "";
$userPhone   = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : "";
$message     = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : "";

// =================== VALIDATION ===================
if ($userName && $senderEmail && $userPhone && $message) {
    // Save to DB
    $stmt = $conn->prepare("INSERT INTO contact_form (name, email, phone,  message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $userName, $senderEmail, $userPhone, $message);
    $stmt->execute();
    $stmt->close();

    // Send Email
    $recipient = RECIPIENT_NAME . " <" . RECIPIENT_EMAIL . ">";
    $headers   = "From: " . $userName . " <" . $senderEmail . ">\r\n";
    $headers  .= "Reply-To: " . $senderEmail . "\r\n";

    $msgBody = "You have received a new message from your website contact form:\n\n";
    $msgBody .= "Name: $userName\n";
    $msgBody .= "Email: $senderEmail\n";
    $msgBody .= "Phone: $userPhone\n";
    $msgBody .= "Message:\n$message\n";

    $success = mail($recipient, $msgBody, $headers);

    if ($success) {
        header('Location: contact.html?message=Success');
        exit;
    } else {
        header('Location: contact.html?message=EmailFailed');
        exit;
    }
} else {
    header('Location: contact.html?message=MissingFields');
    exit;
}

// Close DB connection
$conn->close();
?>
