<?php
$connection = new mysqli('localhost', 'root', '' );
$connection->select_db('visit') or die ("select database error stynax :" . mysqli_connect_error());
$query = "select name from nkvisit where id > ?";
$stmt = $connection->prepare($query);
$id = 2;
$stmt->bind_param('i', $id);
$stmt->execute();
while ($result = $stmt->fetch()) {
    print_r($result);
}