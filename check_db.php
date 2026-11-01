<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=gymie', 'root', '');
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
echo "tables: " . count($tables) . "\n";
echo implode(', ', array_slice(array_map(fn($r) => $r[0], $tables), 0, 15)) . "\n";
