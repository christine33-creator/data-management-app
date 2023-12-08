<!DOCTYPE html>
<html>
<head>
    <title>Add Player to Team</title>
        <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            margin-bottom: 10px;
            padding: 8px;
            width: 200px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            font-weight: bold;
            margin-top: 10px;
        }

        .back-button {
            margin-top: 20px;
        }

        .back-button a {
            text-decoration: none;
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #555;
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

// Check if the user is a coach (you may have already done this in your welcome.php)
if ($_SESSION["userType"] == "Coach") {
    $coachID = getCoachID($conn, $_SESSION["Userid"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["playerID"]) && isset($_POST["teamID"])) {
        $playerID = $_POST["playerID"];
        $teamID = $_POST["teamID"];

        // Check if the player is not already in the team
        if (!isPlayerInTeam($conn, $playerID, $teamID)) {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO PLAYS_IN (PlayerID, TeamID) VALUES (?, ?)");
            $stmt->bind_param("ii", $playerID, $teamID);

            if ($stmt->execute()) {
                $successMessage = "Player $playerID added to team $teamID successfully!";
            } else {
                $errorMessage = "Error adding player to the team: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errorMessage = "Player $playerID is already in team $teamID!";
        }
    }

    // Display the form to add a player to a team
    echo "<h1>Add Player to Team</h1>";

    if (isset($successMessage)) {
        echo "<p style='color: green; font-weight: bold;'>$successMessage</p>";
        echo "<form action=\"CoachAddPlayer.php\" method=\"POST\">";
        echo "Player ID: <input type=\"text\" name=\"playerID\" style='width: 50px;' required>";
        echo "Team ID: <input type=\"text\" name=\"teamID\" style='width: 50px;' required>";
        echo "<input type=\"submit\" value=\"Add Player to Team\">";
        echo "</form>";
    } elseif (isset($errorMessage)) {
        echo "<p style='color: red; font-weight: bold;'>$errorMessage</p>";
        echo "<form action=\"CoachAddPlayer.php\" method=\"POST\">";
        echo "Player ID: <input type=\"text\" name=\"playerID\" required>";
        echo "Team ID: <input type=\"text\" name=\"teamID\" required>";
        echo "<input type=\"submit\" value=\"Add Player to Team\">";
        echo "</form>";
    } else {
        // Default layout when the form is not submitted
        echo "<form action=\"CoachAddPlayer.php\" method=\"POST\">";
        echo "Player ID: <input type=\"text\" name=\"playerID\" required>";
        echo "Team ID: <input type=\"text\" name=\"teamID\" required>";
        echo "<input type=\"submit\" value=\"Add Player to Team\">";
        echo "</form>";
        
        // Back button
        echo "<div class='back-button'>";
        echo "<a href='welcome.php'>Back to Menu</a>";
        echo "</div>";
    }
} else {
    echo "You are not authorized to access this page!";
}

$conn->close();

function getCoachID($conn, $userid) {
    // Get CoachID based on UserID
    $stmt = $conn->prepare("SELECT CoachID FROM COACH WHERE UserID = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["CoachID"];
    } else {
        return null;
    }
}

function isPlayerInTeam($conn, $playerID, $teamID) {
    // Check if the player is already in the team
    $stmt = $conn->prepare("SELECT * FROM PLAYS_IN WHERE PlayerID = ? AND TeamID = ?");
    $stmt->bind_param("ii", $playerID, $teamID);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}
?>

</body>
</html>
