<?php
$servername = "localhost";
$username = "cakeuser421";
$password = "?.0MX*6RqtsM";
$dbname = "mycakedb421";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "Connected successfully";

$sql = "SELECT *  FROM store_category WHERE visible = 1 ORDER BY rank desc";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Category: " . $row["name"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();

?>