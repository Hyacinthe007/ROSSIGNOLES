<?php
$mysqli = new mysqli('localhost', 'root', '', 'rossignoles');
if ($mysqli->connect_error) die('Connect Error');

echo "--- EMPLOIS DU TEMPS ---\n";
$query = "SELECT et.id, et.jour_semaine, et.heure_debut, et.heure_fin, m.nom as matiere, p.nom as prof 
          FROM emplois_temps et 
          JOIN matieres m ON et.matiere_id = m.id 
          LEFT JOIN personnels p ON et.personnel_id = p.id 
          LIMIT 5";
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) echo json_encode($row) . "\n";

echo "\n--- DERNIERES ABSENCES ---\n";
$query = "SELECT id, date_absence, heure_debut, heure_fin FROM absences ORDER BY id DESC LIMIT 5";
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) echo json_encode($row) . "\n";

$mysqli->close();
