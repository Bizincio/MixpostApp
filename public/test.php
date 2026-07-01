<?php
echo "<pre>";

try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    $ssl_ca = getenv('MYSQL_ATTR_SSL_CA');
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $ssl_ca,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to MySQL OK\n\n";
    
    // Show all tables so we know exact schema
    echo "=== ALL TABLES IN DATABASE ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    // Check users table structure
    echo "\n=== USERS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query("DESCRIBE users");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "  " . $col['Field'] . " (" . $col['Type'] . ")" . 
             ($col['Null'] === 'NO' ? ' NOT NULL' : '') . "\n";
    }
    
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "</pre>";
