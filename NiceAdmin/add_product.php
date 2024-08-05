<!DOCTYPE html>
<html>
<head>
  <title>Insert Product Data</title>
  <style>
    form {
      width: 50%;
      margin: 40px auto;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    label {
      display: block;
      margin-bottom: 10px;
    }
    input[type="text"], input[type="number"], input[type="file"] {
      width: 100%;
      height: 40px;
      margin-bottom: 20px;
      padding: 10px;
      border: 1px solid #ccc;
    }
    input[type="submit"] {
      width: 100%;
      height: 40px;
      background-color: #4CAF50;
      color: #fff;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    input[type="submit"]:hover {
      background-color: #3e8e41;
    }
  </style>
</head>
<body>
  <form action="add_product.php" method="post" enctype="multipart/form-data">
    <label for="name">Product Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Product Description:</label>
    <textarea id="description" name="description" required></textarea>

    <label for="price">Product Price:</label>
    <input type="number" id="price" name="price" required>

    <label for="img">Product Image:</label>
    <input type="file" id="img" name="img" required>

    <input type="submit" value="Insert Product">
  </form>
  <?php
// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'handicraft';

// Create a connection to the database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to insert product data into database
function insertProduct($name, $description, $price, $img) {
    global $conn;
    $sql = "INSERT INTO products (name, description, price, img) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $name, $description, $price, $img);
    $stmt->execute();
    $stmt->close();
}

// Get product data from form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $img = $_FILES["img"]["name"];

    // Upload image file
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["img"]["name"]);
    if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
        // Insert product data into database
        insertProduct($name, $description, $price, $img);
        echo "Product inserted successfully!";
    } else {
        echo "Error uploading image file!";
    }
}

// Close the database connection
$conn->close();
?>
</body>
</html>