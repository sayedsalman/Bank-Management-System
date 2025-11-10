<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent re-declaration or double inclusion
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    $host = 'localhost';
    $dbname = 'losers_bank';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    // ✅ Helper functions
    if (!function_exists('isLoggedIn')) {
        function isLoggedIn(): bool {
            return isset($_SESSION['user_id']);
        }
    }

    if (!function_exists('getUserRole')) {
        function getUserRole(): ?string {
            return $_SESSION['role'] ?? null;
        }
    }

    if (!function_exists('redirectIfNotLoggedIn')) {
        function redirectIfNotLoggedIn(): void {
            if (!isLoggedIn()) {
                header('Location: login.php');
                exit();
            }
        }
    }

    if (!function_exists('redirectBasedOnRole')) {
        function redirectBasedOnRole(): void {
            if (isLoggedIn()) {
                $role = getUserRole();
                switch ($role) {
                    case 'admin':
                        header('Location: admin.php');
                        break;
                    case 'employee':
                        header('Location: employee.php');
                        break;
                    case 'customer':
                        header('Location: customer.php');
                        break;
                }
                exit();
            }
        }
    }

    if (!function_exists('logAction')) {
        function logAction($action, $description): void {
            global $pdo;
            $stmt = $pdo->prepare("
                INSERT INTO s_system_logs (user_id, action, description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'] ?? null,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        }
    }

    if (!function_exists('addAuditTrail')) {
        function addAuditTrail($table_name, $record_id, $action, $old_values = null, $new_values = null): void {
            global $pdo;
            $stmt = $pdo->prepare("
                INSERT INTO s_audit_trail (table_name, record_id, action, old_values, new_values, changed_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $table_name,
                $record_id,
                $action,
                $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null,
                $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null,
                $_SESSION['user_id'] ?? null
            ]);
        }
    }
}
?>
