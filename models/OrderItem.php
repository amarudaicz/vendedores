<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class OrderItem implements JsonSerializable {
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var float
     */
    private float $price;

    /**
     * @var int|float
     */
    private int $quantity;

    /**
     * @var string
     */
    private string $productCode;

    /**
     * @var int|string
     */
    private int $orderId;

    private ?int $discount;
    private int $arsUsd;

    /**
     *
     */
    public function __construct() {
        $this->id = 0;
        $this->description = '';
        $this->price = 0.0;
        $this->quantity = 0.0;
        $this->productCode = '';
        $this->orderId = 0;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getProductCode(): string {
        return $this->productCode;
    }

    /**
     * @return int
     */
    public function getOrderId(): int {
        return $this->orderId;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): OrderItem {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): OrderItem {
        $this->description = $description;
        return $this;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price): OrderItem {
        $this->price = $price;
        return $this;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity(int $quantity): OrderItem {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param string $productCode
     *
     * @return $this
     */
    public function setProductCode(string $productCode): OrderItem {
        $this->productCode = $productCode;
        return $this;
    }

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId(int $orderId): OrderItem {
        $this->orderId = $orderId;
        return $this;
    }

    public function setDiscount(int $discount){
        if($discount > 0){
            $this->discount = $discount;
        }else{
            $this->discount = 0;
        }
    }

    public function getDiscount(){
        return $this->discount;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }

    public function getArsUsd(){
        return $this->arsUsd;
    }

    public function setArsUsd($arsUsd){
        $this->arsUsd = $arsUsd;
    }

    /**
     * @param int $oderId
     *
     * @return array
     */
    public static function getOrderItems(int $orderId): array {
        $conn = Connection::getConn();
    
        $query = "SELECT orden_item_id AS id, orden_item_description AS description, orden_item_price AS price, orden_item_quantity AS quantity, orden_item_articulo_code AS product_code, orden_item_orden_id AS order_id
                  FROM ordenes_items 
                  WHERE orden_item_orden_id=?";
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $orderItems = [];
    
        while (($row = $result->fetch_assoc())) {
            $orderItem = new OrderItem();
            $orderItem->setId($row['id']);
            $orderItem->setDescription($row['description']);
            $orderItem->setPrice($row['price']);
            $orderItem->setQuantity($row['quantity']);
            $orderItem->setProductCode($row['product_code']);
            $orderItem->setOrderId($row['order_id']);
    
            $orderItems[] = $orderItem;
        }
    
        $result->free();
        return $orderItems;
    }
    

    /**
     * @param OrderItem $orderItem
     *
     * @return void
     */
    public static function createOrderItem(OrderItem $orderItem): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO ordenes_items (orden_item_description, orden_item_price, orden_item_quantity, orden_item_articulo_code, orden_item_orden_id) VALUES (?,?,?,?,?)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('sddsi',
            $orderItem->description,
            $orderItem->price,
            $orderItem->quantity,
            $orderItem->productCode,
            $orderItem->orderId,
        );

        $stmt->execute();
    }

    /**
     * Elimina todos los items de una orden
     * @param int $orderId
     * @return bool
     */
    public static function deleteOrderItems(int $orderId): bool {
        $conn = Connection::getConn();

        $stmt = $conn->prepare("DELETE FROM ordenes_items WHERE orden_item_orden_id = ?");

        $stmt->bind_param("i", $orderId);

        return $stmt->execute();
    }
}