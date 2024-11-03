<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supreme Student Council Login</title>
    <style> 
        body {
            background-color: #2C3E50;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #34495E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 300px;
        }

        .logo img {
            width: 100px;
            height: auto;
        }

        h2, h3 {
            color: #ECF0F1;
            margin: 10px 0;
        }

        label {
            color: #ECF0F1;
            display: block;
            margin: 10px 0 5px;
        }

        input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
        }

        button {
            background-color: #2980B9;
            color: orange;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: calc(100% - 20px);
        }

        button:hover {
            background-color: orange;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="C:\xampp\htdocs\php\ssc logo.png" alt="Supreme Student Council Logo">
        </div>
        <h2>Supreme Student Council</h2>
        <h3>Isabela State University Santiago Extension Unit</h3>
        <form id="login-form" method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
