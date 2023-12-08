<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Coaches</title>
    <style>
        body {
            background-color: #f8f9fa; /* Light background color */
            font-family: 'Arial', sans-serif;
            text-align: center;
            margin: 50px;
        }

        h1 {
            color: #343a40; /* Dark gray text color */
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
            background-color: #007bff; /* Blue header background color */
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5; /* Light background color on hover */
        }

        a {
            display: block;
            margin-top: 20px;
            padding: 10px;
            background-color: #28a745; /* Green button background color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #218838; /* Darker green on hover */
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
    // Query to get all coaches
    $sql = "SELECT * FROM COACH";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        echo "<h1>All Coaches</h1>";
        echo "<table border='1'>";
        echo "<tr><th>CoachID</th><th>UserID</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["CoachID"] . "</td><td>" . $row["UserID"] . "</td></tr>";
        }
        echo "</table>";

        // "Back" button
        echo "<a href='welcome.php'>Back to Admin Menu</a>";
    } else {
        echo "No coaches found.";
    }
} else {
    echo "You are not authorized to access this page!";
}

$conn->close();
?>

</body>

</html>
