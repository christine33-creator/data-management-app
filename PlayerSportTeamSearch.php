<!DOCTYPE html>
<html>
<head>
    <title>Search for a Team</title>
        <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4caf50;
            color: white;
        }

        p {
            color: #333;
            margin-top: 20px;
        }

        form {
            max-width: 400px;
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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

    session_start();

    if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1) {
        // Only allow access to players
        echo "You don't have permission to access this page.";
        $conn->close();
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sport = $_POST["sport"];
        $teamName = $_POST["teamName"];

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM TEAM WHERE sname = ? AND name LIKE ?");
        $teamName = "%$teamName%"; // Modify the value before binding
        $stmt->bind_param("ss", $sport, $teamName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>Search Results</h2>";
            echo "<table border='1'>";
            echo "<tr><th>Team ID</th><th>Sport</th><th>Name</th><th>Sex</th><th>Min Age</th><th>Max Age</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["TEAMID"] . "</td>";
                echo "<td>" . $row["sname"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["sex"] . "</td>";
                echo "<td>" . $row["Min_age"] . "</td>";
                echo "<td>" . $row["Max_age"] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No teams found with the given criteria.</p>";
        }

        $stmt->close();
        
        // Back button
        echo "<div class='back-button'>";
        echo "<a href='welcome.php'>Back to Menu</a>";
        echo "</div>";
    }
    ?>

    <h2>Search for a Team</h2>
    <form action="PlayerSportTeamSearch.php" method="POST">
        Sport: <input type="text" name="sport">
        Team Name: <input type="text" name="teamName">
        <input type="submit" value="Search">
    </form>
</body>
</html>
