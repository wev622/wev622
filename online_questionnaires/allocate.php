<?php

$servername = "devweb2019.cis.strath.ac.uk";
// space for username/password

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT Page FROM PageActive WHERE Active = 0 LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $SQL = "UPDATE PageActive SET Active = 1 WHERE Page = '" . $row["Page"] . "'";
        $update = $conn->query($SQL);

        if ($update) {
            echo $row["Page"];
            return;
        }
    }
}

echo 'none';

?>