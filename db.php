<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        // DB credentials are read from environment variables per ADR-0002.
        // Fallback values are provided to ease local development but should NOT be used in production.
        $environment = getenv('APP_ENV') ?: 'development';
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'ccczoo';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        if ($environment === 'production' && ($host === '127.0.0.1' || $user === 'root' || $pass === '')) {
            throw new RuntimeException('Production database configuration is incomplete.');
        }

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
        // Schema creation and seed data belong to explicit, versioned migrations.
        // Never mutate roles or permissions during a normal web request.
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function getPersonnelStats(): array
    {
        $stats = [];
        $queries = [
            'personnel_total' => 'SELECT COUNT(*) FROM personnel',
            'directeurs' => "SELECT COUNT(*) FROM personnel WHERE is_director = 1",
            'chefs_service' => "SELECT COUNT(*) FROM personnel WHERE categorie_personnel = 'chef_service'",
            'agents' => "SELECT COUNT(*) FROM personnel WHERE categorie_personnel = 'agent'",
            'comptes_utilisateurs' => 'SELECT COUNT(*) FROM users',
            'comptes_actifs' => 'SELECT COUNT(*) FROM users WHERE is_active = 1',
            'comptes_inactifs' => 'SELECT COUNT(*) FROM users WHERE is_active = 0',
            'personnel_sans_compte' => 'SELECT COUNT(*) FROM personnel WHERE user_id IS NULL',
            'personnel_verification' => 'SELECT COUNT(*) FROM personnel WHERE categorie_personnel = ' . $this->pdo->quote('autre') . ' OR grade IS NULL OR fonction IS NULL',
        ];

        foreach ($queries as $key => $sql) {
            $stats[$key] = (int) $this->pdo->query($sql)->fetchColumn();
        }

        return $stats;
    }

    public function saveContactRequest(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO contact_requests (name, email, phone, subject, category, message)
             VALUES (:name, :email, :phone, :subject, :category, :message)'
        );

        return $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':phone'    => $data['phone'] ?? null,
            ':subject'  => $data['subject'] ?? null,
            ':category' => $data['category'] ?? null,
            ':message'  => $data['message'],
        ]);
    }

    public function getContactRequests(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM contact_requests ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, d.name AS direction_name
             FROM users u JOIN directions d ON d.id = u.direction_id
             WHERE u.email = :email LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findUserById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, d.name AS direction_name
             FROM users u JOIN directions d ON d.id = u.direction_id
             WHERE u.id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getUserPermissions(int $userId): array
    {
        // Explicit user overrides take precedence over inherited role permissions.
        $stmt = $this->pdo->prepare(
            'SELECT p.name
             FROM permissions p
             WHERE (p.name NOT IN (\'admin.users\', \'profile.update\', \'audit.read\') OR EXISTS (
                 SELECT 1 FROM user_roles ur_admin JOIN roles r_admin ON r_admin.id = ur_admin.role_id
                 WHERE ur_admin.user_id = :admin_role_user_id AND r_admin.name = \'administrateur\'
             )) AND EXISTS (
                 SELECT 1 FROM role_permissions rp
                 JOIN user_roles ur ON ur.role_id = rp.role_id
                 WHERE rp.permission_id = p.id AND ur.user_id = :role_user_id
             ) AND NOT EXISTS (
                 SELECT 1 FROM user_permission_overrides uo
                 WHERE uo.user_id = :deny_user_id AND uo.permission_id = p.id
             )
             UNION
             SELECT p.name
             FROM user_permission_overrides uo
             JOIN permissions p ON p.id = uo.permission_id
             WHERE uo.user_id = :allow_user_id AND uo.allowed = 1
               AND (p.name NOT IN (\'admin.users\', \'profile.update\', \'audit.read\') OR EXISTS (
                   SELECT 1 FROM user_roles ur_admin JOIN roles r_admin ON r_admin.id = ur_admin.role_id
                   WHERE ur_admin.user_id = :admin_user_id AND r_admin.name = \'administrateur\'
               ))'
        );
        $stmt->execute([
            ':admin_role_user_id' => $userId,
            ':role_user_id' => $userId,
            ':deny_user_id' => $userId,
            ':allow_user_id' => $userId,
            ':admin_user_id' => $userId,
        ]);
        return array_column($stmt->fetchAll(), 'name');
    }

    public function getUserRoles(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.name FROM roles r JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = :user_id ORDER BY r.name'
        );
        $stmt->execute([':user_id' => $userId]);
        return array_column($stmt->fetchAll(), 'name');
    }

    public function countRecentFailedLogins(string $ipAddress, int $minutes = 15): int
    {
        $minutes = max(1, min(60, $minutes));
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM audit_logs
             WHERE action = 'LOGIN' AND result = 'denied' AND ip_address = :ip
               AND created_at >= DATE_SUB(NOW(), INTERVAL {$minutes} MINUTE)"
        );
        $stmt->execute([':ip' => $ipAddress]);
        return (int) $stmt->fetchColumn();
    }

    public function recordAudit(?int $userId, ?string $direction, string $action, ?string $resourceType, ?string $resourceId, string $result): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_logs
             (user_id, direction, action, resource_type, resource_id, result, ip_address)
             VALUES (:user_id, :direction, :action, :resource_type, :resource_id, :result, :ip_address)'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':direction' => $direction,
            ':action' => $action,
            ':resource_type' => $resourceType,
            ':resource_id' => $resourceId,
            ':result' => $result,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    public function updateLastLogin(int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute([':id' => $userId]);
    }

    public function listItems(string $module, int $limit = 100, ?int $directionId = null, ?int $userId = null, string $scope = 'all'): array
    {
        $sql = 'SELECT i.*, u.name AS creator_name, assigned.name AS assigned_to_name
                FROM internal_items i
                JOIN users u ON u.id = i.created_by
                LEFT JOIN users assigned ON assigned.id = i.assigned_to
                WHERE i.module = :module';
        $params = [':module' => $module];

        if ($scope === 'user' && $userId !== null) {
            $sql .= ' AND (i.assigned_to = :assigned_user_id OR i.created_by = :created_user_id)';
            $params[':assigned_user_id'] = $userId;
            $params[':created_user_id'] = $userId;
        } elseif ($scope === 'direction' && $directionId !== null) {
            $sql .= ' AND (i.direction_id = :direction_id OR (i.direction_id IS NULL AND u.direction_id = :fallback_direction_id))';
            $params[':direction_id'] = $directionId;
            $params[':fallback_direction_id'] = $directionId;
        }

        $sql .= ' ORDER BY i.updated_at DESC LIMIT :limit';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : ($value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->bindValue(':limit', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createItem(string $module, string $title, string $description, int $userId, ?int $assignedTo = null): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO internal_items (module, title, description, direction_id, assigned_to, created_by, updated_by)
             SELECT :module, :title, :description, COALESCE(assignee.direction_id, creator.direction_id),
                    :assigned_to_value, :created_by, :updated_by
             FROM users creator
             LEFT JOIN users assignee ON assignee.id = :assigned_to_join AND assignee.is_active = 1
             WHERE creator.id = :creator_id AND (:assigned_to_check IS NULL OR assignee.id IS NOT NULL)'
        );
        $stmt->execute([
            ':module' => $module,
            ':title' => $title,
            ':description' => $description ?: null,
            ':assigned_to_value' => $assignedTo,
            ':assigned_to_join' => $assignedTo,
            ':created_by' => $userId,
            ':updated_by' => $userId,
            ':creator_id' => $userId,
            ':assigned_to_check' => $assignedTo,
        ]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('Impossible de créer cet élément avec cette affectation.');
        }
        return (int) $this->pdo->lastInsertId();
    }
}

