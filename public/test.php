<?php
echo "PHP is working\n";
echo "Redis test: ";
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    echo "Connected OK\n";
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "Storage writable: ";
echo is_writable('/var/www/html/storage/framework/sessions') ? "YES\n" : "NO\n";

echo "Bootstrap cache writable: ";
echo is_writable('/var/www/html/bootstrap/cache') ? "YES\n" : "NO\n";

echo "ENV APP_URL: " . getenv('APP_URL') . "\n";
echo "ENV SESSION_DRIVER: " . getenv('SESSION_DRIVER') . "\n";

phpinfo();
