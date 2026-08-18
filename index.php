<?php
/*
 * User Status Manager
 * Web Development Task
 *
 * Technologies:
 * PHP, MySQL, HTML, CSS & JavaScript
 *
 * NOTE:
 * Database credentials are intentionally excluded
 * from this public GitHub repository.
 */

// Database configuration
$host     = "YOUR_DATABASE_HOST";
$username = "YOUR_DATABASE_USERNAME";
$password = "YOUR_DATABASE_PASSWORD";
$database = "YOUR_DATABASE_NAME";

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

// Add user
if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["action"]) &&
    $_POST["action"] === "add") {

    $name = trim($_POST["name"] ?? "");
    $age  = (int)($_POST["age"] ?? 0);

    if ($name !== "" && $age > 0) {
        $stmt = $conn->prepare(
            "INSERT INTO users (name, age, status) VALUES (?, ?, 1)"
        );

        $stmt->bind_param("si", $name, $age);
        $stmt->execute();
        $stmt->close();

        $message = "User added successfully.";
    }
}

// Toggle status
if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["action"]) &&
    $_POST["action"] === "toggle") {

    $id = (int)($_POST["id"] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare(
            "UPDATE users
             SET status = IF(status = 1, 0, 1)
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Get users
$result = $conn->query(
    "SELECT id, name, age, status FROM users ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Status Manager</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #07101f;
            color: #f1f5f9;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            width: min(920px, 92%);
            margin: auto;
            padding: 70px 0;
        }

        h1 {
            margin: 0;
            color: #38bdf8;
            font-size: clamp(42px, 7vw, 72px);
        }

        .subtitle {
            margin-top: 18px;
            color: #94a3b8;
            font-size: 24px;
        }

        .card {
            margin-top: 60px;
            padding: 55px;
            border: 2px solid #1e293b;
            border-radius: 32px;
            background: #0f1929;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 180px;
            gap: 25px;
        }

        input {
            width: 100%;
            padding: 25px 28px;
            border: 2px solid #334155;
            border-radius: 18px;
            outline: none;
            background: #101a2c;
            color: white;
            font-size: 20px;
        }

        .name-input {
            grid-column: 1 / 3;
        }

        button {
            padding: 20px;
            border: 0;
            border-radius: 18px;
            cursor: pointer;
            background: #38bdf8;
            color: #062033;
            font-size: 20px;
            font-weight: bold;
        }

        .message {
            margin-top: 25px;
            color: #22c55e;
            font-size: 22px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 20px;
        }

        th {
            padding: 25px;
            background: #1e293b;
            color: #38bdf8;
        }

        td {
            padding: 25px 15px;
            border-bottom: 2px solid #334155;
        }

        .status-on {
            color: #22c55e;
            font-weight: bold;
        }

        .status-off {
            color: #f87171;
            font-weight: bold;
        }

        .toggle-button {
            padding: 16px 35px;
            background: #22c55e;
            color: #052e1a;
        }

        footer {
            margin-top: 65px;
            text-align: center;
            color: #64748b;
            font-size: 22px;
        }

        @media (max-width: 650px) {
            .container {
                padding-top: 45px;
            }

            .card {
                padding: 28px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .name-input {
                grid-column: auto;
            }

            table {
                font-size: 16px;
            }

            th,
            td {
                padding: 18px 8px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>User Status Manager</h1>

    <div class="subtitle">
        PHP, MySQL, HTML, CSS & JavaScript Project
    </div>

    <div class="card">

        <form method="POST" class="form-grid">

            <input
                type="hidden"
                name="action"
                value="add"
            >

            <input
                class="name-input"
                type="text"
                name="name"
                placeholder="Enter name"
                required
            >

            <input
                type="number"
                name="age"
                placeholder="Enter age"
                min="1"
                required
            >

            <button type="submit">
                Submit
            </button>

        </form>

        <?php if (!empty($message)): ?>

            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

    </div>

    <div class="card">

        <table>

            <thead>
                <tr>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($user = $result->fetch_assoc()): ?>

                    <tr>

                        <td class="<?= $user["status"] ? "status-on" : "status-off" ?>">
                            <?= (int)$user["status"] ?>
                        </td>

                        <td>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="action"
                                    value="toggle"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$user["id"] ?>"
                                >

                                <button
                                    class="toggle-button"
                                    type="submit"
                                >
                                    Toggle
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="2">
                        No users found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <footer>
        Web Development Task
    </footer>

</div>

</body>
</html>

<?php
$conn->close();
?>
