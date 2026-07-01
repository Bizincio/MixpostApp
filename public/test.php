<?php
echo "<pre>";

// Test 1: Redis session test
echo "=== REDIS SESSION TEST ===\n";
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->set('test_key', 'test_value', 10);
    $val = $redis->get('test_key');
    echo "Redis write/read: " . ($val === 'test_value' ? "OK" : "FAILED") . "\n";
    $redis->del('test_key');
} catch (Exception $e) {
    echo "Redis FAILED: " . $e->getMessage() . "\n";
}

// Test 2: MySQL with SSL
echo "\n=== MYSQL SSL CONNECTION TEST ===\n";
try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    $ssl_ca = getenv('MYSQL_ATTR_SSL_CA');
    
    echo "SSL CA file exists: " . (file_exists($ssl_ca) ? "YES" : "NO - not found at: $ssl_ca") . "\n";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $ssl_ca,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "MySQL connection: OK\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $exists = $stmt->fetch();
    echo "Users table exists: " . ($exists ? "YES" : "NO") . "\n";
    
    if ($exists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "User count: " . $row['count'] . "\n";
        
        if ($row['count'] > 0) {
            $stmt = $pdo->query("SELECT email FROM users LIMIT 5");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $u) {
                echo "  User found: " . $u['email'] . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "MySQL FAILED: " . $e->getMessage() . "\n";
}

// Test 3: Key environment variables
echo "\n=== ENVIRONMENT VARIABLES ===\n";
$vars = ['APP_URL', 'APP_KEY', 'APP_ENV', 'APP_DEBUG', 
         'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME',
         'SESSION_DRIVER', 'MYSQL_ATTR_SSL_CA', 'TRUSTED_PROXIES'];
foreach ($vars as $var) {
    $val = getenv($var);
    if (in_array($var, ['DB_PASSWORD', 'APP_KEY'])) {
        echo "$var: " . ($val ? "[SET - ".strlen($val)." chars]" : "[NOT SET]") . "\n";
    } else {
        echo "$var: " . ($val ?: "[NOT SET]") . "\n";
    }
}

echo "</pre>";
