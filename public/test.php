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
    echo "Connected OK\n\n";
    
    // Show ALL tables
    echo "=== ALL TABLES ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Total tables: " . count($tables) . "\n";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  - $table ($count rows)\n";
    }
    
    // Check migrations table to see what ran
    echo "\n=== MIGRATIONS THAT RAN ===\n";
    $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY id");
    $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($migrations as $m) {
        echo "  $m\n";
    }
    
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "</pre>";
