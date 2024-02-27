<?php
session_start(); // Start the session

// Database Connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User Authentication
function registerUser($username, $email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function loginUser($username, $password) {
    global $conn;

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) { // Verify password
            // Password is correct, set session variables or perform any other actions needed
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            return true;
        }
    }
    return false;
}

function logoutUser() {
    // Unset all session variables
    session_unset(); 
    // Destroy the session 
    session_destroy(); 
}

// Product Management
function addProduct($name, $description, $price, $quantity) {
    global $conn;
    
    $sql = "INSERT INTO products (name, description, price, quantity) VALUES ('$name', '$description', $price, $quantity)";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function updateProduct($id, $name, $description, $price, $quantity) {
    global $conn;
    
    $sql = "UPDATE products SET name='$name', description='$description', price=$price, quantity=$quantity WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function deleteProduct($id) {
    global $conn;
    
    $sql = "DELETE FROM products WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function getAllProducts() {
    global $conn;
    
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    
    $products = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Shopping Cart
function addToCart($product_id, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // If the product is already in the cart, update its quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCartQuantity($product_id, $quantity) {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function clearCart() {
    unset($_SESSION['cart']);
}

function getTotalItemsInCart() {
    if (isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    } else {
        return 0;
    }
}

function getCartContents() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
}

// Close Database Connection
$conn->close();
?>


