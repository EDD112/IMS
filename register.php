<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
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
            background: url('cec.jpg') no-repeat center center/cover;
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
            animation: fadeIn 0.7s ease;
        }

        .form-container h1 {
            color: #2b5876;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
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
        }

        input[type="submit"]:hover {
            background-color: #4e4376;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            color: #4e4376;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
            text-align: center;
            width: 100%;
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
    input[type="email"],
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
        <h2>Welcome</h2>
        <p>Registration for an immersion student!</p>
    </div>

    <div class="form-container">
        <h1>Register</h1>
           <form method="post" action="">
               <input type="text" name="student_id" placeholder="Student ID *" required>
               <input type="text" name="firstname" placeholder="First Name *" required>
               <input type="text" name="lastname" placeholder="Last Name *" required>
               <input type="text" name="username" placeholder="Username *" required>
               <input type="password" name="password" placeholder="Password *" required>
               <input type="submit" value="Register">
               <a href="login.php">Already have an account? Login</a>
            </form>
    </div>
</div>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn = new mysqli("localhost", "root", "", "user_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $error_messages = [];

    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($student_id) || !preg_match('/^\d{6,}$/', $student_id)) {
        $error_messages[] = "Student ID must be a valid number with at least 6 digits.";
    }

    if (empty($firstname) || !preg_match("/^[a-zA-Z]+(?: [a-zA-Z]+)*$/", $firstname)) {
        $error_messages[] = "Firstname can only contain letters and spaces.";
    }

    if (empty($lastname) || !preg_match("/^[a-zA-Z]+(?: [a-zA-Z]+)*$/", $lastname)) {
        $error_messages[] = "Lastname can only contain letters and spaces.";
    }

    if (empty($username) || !preg_match('/^(?!.*__)(?![_])[a-zA-Z0-9_]+(?<![_])$/', $username)) {
        $error_messages[] = "Username can only contain letters, numbers, and underscores.";
    }

    if (empty($password) || !preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error_messages[] = "Password must be at least 8 characters with one letter, one number, and one special character.";
    }

    if (!empty($error_messages)) {
        $error_list = implode('\n', $error_messages);
        echo "<script>alert('Errors:\\n" . $error_list . "');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (student_id, firstname, lastname, username, password) VALUES (?, ?, ?, ?, ?)");

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bind_param("sssss", $student_id, $firstname, $lastname, $username, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    $conn->close();
}

?>
