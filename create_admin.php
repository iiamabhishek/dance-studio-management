<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=gymie', 'root', '');
$u = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
echo "users: {$u}\n";
if ($u == 0) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $db->prepare("INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW(), NOW())")
       ->execute(['Admin', 'admin@gymie.in', $hash]);
    echo "inserted admin user\n";
}
$row = $db->query("SELECT id,name,email FROM users")->fetch(PDO::FETCH_ASSOC);
print_r($row);
