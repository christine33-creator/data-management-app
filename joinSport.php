<!DOCTYPE html>
<html>

<head>
    <title>Join a Sport</title>
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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["UserID"])) {
        // Fetch the associated player ID using the user ID
        $userID = $_SESSION["UserID"];
        $getPlayerIDQuery = $conn->prepare("SELECT PlayerID FROM PLAYER WHERE UserID = ?");
        $getPlayerIDQuery->bind_param("i", $userID);
        $getPlayerIDQuery->execute();
        $getPlayerIDResult = $getPlayerIDQuery->get_result();

        if ($getPlayerIDResult->num_rows > 0) {
            $playerID = $getPlayerIDResult->fetch_assoc()["PlayerID"];
            $sportName = $_POST["sportName"];
            echo "Player ID: " . $playerID . "<br>";

            // Check if the sport exists in the NEEDS table
            $checkSportQuery = $conn->prepare("SELECT SportsName FROM NEEDS WHERE SportsName = ?");
            $checkSportQuery->bind_param("s", $sportName);
            $checkSportQuery->execute();
            $checkSportResult = $checkSportQuery->get_result();

            if ($checkSportResult->num_rows > 0) {
                // Sport exists, proceed to join
                $stmt = $conn->prepare("INSERT INTO PLAYS_IN (PlayerID, TeamID, Uniform_Number) VALUES (?, (SELECT TeamID FROM TEAM WHERE sname = ? LIMIT 1), ?)");
                $uniformNumber = rand(1, 99); // You may customize this to assign a specific uniform number
                $stmt->bind_param("iss", $playerID, $sportName, $uniformNumber);

                if ($stmt->execute()) {
                    echo "Successfully joined the sport!";
                } else {
                    echo "Error joining the sport: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Invalid sport selection. Please choose a valid sport.";
            }

            $checkSportQuery->close();
        } else {
            echo "Player ID not found for the logged-in user.";
        }

        $getPlayerIDQuery->close();
    }
    ?>

    <h1>Join a Sport</h1>
    <form action="joinSport.php" method="POST">
        Sport Name: <input type="text" name="sportName" required>
        <br><br>
        <input type="submit" value="Join Sport">
    </form>
</body>

</html>
