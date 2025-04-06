<?php
session_start(); 
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = new mysqli("localhost", "root", "", "user_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    // Secure query to prevent SQL Injection
    $stmt = $conn->prepare("SELECT student_id, firstname, lastname, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            // Store user details in session
            $_SESSION["loggedin"] = true;
            $_SESSION["student_id"] = $row["student_id"];
            $_SESSION["firstname"] = $row["firstname"];
            $_SESSION["lastname"] = $row["lastname"];
            $_SESSION["username"] = $row["username"];

            header("location: home.php");
            exit();
        } else {
            $error_message = "❌ Incorrect password.";
        }
    } else {
        $error_message = "❌ Username not found.";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('cec.jpg') center/cover;
            backdrop-filter: blur(5px);
        }

        .wrapper {
            display: flex;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 900px;
            height: 500px;
        }

        .sidebar {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            width: 40%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.7s ease;
        }

        .sidebar h2 {
            margin-bottom: 10px;
            font-size: 24px;
        }

        .sidebar p {
            font-size: 16px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%; 
            background-color: #ddd;
            margin-bottom: 20px;
            border: 4px solid #ffffff; 
        }

        .form-container {
            width: 60%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.7s ease;
        }

        .form-container h1 {
            color: #2b5876;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #2b5876;
            outline: none;
            box-shadow: 0 0 8px rgba(43, 88, 118, 0.2);
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #2b5876;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #4e4376;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        a {
            display: block;
            margin-top: 15px;
            color: #4e4376;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
            text-align: center;
        }

        a:hover {
            color: #2b5876;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (max-width: 768px) {
    .wrapper {
        flex-direction: column;
        width: 90%;
        height: auto;
    }

    .sidebar {
        width: 100%;
        padding: 30px;
        text-align: center;
    }

    .sidebar img {
        width: 100px;
        height: 100px;
    }

    .form-container {
        width: 100%;
        padding: 30px;
    }

    .form-container h1 {
        font-size: 22px;
    }

    input[type="text"],
    input[type="password"],
    input[type="submit"] {
        font-size: 14px;
        padding: 10px;
    }
}

    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <img src="logo.png" alt="Logo">
        <h2>Welcome Back!</h2>
        <p>Login to access your immersion account!</p>
    </div>

    <div class="form-container">
        <h1>Login</h1>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
            <a href="register.php">Don't have an account? Register</a>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>

</body>
</html>

