<?php

namespace models;

use api\exceptions\ApiException;
use helpers\Logger;
use JsonSerializable;

/**
 *
 */
class Customer implements JsonSerializable
{
    /**
     * @var int
     */
    private int $code;

    /**
     * @var string
     */
    private string|null $name;

    /**
     * @var string
     */
    private string $dni;

    /**
     * @var int
     */
    private int $zone;

    /**
     * @var string
     */
    private string $passwordSalt;

    /**
     * @var string
     */
    private string $passwordHash;

    /**
     * @var int
     */
    private int $priceList;

    /**
     * @var int
     */
    private int $sellerCode;

    /**
     * @var string|null
     */
    private string|null $email;


    /**
     * @var bool
     */
    private int $deleted;

    /**
     * @var string
     */
    private string $updatedAt;

    /**
     *
     */
    public function __construct()
    {
        $this->code = 0;
        $this->name = '';
        $this->dni = '';
        $this->zone = 0;
        $this->passwordSalt = '';
        $this->passwordHash = '';
        $this->priceList = 0;
        $this->deleted = false;
        $this->updatedAt = '';
        $this->sellerCode = 0;
        $this->email = '';
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string|null
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDni(): string
    {
        return $this->dni;
    }

    /**
     * @return int
     */
    public function getZone(): int
    {
        return $this->zone;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string
    {
        return $this->passwordSalt;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return int
     */
    public function getPriceList(): int
    {
        return $this->priceList;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getSellerCode(): int
    {
        return $this->sellerCode;
    }

    public function getEmail(): string|null
    {
        return $this->email;
    }

    public function setEmail(string|null $email): Customer
    {
        $this->email = $email;
        return $this;
    }

    public function setSellerCode(int $sellerCode): Customer
    {
        $this->sellerCode = $sellerCode;
        return $this;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode(int $code): Customer
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string|null $name): Customer
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $dni
     *
     * @return $this
     */
    public function setDni(string $dni): Customer
    {
        $this->dni = $dni;
        return $this;
    }

    /**
     * @param int $zone
     *
     * @return $this
     */
    public function setZone(int $zone): Customer
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * @param string $passwordSalt
     *
     * @return $this
     */
    public function setPasswordSalt(string $passwordSalt): Customer
    {
        $this->passwordSalt = $passwordSalt;
        return $this;
    }

    /**
     * @param string $passwordHash
     *
     * @return $this
     */
    public function setPasswordHash(string $passwordHash): Customer
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     * @param int $priceList
     *
     * @return $this
     */
    public function setPriceList(int $priceList): Customer
    {
        $this->priceList = $priceList;
        return $this;
    }

    /**
     * @param bool $deleted
     * @return $this
     */
    public function setDeleted(bool $deleted): Customer
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): Customer
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public static function getCustomers(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT cliente_code AS code,
       cliente_name AS name,
       cliente_dni AS dni,
       cliente_zone AS zone,
       cliente_password_salt AS password_salt,
       cliente_password_hash AS password_hash,
       cliente_price_list AS price_list,
       cliente_email AS email,
       cliente_deleted AS deleted,
       cliente_updated_at AS updated_at
FROM clientes";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $customers = [];

        while ($row = $result->fetch_assoc()) {
            $customer = new Customer();
            $customer->setCode($row["code"]);
            $customer->setName($row["name"]);
            $customer->setDni($row["dni"]);
            $customer->setZone($row["zone"]);
            $customer->setPasswordSalt($row["password_salt"]);
            $customer->setPasswordHash($row["password_hash"]);
            $customer->setPriceList($row["price_list"]);
            $customer->setDeleted($row["deleted"]);
            $customer->setUpdatedAt($row["updated_at"]);
            $customer->setEmail($row["email"] ?? null);
            $customers[] = $customer;
        }

        $result->free();

        return $customers;
    }

    public static function searchByQuery(?string $seller_code, string $query = ''): array
    {
        $conn = Connection::getConn();
        // Base de la consulta SQL
        $sql = "
            SELECT 
                c.cliente_code AS code,
                c.cliente_name AS name,
                c.cliente_dni AS dni,
                c.cliente_zone AS zone,
                c.cliente_price_list AS price_list,
                c.cliente_email AS email,
                c.cliente_deleted AS deleted,
                c.cliente_updated_at AS updated_at,
                c.cliente_vendedor_code AS seller_code,
                s.vendedor_name AS seller_name
            FROM 
                clientes c
            LEFT JOIN 
                vendedor s ON c.cliente_vendedor_code = s.vendedor_code
            WHERE 
                c.cliente_deleted = 0
        ";

        $params = [];
        $types = "";

        if ($seller_code !== null) {
            $sql .= " AND c.cliente_vendedor_code = ?";
            $params[] = $seller_code;
            $types .= "i";
        }

        // Si el query no está vacío, agregamos la búsqueda por nombre, DNI o código
        if (!empty($query)) {
            $sql .= " AND (c.cliente_name LIKE ? OR c.cliente_dni LIKE ? OR c.cliente_code LIKE ?)";
            $querySearch = '%' . $query . '%';
            $params[] = $querySearch;
            $params[] = $querySearch;
            $params[] = $querySearch;
            $types .= "sss";
        }

        $sql .= " ORDER BY c.cliente_name ASC";

        Logger::log('a', $sql);

        // Preparamos la consulta
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            throw new ApiException('Failed to prepare statement: ' . $conn->error);
        }

        // Si hay parámetros, los bindeamos
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Ejecutamos la consulta
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new ApiException('Failed to execute statement: ' . $stmt->error);
        }

        // Obtenemos los resultados
        $customers = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();

        return $customers;
    }

    /**
     * @param string $lastUpdatedAt
     * @return array
     */
    public static function getUnupdatedCustomers(string $lastUpdatedAt): array
    {
        $conn = Connection::getConn();

        $query = "SELECT cliente_code AS code,
       cliente_name AS name,
       cliente_dni AS dni,
       cliente_zone AS zone,
       cliente_password_salt AS password_salt,
       cliente_password_hash AS password_hash,
       cliente_price_list AS price_list,
       cliente_email AS email,
       cliente_deleted AS deleted,
       cliente_updated_at AS updated_at
FROM clientes
WHERE cliente_updated_at != ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $lastUpdatedAt);

        $stmt->execute();

        $result = $stmt->get_result();

        $customers = [];

        while ($row = $result->fetch_assoc()) {
            $customer = new Customer();
            $customer->setCode($row["code"]);
            $customer->setName($row["name"]);
            $customer->setDni($row["dni"]);
            $customer->setZone($row["zone"]);
            $customer->setPasswordSalt($row["password_salt"]);
            $customer->setPasswordHash($row["password_hash"]);
            $customer->setPriceList($row["price_list"]);
            $customer->setDeleted($row["deleted"]);
            $customer->setUpdatedAt($row["updated_at"]);
            $customer->setEmail($row["email"] ?? null);
            $customers[] = $customer;
        }

        $result->free();

        return $customers;
    }

    /**
     * @param int $code
     *
     * @return Customer|null
     */
    public static function getCustomerByCode(int $code, int $zone): ?Customer
    {
        $conn = Connection::getConn();

        error_log('code: ' . $code);
        error_log('zone: ' . $zone);

        $query = "SELECT cliente_code AS code,
       cliente_name AS name,
       cliente_dni AS dni,
       cliente_zone AS zone,
       cliente_password_salt AS password_salt,
       cliente_password_hash AS password_hash,
       cliente_price_list AS price_list,
       cliente_email AS email,
       cliente_deleted AS deleted,
       cliente_updated_at AS updated_at
        FROM clientes WHERE cliente_code = ? AND cliente_zone = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("ii", $code, $zone);

        $stmt->execute();

        $result = $stmt->get_result();

        $customer = null;

        if ($row = $result->fetch_assoc()) {
            $customer = new Customer();
            $customer->setCode($row["code"]);
            $customer->setName($row["name"]);
            $customer->setDni($row["dni"]);
            $customer->setZone($row["zone"]);
            $customer->setPasswordSalt($row["password_salt"]);
            $customer->setPasswordHash($row["password_hash"]);
            $customer->setPriceList($row["price_list"]);
            $customer->setDeleted($row["deleted"]);
            $customer->setUpdatedAt($row["updated_at"]);
            $customer->setEmail($row["email"] ?? null);
        }

        $result->free();

        return $customer;
    }

    /**
     * @param int $code
     *
     * @return Customer|null
     */
    public static function getCustomerByDNI(string $dni): ?Customer
    {
        $conn = Connection::getConn();

        $query = "SELECT cliente_code AS code,
       cliente_name AS name,
       cliente_dni AS dni,
       cliente_zone AS zone,
       cliente_password_salt AS password_salt,
       cliente_password_hash AS password_hash,
       cliente_price_list AS price_list,
       cliente_email AS email,
       cliente_deleted AS deleted,
       cliente_updated_at AS updated_at
FROM clientes WHERE cliente_dni = ? AND cliente_deleted = FALSE";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $dni);

        $stmt->execute();

        $result = $stmt->get_result();

        $customer = null;

        if ($row = $result->fetch_assoc()) {
            $customer = new Customer();
            $customer->setCode($row["code"]);
            $customer->setName($row["name"]);
            $customer->setDni($row["dni"]);
            $customer->setZone($row["zone"]);
            $customer->setPasswordSalt($row["password_salt"]);
            $customer->setPasswordHash($row["password_hash"]);
            $customer->setPriceList($row["price_list"]);
            $customer->setDeleted($row["deleted"]);
            $customer->setUpdatedAt($row["updated_at"]);
            $customer->setEmail($row["email"] ?? null);
        }

        $result->free();

        return $customer;
    }

    /**
     * @param Customer $customer
     * @return void
     */
    public static function updateCustomer(Customer $customer): void
    {
        $conn = Connection::getConn();

        $query = "UPDATE clientes
SET cliente_name=?,
    cliente_dni=?,
    cliente_zone=?,
    cliente_password_salt=?,
    cliente_password_hash=?,
    cliente_price_list=?,
    cliente_deleted=?,
    cliente_updated_at=?
WHERE cliente_code = ? AND cliente_zone = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param(
            "sssssiisii",
            $customer->name,
            $customer->dni,
            $customer->zone,
            $customer->passwordSalt,
            $customer->passwordHash,
            $customer->priceList,
            $customer->deleted,
            $customer->updatedAt,
            $customer->code,
            $customer->zone
        );

        $stmt->execute();
    }

    public static function createUpdateCustomer(Customer $customer): void
    {
        $conn = Connection::getConn();

        // Primero verificamos si existe el registro con la clave compuesta
        $checkQuery = "SELECT cliente_code FROM clientes WHERE cliente_code = ? AND cliente_zone = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $customer->code, $customer->zone);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $exists = $checkResult->num_rows > 0;
        $checkStmt->close();


        if ($exists) {
            error_log("Updating customer: " . $customer->code);
            // UPDATE si ya existe
            $updateQuery = "UPDATE clientes SET 
                cliente_name = ?, cliente_dni = ?, cliente_zone = ?, cliente_password_salt = ?, 
                cliente_password_hash = ?, cliente_price_list = ?, cliente_deleted = ?, 
                cliente_updated_at = ?, cliente_vendedor_code = ?
            WHERE cliente_code = ? AND cliente_zone = ?";

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param(
                "sssssiissii",
                $customer->name,
                $customer->dni,
                $customer->zone,
                $customer->passwordSalt,
                $customer->passwordHash,
                $customer->priceList,
                $customer->deleted,
                $customer->updatedAt,
                $customer->sellerCode,
                $customer->code,
                $customer->zone
            );
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // INSERT si no existe
            $insertQuery = "INSERT INTO clientes (cliente_code, cliente_name, cliente_dni, cliente_zone, cliente_password_salt, cliente_password_hash, cliente_price_list, cliente_deleted, cliente_updated_at, cliente_vendedor_code)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param(
                "ississiiss",
                $customer->code,
                $customer->name,
                $customer->dni,
                $customer->zone,
                $customer->passwordSalt,
                $customer->passwordHash,
                $customer->priceList,
                $customer->deleted,
                $customer->updatedAt,
                $customer->sellerCode
            );
            $insertStmt->execute();
            $insertStmt->close();
        }
    }
}
