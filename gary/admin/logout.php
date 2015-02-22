<?php
    session_start();
    $_SESSION['loggedIn'] = false;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Goodbye, Gary</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css"> 
    </head>
    <body>
        <div class="panel panel-default">
            <div class="panel-body panel-success">
                You are now logged out! &middot; <a href="../detail.php">Return to Store</a>
            </div>
        </div>
    </body>
</html>