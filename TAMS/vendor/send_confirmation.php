<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'C:\xampp\htdocs\TAMS-20231014T155931Z-001\TAMS\vendor/autoload.php';


if (isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Database connection
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'msmsdb';

    $conn = new mysqli($hostname, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL statement to fetch the booking details
    $sql = "SELECT * FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Fetch the booking details
        $booking = $result->fetch_assoc();

        // Close the database connection
        $stmt->close();
        $conn->close();

        // Email content
        $to = $booking['email'];
        $subject = 'Booking Confirmation';
        $messageBody = 'Dear ' . $booking['name'] . "\n";
        $messageBody .= 'Thank you for booking with Adventours Nepal. Your booking has been confirmed.' . "\n";
        $messageBody .= 'Your Booking Details:' . "\n";
        // Add other booking details here

        // Create a PHPMailer instance
        $mailer = new PHPMailer(true);

        // Server settings
        $mailer->isSMTP();
        $mailer->Host = 'smtp.mail.me.com';
        $mailer->SMTPAuth = true;
        $mailer->Username = 'choyabun@gmail.com'; // Your email address
        $mailer->Password = 'ecqt jicz acpr wjah'; // Your email password
        $mailer->SMTPSecure = 'tls';
        $mailer->Port = 587;

        // Recipients
        $mailer->setFrom('choyabun@gmail.com', 'Adventours Nepal');
        $mailer->addAddress($to);

        // Content
        $mailer->isHTML(false); // Set to true if your email content is HTML
        $mailer->Subject = $subject;
        $mailer->Body = $messageBody;

        try {
            // Send the email
            if ($mailer->send()) {
                echo 'Email sent successfully.';
            } else {
                throw new Exception('Email could not be sent.');
            }
        } catch (Exception $e) {
            echo 'An error occurred while sending the email: ' . $e->getMessage();
            // You may want to log the error for debugging purposes
            // Example: error_log('Email error: ' . $e->getMessage(), 0);
        }
    } else {
        echo 'Booking not found.';
    }
} else {
    echo 'No booking ID specified.';
}
?>
