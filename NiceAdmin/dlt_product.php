<?php
// Database connection
include('C:\xampp\htdocs\handicraft2\config.php'); 

// Handle edit product form submission
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Update product in database
    $update_sql = "UPDATE products SET name = '$name', description = '$description', price = '$price' WHERE id = '$id'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Product updated successfully!";
    } else {
        echo "Error updating product: " . $conn->error;
    }
}

// Handle delete product button click
if (isset($_POST['delete_product_id'])) {
    $id = $_POST['delete_product_id'];

    // Delete product from database
    $delete_sql = "DELETE FROM products WHERE id = '$id'";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Product deleted successfully!";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

// Fetch product details for editing
if (isset($_GET['get_product_details'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Handicraft || Administration</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Add your CSS files here -->
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #f4f4f4;
    }

    .container {
      width: 90%;
      max-width: 1200px;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    h1 {
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    table, th, td {
      border: 1px solid #ddd;
    }

    th, td {
      padding: 12px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    #edit-product-form {
      display: none;
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 20px;
      background: #fff;
      border: 1px solid #ccc;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      z-index: 1000;
      width: 300px;
    }

    #edit-product-form label {
      display: block;
      margin-bottom: 8px;
    }

    #edit-product-form input {
      width: calc(100% - 20px);
      margin-bottom: 10px;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    #edit-product-form button {
      margin-top: 10px;
      padding: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    #edit-product-form button[type="submit"] {
      background-color: #4CAF50;
      color: white;
    }

    #edit-product-form button[type="button"] {
      background-color: #f44336;
      color: white;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }
  </style>
</head>

<body>

  <div class="container">
    <h1>Product Management</h1>

    <!-- Search form -->
    <form action="" method="post" style="text-align: center; margin-bottom: 20px;">
      <input type="text" name="search_term" placeholder="Search for products..." style="padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
      <input type="submit" name="search" value="Search" style="padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; background-color: #4CAF50; color: white;">
    </form>

    <!-- Display search results -->
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Display products
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo $row['name']; ?></td>
                  <td><?php echo $row['description']; ?></td>
                  <td><?php echo $row['price']; ?></td>
                  <td>
                    <button onclick="editProduct(<?php echo $row['id']; ?>)">Edit</button> |
                    <button onclick="deleteProduct(<?php echo $row['id']; ?>)">Delete</button>
                  </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
              <td colspan="5">No products found.</td>
            </tr>
            <?php
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Overlay and Edit product form -->
  <div class="overlay" id="overlay"></div>
  <div id="edit-product-form">
    <form id="edit-form" action="" method="post">
      <input type="hidden" id="edit-product-id" name="id">
      <label for="name">Name:</label>
      <input type="text" id="edit-product-name" name="name">
      <label for="description">Description:</label>
      <input type="text" id="edit-product-description" name="description">
      <label for="price">Price:</label>
      <input type="number" id="edit-product-price" name="price">
      <button type="submit" name="edit_product">Save Changes</button>
      <button type="button" onclick="closeEditForm()">Cancel</button>
    </form>
  </div>

  <script>
  function editProduct(id) {
    // Get product details from database using AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "?get_product_details=1&id=" + id, true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        var product = JSON.parse(xhr.responseText);
        if (product.error) {
          alert(product.error);
        } else {
          document.getElementById("edit-product-id").value = product.id;
          document.getElementById("edit-product-name").value = product.name;
          document.getElementById("edit-product-description").value = product.description;
          document.getElementById("edit-product-price").value = product.price;
          document.getElementById("overlay").style.display = "block";
          document.getElementById("edit-product-form").style.display = "block";
        }
      }
    };
    xhr.send();
  }

  function deleteProduct(id) {
    // Confirm deletion
    if (confirm("Are you sure you want to delete this product?")) {
      // Send delete request to server using AJAX
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onload = function() {
        if (xhr.status === 200) {
          alert(xhr.responseText);
          location.reload(); // Reload the page to reflect changes
        }
      };
      xhr.send("delete_product_id=" + id);
    }
  }

  function closeEditForm() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("edit-product-form").style.display = "none";
  }
  </script>

</body>
</html>
