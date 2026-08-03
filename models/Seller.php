<?php

namespace models;

use api\exceptions\ApiException;
use helpers\Logger;
use helpers\Response;
use JsonSerializable;

/**
 *
 */
class Seller implements JsonSerializable
{


    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $passwordSalt;

    /**
     * @var string
     */
    private string $passwordHash;

    /**
     * @var bool
     */
    private bool $deleted;

    private string $createdAt;
    private string $updatedAt;
    private int $code;
    private bool $isAdmin;
    private float $dolar;

    /**
     *
     */
    public function __construct()
    {
        $this->id = 0;
        $this->name = '';
        $this->code = 0;
        $this->email = '';
        $this->passwordSalt = '';
        $this->passwordHash = '';
        $this->deleted = false;
        $this->createdAt = '';
        $this->updatedAt = '';
        $this->isAdmin = false;
        $this->dolar = 0.0;
    }


    /**
     * Specify data which should be serialized to JSON
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'code' => $this->code,
            'passwordSalt' => $this->passwordSalt,
            'passwordHash' => $this->passwordHash,
            'deleted' => $this->deleted,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'isAdmin' => $this->isAdmin,
            'dolar' => $this->dolar,
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string
    {
        return $this->passwordSalt;
    }

    /**
     * @param string $passwordSalt
     */
    public function setPasswordSalt(string $passwordSalt): void
    {
        $this->passwordSalt = $passwordSalt;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return bool
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public static function getByEmail(string $email): ?Seller
    {
        $sql = "SELECT vendedor_id AS id, vendedor_name AS name, vendedor_code AS code, vendedor_email AS email, vendedor_password_salt AS password_salt, vendedor_password_hash AS password_hash, vendedor_deleted AS deleted, vendedor_created_at AS created_at, vendedor_updated_at AS updated_at, vendedor_is_admin AS is_admin, vendedor_dolar AS dolar
                FROM vendedor
                WHERE vendedor_email = ? OR vendedor_name = ?";

        $stmt = Connection::getConn()->prepare($sql);
        $stmt->bind_param('ss', $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $seller = null;
        
        if ($row = $result->fetch_assoc()) {
            $seller = new Seller();
            $seller->setId($row['id']);
            $seller->setName($row['name']);
            $seller->setCode($row['code']);
            $seller->setEmail($row['email']);
            $seller->setPasswordSalt($row['password_salt']);
            $seller->setPasswordHash($row['password_hash']);
            $seller->setDeleted($row['deleted']);
            $seller->setCreatedAt($row['created_at']);
            $seller->setUpdatedAt($row['updated_at']);
            $seller->setIsAdmin((bool)$row['is_admin']);
            $seller->setDolar((float)$row['dolar']);
        }

        $stmt->close();
        $result->free();

        return $seller;
    }


    public static function createOrUpdate(Seller $seller): Seller
    {
        $conn = Connection::getConn();
        Logger::log('info', json_encode($seller));

         $query = "INSERT INTO vendedor (vendedor_name, vendedor_code, vendedor_email, vendedor_deleted, vendedor_created_at, vendedor_updated_at)
        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?) 
        ON DUPLICATE KEY UPDATE 
            vendedor_name = VALUES(vendedor_name),
            vendedor_code = VALUES(vendedor_code),
            vendedor_email = VALUES(vendedor_email),
            vendedor_deleted = VALUES(vendedor_deleted),
            vendedor_updated_at = VALUES(vendedor_updated_at)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param(
            'sssis',
            $seller->name,
            $seller->code,
            $seller->email,
            $seller->deleted,
            $seller->updatedAt,
        );

        $stmt->execute();
        $seller->setId($stmt->insert_id);

        return $seller;
    }

    public static function getOutdatedSellers(string $lastUpdatedAt): array
    {
        $conn = Connection::getConn();

        $query = "SELECT vendedor_id AS id, vendedor_name AS name, vendedor_code AS code, vendedor_email AS email, vendedor_password_salt AS password_salt, vendedor_password_hash AS password_hash, vendedor_deleted AS deleted, vendedor_created_at AS created_at, vendedor_updated_at AS updated_at, vendedor_is_admin AS is_admin, vendedor_dolar AS dolar
        FROM vendedor
        WHERE vendedor_updated_at != ? AND vendedor_id != 999999";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $lastUpdatedAt);

        $stmt->execute();

        $result = $stmt->get_result();

        $sellers = [];

        while ($row = $result->fetch_assoc()) {
            $seller = new Seller();
            $seller->setCode($row["code"]);
            $seller->setName($row["name"]); 
            $seller->setPasswordSalt($row["password_salt"]);
            $seller->setPasswordHash($row["password_hash"]);
            $seller->setDeleted($row["deleted"]);
            $seller->setUpdatedAt($row["updated_at"]);
            $seller->setDolar((float)$row["dolar"]);
            $sellers[] = $seller;
        }

        $result->free();

        return $sellers;
    }

    public static function updateSeller(Seller $seller): bool
    {
        $conn = Connection::getConn();
        // Validar valores antes de ejecutar la consulta
        $seller->deleted = (int) $seller->deleted;
        $seller->updatedAt = $seller->updatedAt ?? date('Y-m-d H:i:s');

        $query = "UPDATE vendedor SET 
           vendedor_name = ?, 
           vendedor_email = ?, 
           vendedor_password_salt = ?, 
           vendedor_password_hash = ?, 
           vendedor_deleted = 1, 
           vendedor_updated_at = ? 
           WHERE vendedor_code = ?";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            Logger::log('error', 'SQL Prepare failed: ' . $conn->error);
            return false;
        }

        $stmt->bind_param(
            'sssssi',
            $seller->name,
            $seller->email,
            $seller->passwordSalt,
            $seller->passwordHash,
            $seller->updatedAt,
            $seller->code
        );

        $result = $stmt->execute();
        return $result;
    }

    public static function updatePassword(int $id, string $newPasswordHash): bool
    {
        $conn = Connection::getConn();
        $stmt = $conn->prepare("UPDATE vendedor SET vendedor_password_hash = ? WHERE vendedor_id = ?");
        $stmt->bind_param("si", $newPasswordHash, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public static function getStats(?int $sellerCode, array $filters = [])
    {
        $conn = Connection::getConn();

        if (empty($filters)) {
            $queryCurrent = "SELECT 
            COUNT(DISTINCT o.orden_id) AS total_orders,
            COUNT(DISTINCT CASE WHEN o.orden_status = 'pending' THEN o.orden_id END) AS pending_orders,
            COUNT(DISTINCT CASE WHEN o.orden_status = 'finalized' THEN o.orden_id END) AS finalized_orders,
            COUNT(DISTINCT CASE WHEN o.orden_status = 'not_realized' THEN o.orden_id END) AS not_realized_orders,
            SUM(CASE WHEN o.orden_status = 'finalized' THEN oi.orden_item_price * oi.orden_item_quantity ELSE 0 END) AS total_invoiced
            FROM ordenes o
            LEFT JOIN ordenes_items oi ON o.orden_id = oi.orden_item_orden_id
            WHERE o.orden_vendedor_code = ? 
            AND MONTH(o.orden_created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(o.orden_created_at) = YEAR(CURRENT_DATE());
        ";
            $stmt = $conn->prepare($queryCurrent);
            if (!$stmt) {
                throw new ApiException("Error al preparar la consulta actual: " . $conn->error);
            }

            $stmt->bind_param('i', $sellerCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $current = $result->fetch_assoc();

            $queryPrevious = "
            SELECT 
            COUNT(DISTINCT o.orden_id) AS total_orders,
            COUNT(DISTINCT CASE WHEN o.orden_status = 'pending' THEN o.orden_id END) AS pending_orders,
            COUNT(DISTINCT CASE WHEN o.orden_status = 'finalized' THEN o.orden_id END) AS finalized_orders,
            COUNT(DISTINCT CASE WHEN o.orden_status = 'not_realized' THEN o.orden_id END) AS not_realized_orders,
            SUM(CASE WHEN o.orden_status = 'finalized' THEN oi.orden_item_price * oi.orden_item_quantity ELSE 0 END) AS total_invoiced
            FROM ordenes o
            LEFT JOIN ordenes_items oi ON o.orden_id = oi.orden_item_orden_id
            WHERE o.orden_vendedor_code = ? AND MONTH(o.orden_created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(o.orden_created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
        ";

            $stmt = $conn->prepare($queryPrevious);
            if (!$stmt) {
                throw new ApiException("Error al preparar la consulta del mes anterior: " . $conn->error);
            }

            $stmt->bind_param('i', $sellerCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $previous = $result->fetch_assoc();

            // 📊 Función para calcular el porcentaje
            $calculatePercentage = function ($current, $previous) {
                if ($previous == 0) {
                    return $current > 0 ? 100 : 0;
                }
                return round((($current - $previous) / $previous) * 100, 2);
            };

            return [
                'total_orders' => [
                    'count' => $current['total_orders'] ?? 0,
                    'percentage' => $calculatePercentage($current['total_orders'] ?? 0, $previous['total_orders'] ?? 0)
                ],
                'pending_orders' => [
                    'count' => $current['pending_orders'] ?? 0,
                    'percentage' => $calculatePercentage($current['pending_orders'] ?? 0, $previous['pending_orders'] ?? 0)
                ],
                'finalized_orders' => [
                    'count' => $current['finalized_orders'] ?? 0,
                    'percentage' => $calculatePercentage($current['finalized_orders'] ?? 0, $previous['finalized_orders'] ?? 0)
                ],
                'not_realized_orders' => [
                    'count' => $current['not_realized_orders'] ?? 0,
                    'percentage' => $calculatePercentage($current['not_realized_orders'] ?? 0, $previous['not_realized_orders'] ?? 0)
                ],
                'total_invoiced' => [
                    'count' => $current['total_invoiced'] ?? 0,
                    'percentage' => $calculatePercentage($current['total_invoiced'] ?? 0.0, $previous['total_invoiced'] ?? 0.0)
                ]
            ];
        }

        // --- LÓGICA CON FILTROS (SYNC CON LISTADO DE ORDENES) ---
        $query = "SELECT 
        COUNT(DISTINCT o.orden_id) AS total_orders,
        COUNT(DISTINCT CASE WHEN o.orden_status = 'pending' THEN o.orden_id END) AS pending_orders,
        COUNT(DISTINCT CASE WHEN o.orden_status = 'finalized' THEN o.orden_id END) AS finalized_orders,
        COUNT(DISTINCT CASE WHEN o.orden_status = 'not_realized' THEN o.orden_id END) AS not_realized_orders,
        SUM(CASE WHEN o.orden_status = 'finalized' THEN oi.orden_item_price * oi.orden_item_quantity ELSE 0 END) AS total_invoiced
        FROM ordenes o
        LEFT JOIN ordenes_items oi ON o.orden_id = oi.orden_item_orden_id
        LEFT JOIN clientes c ON o.orden_cliente_code = c.cliente_code 
        WHERE 1=1";

        $params = [];
        $types = "";

        if (!empty($sellerCode)) {
            $query .= " AND o.orden_vendedor_code = ?";
            $params[] = $sellerCode;
            $types .= "i";
        }

        if (!empty($filters['search'])) {
            $query .= " AND (o.orden_id = ? OR c.cliente_name LIKE ? OR c.cliente_dni LIKE ?)";
            $searchId = is_numeric($filters['search']) ? (int)$filters['search'] : 0;
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchId;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "iss";
        }

        if (!empty($filters['status'])) {
            $query .= " AND o.orden_status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['dateFrom'])) {
            $query .= " AND DATE(o.orden_created_at) >= ?";
            $params[] = $filters['dateFrom'];
            $types .= "s";
        }

        if (!empty($filters['dateTo'])) {
            $query .= " AND DATE(o.orden_created_at) <= ?";
            $params[] = $filters['dateTo'];
            $types .= "s";
        }

        if (!empty($filters['customer_code'])) {
            $query .= " AND o.orden_cliente_code = ?";
            $params[] = $filters['customer_code'];
            $types .= "i";
        }

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new ApiException("Error al preparar la consulta con filtros: " . $conn->error);
        }

        if (!empty($params)) {
             $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $current = $result->fetch_assoc();

        $formatStat = function($val) {
             return [
                 'count' => $val ?? 0,
                 'percentage' => 0 
             ];
        };

        return [
            'total_orders' => $formatStat($current['total_orders']),
            'pending_orders' => $formatStat($current['pending_orders']),
            'finalized_orders' => $formatStat($current['finalized_orders']),
            'not_realized_orders' => $formatStat($current['not_realized_orders']),
            'total_invoiced' => $formatStat($current['total_invoiced']),
        ];
    }

    public static function getAll(): array
    {
        $conn = Connection::getConn();
        $query = "SELECT vendedor_id AS id, vendedor_name AS name, vendedor_code AS code, vendedor_email AS email, vendedor_deleted AS deleted, vendedor_created_at AS created_at, vendedor_updated_at AS updated_at, vendedor_is_admin AS is_admin, vendedor_dolar AS dolar FROM vendedor WHERE vendedor_deleted = 0 ORDER BY vendedor_name ASC";
        $result = $conn->query($query);
        $sellers = [];
        while ($row = $result->fetch_assoc()) {
            $seller = new Seller();
            $seller->setId($row['id']);
            $seller->setName($row['name']);
            $seller->setCode($row['code']);
            $seller->setEmail($row['email']);
            $seller->setDeleted($row['deleted']);
            $seller->setCreatedAt($row['created_at']);
            $seller->setUpdatedAt($row['updated_at']);
            $seller->setIsAdmin((bool)$row['is_admin']);
            $seller->setDolar((float)$row['dolar']);
            $sellers[] = $seller;
        }
        return $sellers;
    }

    public function getIsAdmin(): bool {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void {
        $this->isAdmin = $isAdmin;
    }

    public function getDolar(): float {
        return $this->dolar;
    }

    public function setDolar(float $dolar): void {
        $this->dolar = $dolar;
    }
}
