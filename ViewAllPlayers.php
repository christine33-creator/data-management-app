<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Players</title>
    <style>
        body {
            background-color: #f5f5f5; /* Light background color */
            font-family: 'Arial', sans-serif;
            text-align: center;
            margin: 50px;
        }

        h1 {
            color: #333; /* Dark gray text color */
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff; /* White background for the table */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Light box shadow */
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd; /* Bottom border for table cells */
        }

        th {
            background-color: #4CAF50; /* Green header background color */
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5; /* Light background color on hover */
        }

        a {
            display: block;
            margin-top: 20px;
            padding: 10px;
            background-color: #3498db; /* Blue button background color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }
    </style>
</head>

<body>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sportslfc";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is an admin (you may have already done this in your welcome.php)
session_start();
if ($_SESSION["userType"] == "Admin") {
    // Query to get all players
    $sql = "SELECT * FROM PLAYER";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        echo "<h1>All Players</h1>";
        echo "<table border='1'>";
        echo "<tr><th>PlayerID</th><th>UserID</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["PlayerID"] . "</td><td>" . $row["UserID"] . "</td></tr>";
        }
        echo "</table>";

        // "Back" button
        echo "<a href='welcome.php'>Back to Main Menu</a>";
    } else {
        echo "No players found.";
    }
} else {
    echo "You are not authorized to access this page!";
}

$conn->close();
?>

</body>

</html>
