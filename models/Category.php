<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Category implements JsonSerializable {
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
     *
     * @return $this
     */
    public function setCode(int|string|null $code): Category {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): Category { 
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Category {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt(string $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }

    /**
     * @param Category $category
     *
     * @return void
     */
    public static function createUpdateCategory(Category $category): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO generos (genero_code, genero_name, genero_created_at, genero_updated_at) VALUE (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE genero_name = ?, genero_updated_at=NOW()";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("iss",
            $category->code,
            $category->name,
            $category->name,
        );

        $stmt->execute();
    }
}