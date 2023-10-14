<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'C:\xampp\htdocs\TAMS-20231014T155931Z-001\TAMS\vendor/autoload.php';

if (isset($_GET['id'])) {
    $bookingId = $_GET['id']; // Remove the quotes to keep it as an integer

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
    $sql = "SELECT * FROM tblappointment WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId); // Use $bookingId here as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Fetch the booking details into an associative array
        $booking = $result->fetch_assoc();

        // Close the database connection
        $stmt->close();
        $conn->close();

        // Email content
        $to = $booking['Email'];
        $subject = 'Booking Confirmation';
        $messageBody = 'Dear ' . $booking['Name'] . "\n";
        $messageBody .= 'Your appointment has been successfully booked.' . "\n\n";
        $messageBody .= 'Your Booking Details:' . "\n";
        $messageBody .= 'Appointment Number: ' . $booking['AptNumber'] . "\n";
        $messageBody .= 'Appointment Date: ' . $booking['AptDate'] . "\n";
        $messageBody .= 'Appointment Time: ' . $booking['AptTime'] . "\n";
        $messageBody .= 'Services: ' . $booking['Services'] . "\n";
        $messageBody .= 'Remark: ' . $booking['Remark'] . "\n";
        $messageBody .= 'Status: ' . $booking['Status'] . "\n\n";

        // Add other booking details here

        // Create a PHPMailer instance
        $mailer = new PHPMailer(true);

        // Server settings
        $mailer->isSMTP();
        $mailer->Host = 'smtp.gmail.com';
        $mailer->SMTPAuth = true;
        $mailer->Username = 'choyabun@gmail.com'; // Your email address
        $mailer->Password = 'ecqt jicz acpr wjah'; // Your email password
        $mailer->SMTPSecure = 'tls';
        $mailer->Port = 587;

        // Recipients
        $mailer->setFrom('choyabun@gmail.com', 'Arts Tattoo Shop');
        $mailer->addAddress($to);

        // Content
        $mailer->isHTML(false); // Set to true if your email content is HTML
        $mailer->Subject = $subject;
        $mailer->Body = $messageBody;

        try {
            // Send the email
            if ($mailer->send()) {
                echo '<script>alert("Email sent successfully.");</script>';
            } else {
                throw new Exception('Email could not be sent.');
            }
        } catch (Exception $e) {
            echo '<script>alert("An error occurred while sending the email: ' . $e->getMessage() . '");</script>';
        }
    } else {
        echo 'Booking not found.';
    }
} else {
    echo 'No booking ID specified.';
}
?>
