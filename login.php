<!-- //login.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="stylesheet.css?v=0.0.1"> <!-- Link to external CSS file for styling -->
</head>

<body style="background-image: url('./images/backgroundImage.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <div class="login-container">

        <div class="login-box">
            <div class="container">     
                <div class="column">
                    <form id="loginForm">
                        <div class="form-group">
                            <label for="email">Username:</label>
                            <input type="text" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <p class="error"></p>

                        <button type="submit">Login</button>
                    </form>
                </div>
                <div class="column">
                    <div class="logo">
                        <img src="./images/bg.jpg" ;> <!-- Replace with your logo's path -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="handleLogin.js?v=0.0.5"></script> <!-- Link to external javascript file which handles user login -->
</body>

</html>