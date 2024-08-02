<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handicrafts || Login Form</title>
    <link rel="stylesheet" href="login.css">
  </head>
<body>
<div class="login">
    <div class="login-main">
        <div class="login-form">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
          </form>
    </div>
  </body>
  <?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);


    $stmt->execute();


    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id']; 
            $_SESSION['email'] = $email;


            header("Location: index.html");
        } else {

            echo "The password you entered was not valid.";
        }
    } else {

        echo "No user found with that email address.";
    }


    $stmt->close();
}

$conn->close();
?>
</html>
