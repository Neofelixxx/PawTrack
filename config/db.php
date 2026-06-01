<?php

$host = "localhost";
$port = "5432";
$db   = "PawTrack";
$user = "postgres";
$pass = "abc123";

$conn = pg_connect("
    host=$host
    port=$port
    dbname=$db
    user=$user
    password=$pass
");

if (!$conn) {
    die("PostgreSQL connection failed.");
}

?>