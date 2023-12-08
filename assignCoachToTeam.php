<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Coach to Team</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            margin: 5px 0;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .back-button {
            margin-top: 10px;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            background-color: #ddd;
            border-radius: 4px;
        }

        .back-button a:hover {
            background-color: #ccc;
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

    // Check if the user is an admin (you may have already done this in your welcome.php)
    if ($_SESSION["userType"] == "Admin") {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["coachID"]) && isset($_POST["teamID"])) {
            $coachID = $_POST["coachID"];
            $teamID = $_POST["teamID"];

            // Check if the team already has a head coach
            if (isset($_POST["isHeadCoach"]) && isTeamHeadCoachAssigned($conn, $teamID)) {
                echo "<p style='color: red;'>A team can have only one head coach!</p>";
            } else {
                // Check if the coach is not already assigned to the team
                if (!isCoachAssignedToTeam($conn, $coachID, $teamID)) {
                    $isHeadCoach = isset($_POST["isHeadCoach"]) ? 1 : 0;

                    // Use prepared statement to prevent SQL injection
                    $stmt = $conn->prepare("INSERT INTO COACHES (CoachID, TeamID, is_head) VALUES (?, ?, ?)");
                    $stmt->bind_param("iii", $coachID, $teamID, $isHeadCoach);

                    if ($stmt->execute()) {
                        echo "<p style='color: green;'>Coach assigned to the team successfully!</p>";
                    } else {
                        echo "<p style='color: red;'>Error assigning coach to the team: " . $stmt->error . "</p>";
                    }

                    $stmt->close();
                } else {
                    echo "<p style='color: red;'>Coach is already assigned to the team!</p>";
                }
            }
        }

        // Display the form to assign a coach to a team
        echo "<div class='container'>";
        echo "<h1>Assign Coach to Team</h1>";
        echo "<form action=\"assignCoachToTeam.php\" method=\"POST\">";
        echo "Coach ID: <input type=\"text\" name=\"coachID\" required><br>";
        echo "Team ID: <input type=\"text\" name=\"teamID\" required><br>";
        echo "Is Head Coach? <input type=\"checkbox\" name=\"isHeadCoach\"><br>";
        echo "<input type=\"submit\" value=\"Assign Coach to Team\">";
        echo "</form>";

        // Back button
        echo "<div class='back-button'>";
        echo "<a href='welcome.php'>Back to Menu</a>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>You are not authorized to access this page!</p>";
    }

    $conn->close();

    function isCoachAssignedToTeam($conn, $coachID, $teamID) {
        // Check if the coach is already assigned to the team
        $stmt = $conn->prepare("SELECT * FROM COACHES WHERE CoachID = ? AND TeamID = ?");
        $stmt->bind_param("ii", $coachID, $teamID);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    function isTeamHeadCoachAssigned($conn, $teamID) {
        // Check if the team already has a head coach
        $stmt = $conn->prepare("SELECT * FROM COACHES WHERE TeamID = ? AND is_head = 1");
        $stmt->bind_param("i", $teamID);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
    ?>

</body>

</html>
