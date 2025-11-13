<?php
session_start();
require_once("db.php");

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
        $stmt2->bind_param("sss", $token, $expire, $email);
        $stmt2->execute();

        $reset_link = "http://localhost/pawnion/control/reset-password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'Email here';
            $mail->Password   = 'email password here'; 
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom("email here", "Library System");
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body    = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";

            $mail->send();
            echo "<script>
                alert('Registration successful!');
                window.location.href = '../index.php';
              </script>";
            
        } catch (Exception $e) {
            echo "❌ Message could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ No account found with this email!";
    }
}
?>