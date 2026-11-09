
<?php
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_input = $_POST['email'];

    // 1. Establish database connection
    $host = 'db';
    $db   = 'contact_db';
    $user = 'root';
    $pass = 'secret_pass';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Check if the email exists in the users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email_input]);
        $user_record = $stmt->fetch();

        if ($user_record) {
            // Generate a random reset token
            $token = bin2hex(random_bytes(16));

            // Save token in the database
            $update_stmt = $pdo->prepare(
                "UPDATE users SET reset_token = ? WHERE email = ?"
            );
            $update_stmt->execute([$token, $email_input]);

            // 2. Construct the email
            $to = $email_input;
            $subject = "Password Reset Request - Distributed Demo App";

            $reset_link = "http://localhost:8080/reset-password.php?token=" . $token;

            $email_body = "Hello,\n\nYou requested a password reset. Click the link below to change it:\n\n"
                . $reset_link
                . "\n\nIf you did not request this, please ignore this message.";

            $headers = "From: system-noreply@kabarak.ac.ke\r\n" .
                       "Reply-To: support@kabarak.ac.ke\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            // Connect to Mailpit
            $smtp = fsockopen("mail", 1025, $errno, $errstr, 10);

            if (!$smtp) {
                $error = "Could not connect to Mailpit: " . $errstr;
            } else {
                // Read Mailpit's greeting
                fgets($smtp, 515);

                // Identify our application
                fwrite($smtp, "EHLO localhost\r\n");
                while (($response = fgets($smtp, 515)) !== false) {
                    if (isset($response[3]) && $response[3] === ' ') {
                        break;
                    }
                }

                // Specify sender
                fwrite($smtp, "MAIL FROM:<system-noreply@kabarak.ac.ke>\r\n");
                $response = fgets($smtp, 515);

                // Specify recipient
                fwrite($smtp, "RCPT TO:<" . $to . ">\r\n");
                $response = fgets($smtp, 515);

                // Start email content
                fwrite($smtp, "DATA\r\n");
                $response = fgets($smtp, 515);

                if (substr($response, 0, 3) === "354") {
                    $email_message = $headers . "\r\n";
                    $email_message .= "To: " . $to . "\r\n";
                    $email_message .= "Subject: " . $subject . "\r\n";
                    $email_message .= "Content-Type: text/plain; charset=UTF-8\r\n";
                    $email_message .= "\r\n";
                    $email_message .= $email_body;

                    // Send the email
                    $email_message = preg_replace('/(?m)^\./', '..', $email_message);
                    fwrite($smtp, $email_message . "\r\n.\r\n");

                    $response = fgets($smtp, 515);

                    if (substr($response, 0, 3) === "250") {
                        $message = "Password reset email successfully sent to Mailpit!";
                    } else {
                        $error = "Mailpit rejected the email: " . $response;
                    }
                } else {
                    $error = "Mailpit could not accept the email content: " . $response;
                }

                // Close the connection
                fwrite($smtp, "QUIT\r\n");
                fclose($smtp);
            }

        } else {
            $error = "No user account found matching that email address.";
        }

    } catch (\PDOException $e) {
        $error = "Database link error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Distributed System Demo - Forgot Password</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 340px;
        }

        h2 {
            margin-top: 0;
            color: #333;
            text-align: center;
        }

        p {
            color: #666;
            font-size: 14px;
            text-align: center;
            line-height: 1.4;
        }

        .form-group { margin-bottom: 15px; }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-weight: bold;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #0056b3;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover { background-color: #004085; }

        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .links {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .links a {
            color: #0056b3;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Task A: Reset Password</h2>

    <p>
        Enter your registration email address below to send
        an asynchronous message update request.
    </p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="forgot-password.php" method="POST">
        <div class="form-group">
            <label>Your Account Email</label>
            <input
                type="email"
                name="email"
                placeholder="e.g., admin@kabarak.ac.ke"
                required
            >
        </div>

        <button type="submit">Send Reset Message</button>
    </form>

    <div class="links">
        <a href="index.php">← Back to Login Screen</a>
    </div>
</div>

</body>
</html>
