<?php
$mysqli = new mysqli('localhost', 'root', '', 'rossignoles');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$query = "SELECT id, date_absence, heure_debut, heure_fin, periode FROM absences ORDER BY id DESC LIMIT 10";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "Error: " . $mysqli->error;
}
$mysqli->close();
