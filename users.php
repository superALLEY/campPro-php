<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CampProDB";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth.php");
    exit();
}
$adminName = $_SESSION['username'];

// Handle Delete Action for Admins
if (isset($_POST['delete_admin'])) {
    $admin_id = $_POST['admin_id'];
    $deleteAdminQuery = "DELETE FROM Admins WHERE id = ?";
    $stmt = $conn->prepare($deleteAdminQuery);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Action for Users
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $deleteUserQuery = "DELETE FROM Users WHERE id = ?";
    $stmt = $conn->prepare($deleteUserQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Admins
$adminQuery = "SELECT * FROM Admins ORDER BY username";
$adminResult = $conn->query($adminQuery);
$admins = [];
while ($row = $adminResult->fetch_assoc()) {
    $admins[] = $row;
}

// Fetch Users
$userQuery = "SELECT * FROM Users ORDER BY username";
$userResult = $conn->query($userQuery);
$users = [];
while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" type="image/jpeg" href="camp-pro-logo.jpg">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Camp Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Icons -->
  
  
  <style>  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: url('contact.jpg') no-repeat center center/cover;

    background-attachment: fixed;
    color: #fff; /* Default text color */
}

/* Navbar Styling */
.navbar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    background: rgba(255, 223, 0, 0.9); /* Yellow background */
    border-bottom: 2px solid rgba(255, 200, 0, 0.7);
    padding: 15px 30px;
    z-index: 10;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
}

.welcome-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.welcome-container .welcome {
    font-size: 18px;
    font-weight: bold;
    color: #000;
}

.welcome-container i {
    font-size: 24px;
    color: #000;
}

.navbar {
    display: flex;
    align-items: center;
    gap: 25px;
}

.navbar a {
    text-decoration: none;
    color: #000;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: color 0.3s ease-in-out;
}

.navbar a:hover {
    color: #ff5722;
}
/* Add Admin Button */
.add-user {
    text-align: center;
    margin: 20px 0;
}

.add-user a {
    display: inline-block;
    background: #ffc107; /* Bright yellow for distinction */
    color: #000; /* Dark text for contrast */
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s, transform 0.2s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.add-user a:hover {
    background: #e0a800; /* Slightly darker yellow */
    transform: scale(1.05); /* Adds a slight zoom effect */
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); /* Enhances shadow on hover */
}


h1, h2 {
    text-align: center;
    margin-top: 100px;
    color: #fff; /* Updated for better contrast */
}

h2 {
    margin-top: 50px;
    color: #ddd; /* Slightly lighter for distinction */
}

/* User/Admin List */
.user-list {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: rgba(0, 0, 0, 0.6); /* Semi-transparent black */
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
}

.user {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3); /* Semi-transparent white */
    color: #fff; /* Ensures text is visible */
}

.user:last-child {
    border-bottom: none;
}

.user-details {
    flex: 1;
    margin-left: 20px;
}

.user-details span {
    display: block;
    font-size: 16px;
    color: #ccc; /* Light gray for subtlety */
}

.user-buttons {
    display: flex;
    gap: 10px;
}

.user-buttons button {
    padding: 8px 15px;
    font-size: 14px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
    color: #fff; /* White text on buttons */
}

.user-buttons .modify {
    background: #007bff;
}

.user-buttons .modify:hover {
    background: #0056b3;
}

.user-buttons .delete {
    background: #dc3545;
}

.user-buttons .delete:hover {
    background: #b02a37;
}

/* Add User Button */
.add-user {
    text-align: center;
    margin: 20px 0;
}

.add-user a {
    display: inline-block;
    background: #28a745;
    color: #fff;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s;
}

.add-user a:hover {
    background: #218838;
}


    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-container">
        <div class="welcome-container">
            <i class="fas fa-user-circle"></i>
            <span class="welcome">Welcome, Admin: <?php echo htmlspecialchars($adminName); ?></span>
        </div>
        <div class="navbar">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="admin.php"><i class="fas fa-box"></i> Products</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
        </div>
    </div>

    <h1>Admin Panel - Users</h1>

    <!-- Add Admin Button -->
    <div class="add-user">
        <a href="add-user.php">Add New User</a>
    </div>

    <h2>Admins</h2>
    <div class="user-list">
        <?php foreach ($admins as $admin): ?>
            <div class="user">
                <div class="user-details">
                    <strong><?php echo htmlspecialchars($admin['username']); ?></strong>
                    <span>Email: <?php echo htmlspecialchars($admin['email']); ?></span>
                </div>
                <div class="user-buttons">
                    <button class="modify" onclick="window.location.href='modify-admin.php?id=<?php echo $admin['id']; ?>'">Modify</button>
                    <form method="POST" action="">
                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                        <button type="submit" class="delete" name="delete_admin">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Users</h2>
    <div class="user-list">
        <?php foreach ($users as $user): ?>
            <div class="user">
                <div class="user-details">
                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                    <span>Email: <?php echo htmlspecialchars($user['email']); ?></span>
                    <span>Role: <?php echo htmlspecialchars($user['role']); ?></span>
                </div>
                <div class="user-buttons">
                    <button class="modify" onclick="window.location.href='modify-user.php?id=<?php echo $user['id']; ?>'">Modify</button>
                    <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="delete" name="delete_user">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>
