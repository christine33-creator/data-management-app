<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    
<?php
     session_start();
     if(isset($_SESSION["userid"]) && $_SESSION["userid"]!="")
     {
            echo $_SESSION["fullname"];
            echo " logged out successfully.";
            session_destroy();
     }
            
?>
<br><br>
<a href='index.html'>Login Again</a> 
</body>
</html>