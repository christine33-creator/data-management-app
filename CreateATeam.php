<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team</title>
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

        form {
            width: 50%;
            margin: 20px auto;
            background-color: #fff; /* White background for the form */
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Light box shadow */
            border-radius: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff; /* Blue submit button background color */
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        input[type="text"] {
            text-transform: uppercase; /* Convert text to uppercase */
        }

        .success-message {
            color: #28a745; /* Green text color for success messages */
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

        $showForm = true;  // Variable to control whether to show the form or not

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["teamID"]) && isset($_POST["sname"]) && isset($_POST["sex"])
            && isset($_POST["name"]) && isset($_POST["min_age"]) && isset($_POST["max_age"])) {

            $teamID = $_POST["teamID"];
            $sname = $_POST["sname"];
            $sex = $_POST["sex"];
            $name = $_POST["name"];
            $min_age = $_POST["min_age"];
            $max_age = $_POST["max_age"];

            // Check if the provided SNAME exists in the SPORT table
            $checkSportQuery = $conn->prepare("SELECT SNAME FROM SPORT WHERE SNAME = ?");
            $checkSportQuery->bind_param("s", $sname);
            $checkSportQuery->execute();
            $checkSportResult = $checkSportQuery->get_result();

            if ($checkSportResult->num_rows > 0) {
                // SNAME exists, proceed to create the team
                $stmt = $conn->prepare("INSERT INTO TEAM (TEAMID, SNAME, SEX, NAME, MIN_AGE, MAX_AGE) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssii", $teamID, $sname, $sex, $name, $min_age, $max_age);

                try {
                    if ($stmt->execute()) {
                        echo "<h1 class='success-message'>Team created successfully!</h1>";

                        // Ask if the user wants to create another team
                        echo "<br><br>";
                        echo "Do you want to create another team?";
                        echo "<form action=\"CreateATeam.php\" method=\"POST\">";
                        echo "<input type=\"submit\" name=\"createAnother\" value=\"Yes\">";
                        echo "<input type=\"submit\" name=\"createAnother\" value=\"No\">";
                        echo "</form>";

                        $showForm = false;  // Do not show the form again for now
                    } else {
                        echo "Error creating team: " . $stmt->error;
                    }
                } catch (mysqli_sql_exception $e) {
                    // Handle the duplicate entry exception
                    echo "<p class='error-message'>Team with ID $teamID already exists. Please choose a different Team ID.</p>";
                }

                $stmt->close();
            } else {
                echo "<p class='error-message'>Invalid Sport Name. Please choose a valid Sport Name.</p>";
            }

            $checkSportQuery->close();
        }

        // Display the form to create a team only if the condition is met
        if ($showForm) {
            echo "<h1>Create Team</h1>";
            echo "<form action=\"CreateATeam.php\" method=\"POST\">";
            echo "Team ID: <input type=\"text\" name=\"teamID\" required><br>";
            echo "Sport Name: <input type=\"text\" name=\"sname\" required><br>";
            echo "Sex: <input type=\"text\" name=\"sex\" required><br>";
            echo "Team Name: <input type=\"text\" name=\"name\" required><br>";
            echo "Min Age: <input type=\"text\" name=\"min_age\" required><br>";
            echo "Max Age: <input type=\"text\" name=\"max_age\" required><br>";
            echo "<input type=\"submit\" value=\"Create Team\">";
            echo "</form>";

            // Back button
            echo "<div class='back-button'>";
            echo "<a href='welcome.php'>Back to Menu</a>";
            echo "</div>";
        }

        // Check if the user chose not to create another team
        if (isset($_POST["createAnother"]) && $_POST["createAnother"] == "No") {
            echo "<br><br>";
            echo "Redirecting to the Admin Menu...";
            // Redirect to the admin menu after a short delay
            header("refresh:2;url=welcome.php");
            exit();
        }

    } else {
        echo "<p class='error-message'>You are not authorized to access this page!</p>";
    }

    $conn->close();
    ?>


</body>

</html>
