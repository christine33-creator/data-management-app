<!DOCTYPE html>
<html>
<head>
    <title>Leave Team</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        form {
            max-width: 400px;
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            width: 100%;
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

        p {
            text-align: center;
            margin-top: 15px;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
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

// Check if the user is a coach
if ($_SESSION["userType"] == "Coach") {
    $coachID = getCoachID($conn, $_SESSION["Userid"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["teamID"])) {
        $teamID = $_POST["teamID"];

        // Check if the coach is associated with the team
        if (isCoachInTeam($conn, $coachID, $teamID)) {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("DELETE FROM COACHES WHERE CoachID = ? AND TeamID = ?");
            $stmt->bind_param("ii", $coachID, $teamID);

            if ($stmt->execute()) {
                $successMessage = "Successfully left the team!";
            } else {
                $errorMessage = "Error leaving the team: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errorMessage = "You are not associated with the team $teamID!";
        }
    }

    // Display the form to leave a team
    echo "<h1>Leave Team</h1>";

    if (isset($successMessage)) {
        echo "<p style='color: green; font-weight: bold;'>$successMessage</p>";
    } elseif (isset($errorMessage)) {
        echo "<p style='color: red; font-weight: bold;'>$errorMessage</p>";
    }

    // Display the form to leave a team
    echo "<form action=\"CoachLeaveTeam.php\" method=\"POST\">";
    echo "Team ID: <input type=\"text\" name=\"teamID\" required>";
    echo "<input type=\"submit\" value=\"Leave Team\">";
    echo "</form>";
    
    // Back button
    echo "<div class='back-button'>";
    echo "<a href='welcome.php'>Back to Menu</a>";
    echo "</div>";

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

function isCoachInTeam($conn, $coachID, $teamID) {
    // Check if the coach is associated with the team
    $stmt = $conn->prepare("SELECT * FROM COACHES WHERE CoachID = ? AND TeamID = ?");
    $stmt->bind_param("ii", $coachID, $teamID);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}
?>

</body>
</html>
