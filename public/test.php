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
    echo "Connected OK\n";
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@globifye.com']);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "Admin user already exists!\n";
    } else {
        // Create admin user
        // Password is: GlobiFYE@Admin2024
        $hashedPassword = password_hash('GlobiFYE@Admin2024', PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at)
            VALUES (?, ?, NOW(), ?, NOW(), NOW())
        ");
        $stmt->execute([
            'GlobiFYE Admin',
            'admin@globifye.com',
            $hashedPassword
        ]);
        
        echo "Admin user created successfully!\n";
        echo "Email: admin@globifye.com\n";
        echo "Password: GlobiFYE@Admin2024\n";
    }
    
    // Verify
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n=== USERS IN DATABASE ===\n";
    foreach ($users as $u) {
        echo "ID: " . $u['id'] . "\n";
        echo "Name: " . $u['name'] . "\n";
        echo "Email: " . $u['email'] . "\n";
        echo "Created: " . $u['created_at'] . "\n";
    }
    
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "</pre>";
