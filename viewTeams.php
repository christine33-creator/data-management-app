<!DOCTYPE html>
<html>

<head>
    <title>View Your Teams</title>
        <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        li:hover {
            background-color: #f0f0f0;
        }

        .no-teams {
            color: #888;
            font-style: italic;
        }
    </style>
</head>

<body>
    <?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sportslfc";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION["userid"]) || empty($_SESSION["userid"])) {
        // Redirect to login page if not logged in
        header("Location: index.html");
        exit();
    }

    $userid = $_SESSION["userid"];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT t.name
                            FROM PLAYS_IN p
                            JOIN TEAM t ON p.TeamID = t.TEAMID
                            WHERE p.PlayerID IN (SELECT PlayerID FROM PLAYER WHERE UserID = ?)");

    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h1>Your Teams</h1>";

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row["name"] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "You are not a member of any teams.";
    }

    $stmt->close();
    $conn->close();
    ?>

</body>

</html>
