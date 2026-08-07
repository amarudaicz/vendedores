<?php

namespace models;

use Exception;
use helpers\Logger;
use JsonSerializable;

/**
 *
 */
class Order implements JsonSerializable
{
    /**
     *
     */
    const STATUS_PENDING = 'pending';
    const STATUS_FINALIZED = 'finalized';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_NOT_REALIZED = 'not_realized';
    const STATUS_IN_PROGRESS = 'in_progress';

    /**
     * Transiciones válidas (forward-only). Cada estado solo se puede alcanzar una vez.
     */
    const STATUS_FLOW = [
        self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_NOT_REALIZED],
        self::STATUS_CONFIRMED => [self::STATUS_IN_PROGRESS],
        self::STATUS_IN_PROGRESS => [self::STATUS_FINALIZED],
        self::STATUS_FINALIZED => [],
        self::STATUS_NOT_REALIZED => [],
    ];

    /**
     * Etiquetas legibles de cada estado.
     */
    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_CONFIRMED => 'Confirmado',
        self::STATUS_IN_PROGRESS => 'En proceso',
        self::STATUS_FINALIZED => 'Enviado',
        self::STATUS_NOT_REALIZED => 'No Concretada',
    ];

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string|null $paymentMethod;

    /**
     * @var string
     */
    private ?string $note;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     * @var string
     */
    private string $updatedAt;

    /**
     * @var int|null
     */
    private ?int $customerCode;

    /**
     * @var int|null
     */
    private ?int $customerZone;

    private ?int $guestId;

    private ?int $sellerCode;

    /**
     * @var Customer|null
     */
    private ?Customer $customer;

    private ?Guest $guest;

    /**
     * @var float|null
     */
    private ?float $total;


    /**
     * @var float|null
     */
    private ?float $cotizacion;

    /**
     *
     */
    public function __construct()
    {
        $this->id = 0;
        $this->paymentMethod = '';
        $this->note = '';
        $this->status = self::STATUS_PENDING;
        $this->createdAt = '';
        $this->updatedAt = '';
        $this->customerCode = null;
        $this->customerZone = null;
        $this->guestId = null;
        $this->sellerCode = null;
        $this->customer = null;
        $this->guest = null;
        $this->total = 0.0;
        $this->cotizacion = 0.0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @return string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getCustomerCode(): ?int
    {
        return $this->customerCode;
    }

    /**
     * @return int|null
     */
    public function getCustomerZone(): ?int
    {
        return $this->customerZone;
    }

    public function getGuestId(): ?int
    {
        return $this->guestId;
    }

    public function getSellerCode(): ?int
    {
        return $this->sellerCode;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getGuest(): ?Guest
    {
        return $this->guest;
    }

    /**
     * @return float|null
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @return float|null
     */
    public function getCotizacion(): ?float
    {
        return $this->cotizacion;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $paymentMethod
     *
     * @return $this
     */
    public function setPaymentMethod(?string $paymentMethod): Order
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @param string $note
     *
     * @return $this
     */
    public function setNote(?string $note): Order
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): Order
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Order
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): Order
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @param int $customerCode
     *
     * @return $this
     */
    public function setCustomerCode(?int $customerCode): Order
    {
        $this->customerCode = $customerCode;
        return $this;
    }

    /**
     * @param int $customerZone
     *
     * @return $this
     */
    public function setCustomerZone(?int $customerZone): Order
    {
        $this->customerZone = $customerZone;
        return $this;
    }

    public function setGuestId(?int $guestId): Order
    {
        $this->guestId = $guestId;
        return $this;
    }

    public function setSellerCode(?int $sellerCode): Order
    {
        $this->sellerCode = $sellerCode;
        return $this;
    }

    /**
     * @param Customer|null $customer
     * @return $this
     */
    public function setCustomer(?Customer $customer): Order
    {
        $this->customer = $customer;
        return $this;
    }

    public function setGuest(?Guest $guest): Order
    {
        $this->guest = $guest;
        return $this;
    }

    /**
     * @param float|null $total
     * @return $this
     */
    public function setTotal(?float $total): Order
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @param float|null $cotizacion
     * @return $this
     */
    public function setCotizacion(?float $cotizacion): Order
    {
        $this->cotizacion = $cotizacion;
        return $this;
    }

    /**
     * Determina si se permite transicionar del estado actual al nuevo.
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function canChangeStatus(string $current, string $new): bool
    {
        return in_array($new, self::STATUS_FLOW[$current] ?? [], true);
    }

    /**
     * Devuelve la etiqueta legible de un estado.
     * @param string $status
     * @return string
     */
    public static function getStatusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
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
    public static function getOrders(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT o.orden_id AS id,
       o.orden_payment_method AS payment_method,
       o.orden_note AS note,
       o.orden_status AS status,
       o.orden_created_at AS created_at,
       o.orden_updated_at AS updated_at,
       o.orden_cliente_code AS customer_code,
       o.orden_cliente_zone AS customer_zone,
       o.orden_guest_id AS guest_id,
       c.cliente_name AS customer_name,
       g.guest_name AS guest_name,
       SUM(oi.orden_item_price * oi.orden_item_quantity) AS total
FROM ordenes o
    LEFT JOIN clientes c ON c.cliente_code = o.orden_cliente_code AND c.cliente_zone = o.orden_cliente_zone AND c.cliente_deleted = 0
    LEFT JOIN guests g ON g.guest_id = o.orden_guest_id
    LEFT JOIN ordenes_items oi ON o.orden_id = oi.orden_item_orden_id
GROUP BY o.orden_id
ORDER BY o.orden_id DESC";

        $conn->real_query($query);

        $result = $conn->store_result();

        $orders = [];

        while (($row = $result->fetch_assoc())) {
            $order = new Order();
            $order->setId($row["id"]);
            $order->setPaymentMethod($row["payment_method"]);
            $order->setNote($row["note"]);
            $order->setStatus($row["status"]);
            $order->setCreatedAt($row["created_at"]);
            $order->setUpdatedAt($row["updated_at"]);
            $order->setCustomerCode($row["customer_code"]);
            $order->setCustomerZone($row["customer_zone"]);
            $order->setGuestId($row["guest_id"]);
            $order->setTotal($row["total"]);

            if (!empty($row["customer_code"])) {
                $order->setCustomer(new Customer());
                $order->getCustomer()->setCode($row["customer_code"]);
                $order->getCustomer()->setName($row["customer_name"]);
            }

            if (!empty($row["guest_id"])) {
                $order->setGuest(new Guest());
                $order->getGuest()->setId($row["guest_id"]);
                $order->getGuest()->setName($row["guest_name"]);
            }

            $orders[] = $order;
        }

        $result->free();

        return $orders;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public static function getOrdersByDate(string $from, string $to): array
    {
        $conn = Connection::getConn();

        $query = "SELECT o.orden_id AS id,
       o.orden_payment_method AS payment_method,
       o.orden_note AS note,
       o.orden_status AS status,
       o.orden_created_at AS created_at,
       o.orden_updated_at AS updated_at,
       o.orden_cliente_code AS customer_code,
       o.orden_cliente_zone AS customer_zone,
       o.orden_guest_id AS guest_id,
       c.cliente_name AS customer_name,
       g.guest_name AS guest_name
FROM ordenes o
LEFT JOIN clientes c ON c.cliente_code = o.orden_cliente_code AND c.cliente_zone = o.orden_cliente_zone AND c.cliente_deleted = 0
LEFT JOIN guests g ON g.guest_id = o.orden_guest_id
WHERE DATE(o.orden_created_at) BETWEEN ? AND ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("ss", $from, $to);

        $stmt->execute();

        $result = $stmt->get_result();

        $orders = [];

        while (($row = $result->fetch_assoc())) {
            $order = new Order();
            $order->setId($row["id"]);
            $order->setPaymentMethod($row["payment_method"]);
            $order->setNote($row["note"]);
            $order->setStatus($row["status"]);
            $order->setCreatedAt($row["created_at"]);
            $order->setUpdatedAt($row["updated_at"]);
            $order->setCustomerCode($row["customer_code"]);
            $order->setCustomerZone($row["customer_zone"]);
            $order->setGuestId($row["guest_id"]);

            if (!empty($row["customer_code"])) {
                $order->setCustomer(new Customer());
                $order->getCustomer()->setCode($row["customer_code"]);
                $order->getCustomer()->setName($row["customer_name"]);
            }


            if (!empty($row["guest_id"])) {
                $order->setGuest(new Guest());
                $order->getGuest()->setId($row["guest_id"]);
                $order->getGuest()->setName($row["guest_name"]);
            }

            $orders[] = $order;
        }

        $result->free();

        return $orders;
    }

    /**
     * @param int $year
     * @return array
     */
    public static function getOrdersByYear(int $year): array
    {
        $conn = Connection::getConn();

        $query = "SELECT o.orden_id AS id,
       o.orden_payment_method AS payment_method,
       o.orden_note AS note,
       o.orden_status AS status,
       o.orden_created_at AS created_at,
       o.orden_updated_at AS updated_at,
       o.orden_cliente_code AS customer_code,
       o.orden_cliente_zone AS customer_zone,
       o.orden_guest_id AS guest_id,
       SUM(oi.orden_item_price * oi.orden_item_quantity) AS total
FROM ordenes o
    LEFT JOIN ordenes_items oi ON o.orden_id = oi.orden_item_orden_id
WHERE YEAR(o.orden_created_at) = ?
GROUP BY o.orden_id";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $year);

        $stmt->execute();

        $result = $stmt->get_result();

        $orders = [];

        while (($row = $result->fetch_assoc())) {
            $order = new Order();
            $order->setId($row["id"]);
            $order->setPaymentMethod($row["payment_method"]);
            $order->setNote($row["note"]);
            $order->setStatus($row["status"]);
            $order->setCreatedAt($row["created_at"]);
            $order->setUpdatedAt($row["updated_at"]);
            $order->setCustomerCode($row["customer_code"]);
            $order->setCustomerZone($row["customer_zone"]);
            $order->setGuestId($row["guest_id"]);
            $order->setTotal($row['total']);

            $orders[] = $order;
        }

        $result->free();

        return $orders;
    }

    /**
     * @param int $orderId
     *
     * @return Order|null
     */
    public static function getOrderById(int $orderId): ?Order
    {
        $conn = Connection::getConn();

        $query = "SELECT o.orden_id AS id,
        o.orden_payment_method AS payment_method,
        o.orden_note AS note,
        o.orden_status AS status,
        o.orden_created_at AS created_at,
        o.orden_updated_at AS updated_at,
        o.orden_cliente_code AS customer_code,
        o.orden_cliente_zone AS customer_zone,
        o.orden_guest_id AS guest_id,
        o.orden_vendedor_code AS seller_code,
        o.orden_cotizacion AS cotizacion
        FROM ordenes o
        WHERE o.orden_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $orderId);

        $stmt->execute();

        $result = $stmt->get_result();

        $order = null;

        if (($row = $result->fetch_assoc())) {
            $order = new Order();
            $order->setId($row["id"]);
            $order->setPaymentMethod($row["payment_method"]);
            $order->setNote($row["note"]);
            $order->setStatus($row["status"]);
            $order->setCreatedAt($row["created_at"]);
            $order->setUpdatedAt($row["updated_at"]);
            $order->setCustomerCode($row["customer_code"]);
            $order->setCustomerZone($row["customer_zone"]);
            $order->setGuestId($row["guest_id"]);
            $order->setSellerCode($row["seller_code"]);
            $order->setCotizacion($row["cotizacion"]);
        }

        $result->free();

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return void
     */
    public static function createOrder(Order $order, ?int $seller_code = null): void
    {
        $conn = Connection::getConn();

        $query = "INSERT INTO ordenes (orden_payment_method, orden_note, orden_status, orden_created_at, orden_updated_at, orden_cliente_code, orden_cliente_zone, orden_guest_id, orden_cotizacion, orden_vendedor_code) VALUES (?,?,?,NOW(),NOW(),?,?,?,?,?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new \Exception("Error preparando consulta: " . $conn->error);
        }

        $stmt->bind_param(
            'sssiiiid',
            $order->paymentMethod,
            $order->note,
            $order->status,
            $order->customerCode,
            $order->customerZone,
            $order->guestId,
            $order->cotizacion,
            $seller_code,
        );

        $stmt->execute();

        $order->setId($stmt->insert_id);
    }

    /**
     * @param Order $order
     * @return void
     */
    public static function updateOrder(Order $order): void
    {
        $conn = Connection::getConn();

        $status = trim((string)($order->status ?? 'pending'));

        if ($status === '') {
            $status = 'pending';
        }

        if (strlen($status) > 50) {
            throw new Exception('Estado inválido');
        }

        $query = "UPDATE ordenes SET orden_status = ?, orden_updated_at = NOW() WHERE orden_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $order->id);
        $stmt->execute();
    }

    public static function getOrdersBySeller(?int $seller_code, array $filters = [], $page = 1, $perPage = 10)
    {
        $conn = Connection::getConn();

        // Construcción de la consulta principal
        $query = "SELECT o.orden_id AS id,
            o.orden_payment_method AS payment_method,
            o.orden_note AS note,
            o.orden_status AS status,
            o.orden_vendedor_code AS seller_code,
            o.orden_created_at AS created_at,
            o.orden_updated_at AS updated_at,
            o.orden_cliente_code AS customer_code,
            o.orden_cliente_zone AS customer_zone,
            o.orden_guest_id AS guest_id,
            COALESCE(c.cliente_name, g.guest_name) AS customer_name,
            COALESCE(c.cliente_dni, g.guest_tin) AS customer_dni,
            s.vendedor_name AS seller_name,
            SUM(oi.orden_item_price * oi.orden_item_quantity) AS total
        FROM ordenes o
        LEFT JOIN clientes c ON o.orden_cliente_code = c.cliente_code AND o.orden_cliente_zone = c.cliente_zone AND c.cliente_deleted = 0
        LEFT JOIN guests g ON o.orden_guest_id = g.guest_id
        LEFT JOIN ordenes_items oi ON o.orden_id = oi.orden_item_orden_id
        LEFT JOIN vendedor s ON o.orden_vendedor_code = s.vendedor_code
        WHERE 1=1";

        // Inicializar parámetros
        $params = [];
        $types = "";

        if (!empty($seller_code) && $seller_code !== null && $seller_code !== "") {
            $query .= " AND o.orden_vendedor_code = ?";
            $params[] = $seller_code;
            $types .= "i";
        }

        // Filtro por búsqueda general (ID de orden o nombre de cliente)
        if (!empty($filters['search'])) {
            $query .= " AND (o.orden_id = ? OR c.cliente_name LIKE ? OR c.cliente_dni LIKE ?)";
            $searchId = is_numeric($filters['search']) ? (int)$filters['search'] : 0;
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchId;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "iss";
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query .= " AND o.orden_status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        // Filtro por fecha desde
        if (!empty($filters['dateFrom'])) {
            $query .= " AND DATE(o.orden_created_at) >= ?";
            $params[] = $filters['dateFrom'];
            $types .= "s";
        }

        // Filtro por fecha hasta
        if (!empty($filters['dateTo'])) {
            $query .= " AND DATE(o.orden_created_at) <= ?";
            $params[] = $filters['dateTo'];
            $types .= "s";
        }

        // Filtro por customer_code (para búsqueda específica de cliente)
        if (!empty($filters['customer_code'])) {
            $query .= " AND o.orden_cliente_code = ?";
            $params[] = $filters['customer_code'];
            $types .= "i";
        }

        // Finalizar la consulta con paginación
        $query .= " GROUP BY o.orden_id
        ORDER BY o.orden_created_at DESC
        LIMIT ? OFFSET ?";

        // Calcular offset para la paginación
        $offset = ($page - 1) * $perPage;

        // Añadir limit y offset a los parámetros
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";

        // Preparar y ejecutar la consulta principal
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);

        // Construcción de la consulta para contar resultados
        $countQuery = "SELECT COUNT(DISTINCT o.orden_id) as total 
        FROM ordenes o 
        LEFT JOIN clientes c ON o.orden_cliente_code = c.cliente_code
        WHERE 1=1";

        // Reinicializar parámetros para el conteo
        $countParams = [];
        $countTypes = "";

        if (!empty($seller_code) && $seller_code !== null && $seller_code !== "") {
            $countQuery .= " AND o.orden_vendedor_code = ?";
            $countParams[] = $seller_code;
            $countTypes .= "i";
        }

        // Aplicar los mismos filtros en el conteo
        if (!empty($filters['search'])) {
            $countQuery .= " AND (o.orden_id = ? OR c.cliente_name LIKE ? OR c.cliente_dni LIKE ?)";
            $searchId = is_numeric($filters['search']) ? (int)$filters['search'] : 0;
            $searchTerm = '%' . $filters['search'] . '%';
            $countParams[] = $searchId;
            $countParams[] = $searchTerm;
            $countParams[] = $searchTerm;
            $countTypes .= "iss";
        }

        if (!empty($filters['status'])) {
            $countQuery .= " AND o.orden_status = ?";
            $countParams[] = $filters['status'];
            $countTypes .= "s";
        }

        if (!empty($filters['dateFrom'])) {
            $countQuery .= " AND DATE(o.orden_created_at) >= ?";
            $countParams[] = $filters['dateFrom'];
            $countTypes .= "s";
        }

        if (!empty($filters['dateTo'])) {
            $countQuery .= " AND DATE(o.orden_created_at) <= ?";
            $countParams[] = $filters['dateTo'];
            $countTypes .= "s";
        }

        if (!empty($filters['customer_code'])) {
            $countQuery .= " AND o.orden_cliente_code = ?";
            $countParams[] = $filters['customer_code'];
            $countTypes .= "i";
        }

        // Preparar y ejecutar la consulta de conteo
        $countStmt = $conn->prepare($countQuery);
        if (!empty($countTypes)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();

        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];

        // Liberar recursos
        $countResult->free();
        $result->free();
        $stmt->close();
        $countStmt->close();

        // Retornar los resultados con paginación
        return [
            'orders' => $orders,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
            'total' => $total
        ];
    }

    /**
     * Elimina físicamente una orden.
     * @param int $orderId
     * @return bool
     */
    public static function deleteOrder(int $orderId): bool
    {
        $conn = Connection::getConn();

        $stmt = $conn->prepare("DELETE FROM ordenes WHERE orden_id = ?");
        $stmt->bind_param("i", $orderId);

        return $stmt->execute();
    }
}
