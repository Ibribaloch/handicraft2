<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handicrafts || Signup Form</title>
    <link rel="stylesheet" href="login.css">
  </head>
<body>
<div class="login">
      <div class="login-main">
        <div class="login-form">
        <h2>Signup</h2>
        <form action="signup.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Signup</button>
        </form>
    </div>
    </div>
</div>
<?php

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $hashed_password); 

    if ($stmt->execute()) {
        echo "User registered successfully.";
    } else {
        echo "Error registering user: " . $stmt->error;
    }
    
    // Close the connection after operations are done
    $conn->close();
}
?>

</body>
</html>
