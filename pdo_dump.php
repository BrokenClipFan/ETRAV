<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=etrav', 'root', '');
$stmt = $pdo->query("DESCRIBE transports");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
