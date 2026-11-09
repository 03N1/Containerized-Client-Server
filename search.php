<?php
session_start();

// Only logged-in users can search
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$results = [];
$error = "";
$search_performed = false;
$registration_number = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $registration_number = trim($_POST["registration_number"] ?? "");
    $search_performed = true;

    if ($registration_number === "") {
        $error = "Please enter a registration number.";
    } else {
        try {
            $host = "db";
            $db = "contact_db";
            $user = "root";
            $pass = "secret_pass";
            $charset = "utf8mb4";

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $stmt = $pdo->prepare(
                "SELECT id, registration_number, mobile_number,
                        email, address, created_at
                 FROM contacts
                 WHERE registration_number = ?"
            );

            $stmt->execute([$registration_number]);
            $results = $stmt->fetchAll();

        } catch (PDOException $e) {
            $error = "Database search error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Contacts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }

        input, button {
            padding: 10px;
            margin: 8px 0;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #007bff;
            color: white;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Search Contact Details</h2>

    <form method="POST">
        <label>Registration Number:</label><br>

        <input
            type="text"
            name="registration_number"
            value="<?php echo htmlspecialchars($registration_number, ENT_QUOTES, 'UTF-8'); ?>"
            required
        >

        <button type="submit">Search Service</button>
    </form>

    <?php if ($error): ?>
        <p class="error">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php endif; ?>

    <?php if ($search_performed && !$error && empty($results)): ?>
        <p>No contact found with that registration number.</p>
    <?php endif; ?>

    <?php if (!empty($results)): ?>

        <h3>Search Results</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Registration Number</th>
                <th>Mobile Number</th>
                <th>Email</th>
                <th>Address</th>
                <th>Date Created</th>
            </tr>

            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['registration_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php endif; ?>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
    <p><a href="logout.php">Logout</a></p>

</div>
</body>
</html>
