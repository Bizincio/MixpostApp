<?php
echo "<pre>";

// Generate Mixpost API token via Laravel Sanctum
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
    
    // Get user ID
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@bizinc.io']);
    $userId = $stmt->fetchColumn();
    
    if (!$userId) {
        echo "User not found!\n";
        exit;
    }
    
    echo "User ID: $userId\n";
    
    // Generate a secure token
    $tokenPlain = bin2hex(random_bytes(40));
    $tokenHash = hash('sha256', $tokenPlain);
    
    // Check if token already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM personal_access_tokens WHERE name = ? AND tokenable_id = ?");
    $stmt->execute(['GlobiFYE Integration', $userId]);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        // Delete old token
        $stmt = $pdo->prepare("DELETE FROM personal_access_tokens WHERE name = ? AND tokenable_id = ?");
        $stmt->execute(['GlobiFYE Integration', $userId]);
        echo "Old token deleted, generating fresh one\n";
    }
    
    // Insert new token
    $stmt = $pdo->prepare("
        INSERT INTO personal_access_tokens 
        (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([
        'App\\Models\\User',
        $userId,
        'GlobiFYE Integration',
        $tokenHash,
        '["*"]'
    ]);
    
    $tokenId = $pdo->lastInsertId();
    $fullToken = $tokenId . '|' . $tokenPlain;
    
    echo "\n=== YOUR MIXPOST API TOKEN ===\n";
    echo "COPY THIS ENTIRE VALUE:\n\n";
    echo $fullToken . "\n\n";
    echo "=== SAVE THIS NOW - it will not be shown again ===\n";
    echo "Add this to Supabase secrets as: MIXPOST_ADMIN_API_TOKEN\n";
    
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "</pre>";
