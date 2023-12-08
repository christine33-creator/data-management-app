<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Lake Forest College Sports Website.</title>
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

        h1, h2, h3 {
            color: #333;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 10px;
        }

        h3 {
            color: #4285f4;
            margin-top: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #4285f4;
            font-weight: bold;
            transition: color 0.3s ease-in-out;
        }

        a:hover {
            color: #1a73e8;
        }

        .submenu a {
            text-decoration: underline;
            margin-left: 10px;
        }

        .admin-menu a {
            color: #d93025;
        }

        .logout-link {
            color: #d93025;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
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

// Am I coming from the login screen, or am I coming from another page?
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    handleLoginForm($conn);
} elseif (isLoggedIn()) {
    displayWelcomePage();
} else {
    displayLoginPage();
}

$conn->close();

function handleLoginForm($conn) {
    // Check if Userid and Password are set in the post data and not empty
    if (isset($_POST["Userid"]) && !empty($_POST["Userid"]) && isset($_POST["Password"]) && !empty($_POST["Password"])) {
        $userid = $_POST["Userid"];
        $password = $_POST["Password"];

        // Check if the user exists in the database and the password is correct
        $stmt = $conn->prepare("SELECT UserID, Fname, Lname FROM user WHERE UserID = ? AND Password = ?");
        $stmt->bind_param("ss", $userid, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Set session variables
            $_SESSION["Userid"] = $row["UserID"];
            $_SESSION["Password"] = $password;
            $_SESSION["fullname"] = $row["Fname"] . " " . $row["Lname"];
            $_SESSION["userType"] = determineUserType($conn, $userid);

            // Redirect to welcome.php (prevent form resubmission)
            header("Location: welcome.php");
            exit();
        } else {
            // User doesn't exist in the database or incorrect password
            echo "<h1>View Sports LFC Home Screen</h1>";
            echo "Invalid username or password.";
            echo "Please <a href='index.html'>login</a> with valid credentials.";
        }

        $stmt->close();
    } else {
        // Form data is incomplete
        echo "<h1>View Sports LFC Home Screen</h1>";
        echo "Please enter both username and password.";
        echo "Please <a href='index.html'>login</a> with valid credentials.";
    }
}

function isLoggedIn() {
    return isset($_SESSION["Userid"]) && !empty($_SESSION["Userid"]) && isset($_SESSION["Password"]) && !empty($_SESSION["Password"]);
}

function displayWelcomePage() {
    echo "<h1>View Sports LFC Home Screen</h1>";
    // Check if user type is set in the session
    if (isset($_SESSION["userType"])) {
        $userType = $_SESSION["userType"];
        // Check if fullname is set in the session
        echo "<h2>Welcome, " . (isset($_SESSION["fullname"]) ? $_SESSION["fullname"] : "") . ".</h2>";

        // Check the user type before displaying the menu
        if ($userType !== "Admin" && $userType !== "Coach" && $userType !== "Player") {
            echo "Sorry! You don't have a login. ";
            echo "Please <a href='signup.php'>sign up</a> if you don't have an account.";
        } else {
            // Display menu based on user type
            displayMenu($userType);
            echo "<a href=\"logout.php\" class='logout-link'>Sign Out</a>";
        }
    } else {
        echo "You are not supposed to be here!<br>";
        echo "<a href=\"index.html\">Login</a> to continue.";
    }
}

function displayLoginPage() {
    echo "<h1>View Sports LFC Home Screen</h1>";
    // Check if fullname is set in the session
    echo "<h2>Welcome, " . (isset($_SESSION["fullname"]) ? $_SESSION["fullname"] : "") . ".</h2>";
    echo "You are not supposed to be here!<br>";
    echo "<a href=\"index.html\">Login</a> to continue.";
}

function determineUserType($conn, $userid) {
    // Check if the user is a player, coach, or admin
    $playerQuery = $conn->prepare("SELECT PlayerID FROM PLAYER WHERE UserID = ?");
    $playerQuery->bind_param("s", $userid);
    $playerQuery->execute();
    $playerResult = $playerQuery->get_result();

    $coachQuery = $conn->prepare("SELECT CoachID FROM COACH WHERE UserID = ?");
    $coachQuery->bind_param("s", $userid);
    $coachQuery->execute();
    $coachResult = $coachQuery->get_result();

    if ($playerResult->num_rows > 0) {
        return "Player";
    } elseif ($coachResult->num_rows > 0) {
        return "Coach";
    } else {
        return "Admin";
    }
}

function displayMenu($userType) {
    echo "<h3>{$userType} Menu</h3>";
    echo "<ul>";

    if ($userType == "Admin") {
        echo "<li> <a href=\"ViewAllPlayers.php\"> View All Players </a></li>";
        echo "<li> <a href=\"ViewAllCoaches.php\"> View All Coaches </a></li>";
        echo "<li> <a href=\"CreateATeam.php\"> Create A Team </a></li>";
        echo "<li> <a href=\"assignCoachToTeam.php\"> Assign Coach to a Team </a></li>";
    } elseif ($userType == "Coach") {
        echo "<li> <a href=\"PlayerSportTeamSearch.php\"> Search for a Team </a></li>";
        echo "<li> <a href=\"viewTeams.php\"> View Your Teams </a></li>";
        echo "<li> <a href=\"CoachAddPlayer.php\"> Add a Player to a Team </a></li>";
        echo "<li> <a href=\"CoachLeaveTeam.php\"> Leave a Team </a></li>";
    } elseif ($userType == "Player") {
        echo "<li> <a href=\"PlayerSportTeamSearch.php\"> Search for a Team </a></li>";
        echo "<li> <a href=\"joinSport.php\"> Join a Sport </a></li>";
        echo "<li> <a href=\"viewTeams.php\"> View Your Teams </a></li>";
        echo "<li> <a href=\"PlayerLeaveATeam.php\"> Leave a Team </a></li>";
    }

    echo "</ul>";
    echo "<br><br><br><br>";
}
?>
</body>
</html>