<?php
header('Content-Type: application/json'); // Inform the browser that the response is JSON

$response = ['success' => false, 'message' => ''];

// Database connection details (replace with your actual credentials!)
$servername = "localhost"; // Usually 'localhost' if DB is on the same server
$username = "root"; // e.g., 'root' or a specific DB user
$password = ""; // Your database user's password
$dbname = "viyas_sampledb"; // The name of your database (e.g., 'viyas_printers_db')

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response['message'] = "Database connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Get the form data from the POST request
// Using null coalescing operator (??) to safely get values
$customerName = $_POST['Custname'] ?? '';
$customerContact = $_POST['Custcontact'] ?? '';
$productOrdered = $_POST['Product'] ?? '';
$orderDescription = $_POST['Orderdesc'] ?? '';

// Basic validation (you should add more robust validation)
if (empty($customerName) || empty($customerContact) || empty($productOrdered) || empty($orderDescription)) {
    $response['message'] = "All fields are required.";
    echo json_encode($response);
    $conn->close();
    exit();
}

// Prepare SQL statement to prevent SQL Injection (VERY IMPORTANT!)
$stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_contact, product_ordered, order_description) VALUES (?, ?, ?, ?)");

// Check if statement preparation was successful
if ($stmt === false) {
    $response['message'] = "SQL prepare failed: " . $conn->error;
    echo json_encode($response);
    $conn->close();
    exit();
}

// Bind parameters (s = string, i = integer, d = double, b = blob)
// "ssss" means four string parameters
$stmt->bind_param("ssss", $customerName, $customerContact, $productOrdered, $orderDescription);

// Execute the statement
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Order placed successfully!";
} else {
    $response['message'] = "Error placing order: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();

echo json_encode($response); // Send the JSON response back to the client

?>