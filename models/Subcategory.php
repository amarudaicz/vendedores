<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Subcategory implements JsonSerializable {
    /**
     * @var int
     */
    private int|string|null $code;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     * @var string
     */
    private string $updatedAt;

    /**
     *
     */
    public function __construct() {
        $this->code = 0;
        $this->name = '';
        $this->createdAt = '';
        $this->updatedAt = '';
    }

    /**
     * @return int
     */
    public function getCode(): int|string|null {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int|string|null $code): Subcategory {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Subcategory {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Subcategory {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): Subcategory {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }

    /**
     * @param Subcategory $subcategory
     *
     * @return void
     */
    public static function createUpdateCategory(Subcategory $subcategory): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO familias (familia_code, familia_name, familia_created_at, familia_updated_at) VALUE (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE familia_name = ?, familia_updated_at=NOW()";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("iss",
            $subcategory->code,
            $subcategory->name,
            $subcategory->name,
        );

        $stmt->execute();
    }
}