<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Promotion implements JsonSerializable {
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var Image|null
     */
    private ?Image $image;

    /**
     *
     */
    public function __construct() {
        $this->id = 0;
        $this->description = '';
        $this->image = null;
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
     * @return Image|null
     */
    public function getImage(): ?Image {
        return $this->image;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): Promotion {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): Promotion {
        $this->description = $description;
        return $this;
    }

    /**
     * @param Image|null $image
     *
     * @return $this
     */
    public function setImage(?Image $image): Promotion {
        $this->image = $image;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public static function getPromotions(): array {
        $conn = Connection::getConn();

        $query = "SELECT p.promocion_id AS id,
       p.promocion_description AS description,
       i.imagen_id AS image_id,
       i.imagen_file_path AS file_path
FROM promotions p
         LEFT JOIN promotions_images pi on p.promocion_id = pi.promocion_id
         LEFT JOIN imagenes i ON pi.imagen_id = i.imagen_id";

        $conn->real_query($query);

        $result = $conn->store_result();

        $promotions = [];

        while ($row = $result->fetch_assoc()) {
            $promotion = new Promotion();
            $promotion->setId($row['id']);
            $promotion->setDescription($row['description']);

            if (!empty($row['image_url'])) {
                $promotion->setImage(new Image());
                $promotion->getImage()->setId($row['image_id']);
                $promotion->getImage()->setFilePath($row['file_path']);
            }

            $promotions[] = $promotion;
        }

        $result->free();

        return $promotions;
    }

    /**
     * @param int $promotionId
     *
     * @return Promotion|null
     */
    public static function getPromotionById(int $promotionId): ?Promotion {
        $conn = Connection::getConn();

        $query = "SELECT p.promocion_id AS id,
       p.promocion_description AS description,
       i.imagen_id  AS image_id,
       i.imagen_file_path AS file_path
FROM promotions p
         LEFT JOIN promotions_images pi on p.promocion_id = pi.promocion_id
         LEFT JOIN imagenes i ON pi.imagen_id = i.imagen_id
WHERE p.promocion_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('i', $promotionId);

        $stmt->execute();

        $result = $stmt->get_result();

        $promotion = null;

        if ($row = $result->fetch_assoc()) {
            $promotion = new Promotion();
            $promotion->setId($row['id']);
            $promotion->setDescription($row['description']);

            if (!empty($row['image_url'])) {
                $promotion->setImage(new Image());
                $promotion->getImage()->setId($row['image_id']);
                $promotion->getImage()->setFilePath($row['file_path']);
            }
        }

        $result->free();

        return $promotion;
    }

    /**
     * @param Promotion $promotion
     *
     * @return void
     */
    public static function createPromotion(Promotion $promotion): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO promotions (promocion_description) VALUE (?)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('s', $promotion->description);

        $stmt->execute();

        $promotion->setId($stmt->insert_id);
    }

    /**
     * @param Promotion $promotion
     *
     * @return void
     */
    public static function updatePromotion(Promotion $promotion): void {
        $conn = Connection::getConn();

        $query = "UPDATE promotions SET promocion_description=? WHERE promocion_id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('si', $promotion->description, $promotion->id);

        $stmt->execute();
    }

    /**
     * @param Promotion $promotion
     *
     * @return void
     */
    public static function deletePromotion(Promotion $promotion): void {
        $conn = Connection::getConn();

        $query = "DELETE FROM promotions WHERE promocion_id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('i', $promotion->id);

        $stmt->execute();
    }

    /**
     * @param int $promotionId
     * @param int $imageId
     *
     * @return void
     */
    public static function assignImage(int $promotionId, int $imageId): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO promotions_images (promotion_id, image_id) VALUE (?,?)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('ii', $promotionId, $imageId);

        $stmt->execute();
    }

    /**
     * @param int $promotionId
     * @param int $imageId
     *
     * @return void
     */
    public static function unassignImage(int $promotionId, int $imageId): void {
        $conn = Connection::getConn();

        $query = "DELETE FROM promotions_images WHERE promotion_id=? AND image_id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('ii', $promotionId, $imageId);

        $stmt->execute();
    }
}