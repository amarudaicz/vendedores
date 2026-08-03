<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Image implements JsonSerializable {
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
    private string $description;

    /**
     * @var string
     */
    private string $filePath;

    /**
     * @var int
     */
    private int $fileSize;

    /**
     * @var string
     */
    private string $fileType;

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
        $this->id = 0;
        $this->name = '';
        $this->description = '';
        $this->filePath = '';
        $this->fileSize = 0;
        $this->fileType = '';
        $this->createdAt = '';
        $this->updatedAt = '';
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
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFilePath(): string {
        return $this->filePath;
    }

    /**
     * @return int
     */
    public function getFileSize(): int {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getFileType(): string {
        return $this->fileType;
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
     * @param int $id
     * @return $this
     */
    public function setId(int $id): Image {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Image {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): Image {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setFilePath(string $filePath): Image {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @param int $fileSize
     * @return $this
     */
    public function setFileSize(int $fileSize): Image {
        $this->fileSize = $fileSize;
        return $this;
    }

    /**
     * @param string $fileType
     * @return $this
     */
    public function setFileType(string $fileType): Image {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Image {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): Image {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        if (!empty($this->filePath))
            $this->filePath = '/' . $this->filePath;

        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public static function getImages(): array {
        $conn = Connection::getConn();

        $query = "SELECT imagen_id AS id,
       imagen_name AS name,
       imagen_description AS description,
       imagen_file_path AS file_path,
       imagen_file_size AS file_size,
       imagen_file_type AS file_type,
       imagen_created_at AS created_at,
       imagen_updated_at AS updated_at
FROM imagenes
ORDER BY imagen_id DESC";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $images = [];

        while ($row = $result->fetch_assoc()) {
            $image = new Image();
            $image->setId($row["id"]);
            $image->setName($row["name"]);
            $image->setDescription($row["description"]);
            $image->setFilePath($row["file_path"]);
            $image->setFileSize($row["file_size"]);
            $image->setFileType($row["file_type"]);
            $image->setCreatedAt($row["created_at"]);
            $image->setUpdatedAt($row["updated_at"]);
            $images[] = $image;
        }

        $result->free();

        return $images;
    }

    /**
     * @param int $id
     *
     * @return Image|null
     */
    public static function getImageById(int $id): ?Image {
        $conn = Connection::getConn();

        $query = "SELECT imagen_id AS id,
       imagen_name AS name,
       imagen_description AS description,
       imagen_file_path AS file_path,
       imagen_file_size AS file_size,
       imagen_file_type AS file_type,
       imagen_created_at AS created_at,
       imagen_updated_at AS updated_at
FROM imagenes
WHERE imagen_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $image = null;

        if ($row = $result->fetch_assoc()) {
            $image = new Image();
            $image->setId($row["id"]);
            $image->setName($row["name"]);
            $image->setDescription($row["description"]);
            $image->setFilePath($row["file_path"]);
            $image->setFileSize($row["file_size"]);
            $image->setFileType($row["file_type"]);
            $image->setCreatedAt($row["created_at"]);
            $image->setUpdatedAt($row["updated_at"]);
        }

        $result->free();

        return $image;
    }

    /**
     * @param Image $image
     *
     * @return void
     */
    public static function createImage(Image $image): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO imagenes (imagen_id, imagen_name, imagen_description, imagen_file_path, imagen_file_size, imagen_file_type, imagen_created_at, imagen_updated_at) VALUES(?,?,?,?,?,?,NOW(),NOW())";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("isssis",
            $image->id,
            $image->name,
            $image->description,
            $image->filePath,
            $image->fileSize,
            $image->fileType
        );

        $stmt->execute();

        $image->setId($stmt->insert_id);
    }

    /**
     * @param Image $image
     * @return void
     */
    public static function updateImage(Image $image): void {
        $conn = Connection::getConn();

        $query = "UPDATE imagenes SET imagen_name = ?, imagen_description = ?, imagen_file_size=?, imagen_file_type=?, imagen_updated_at = NOW() WHERE imagen_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("ssdsi",
            $image->name,
            $image->description,
            $image->fileSize,
            $image->fileType,
            $image->id
        );

        $stmt->execute();
    }

    /**
     * @param Image $image
     *
     * @return void
     */
    public static function deleteImage(Image $image): void {
        $conn = Connection::getConn();

        $query = "DELETE FROM imagenes WHERE imagen_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $image->id);

        $stmt->execute();
    }

    /**
     * @param string $productCode
     *
     * @return Image|null
     */
    public static function getImageAssignedToProductCode(string $productCode): ?Image {
        $conn = Connection::getConn();

        $query = "SELECT i.imagen_id AS id,
       i.imagen_name AS name,
       i.imagen_description AS description,
       i.imagen_file_path AS file_path,
       i.imagen_file_size AS file_size,
       i.imagen_file_type AS file_type,
       i.imagen_created_at AS created_at,
       i.imagen_updated_at AS updated_at
FROM imagenes i,
     articulos_images pi
WHERE i.imagen_id = pi.articulos_image_image_id
  AND pi.articulos_image_articulo_code = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $productCode);

        $stmt->execute();

        $result = $stmt->get_result();

        $image = null;

        if ($row = $result->fetch_assoc()) {
            $image = new Image();
            $image->setId($row["id"]);
            $image->setName($row["name"]);
            $image->setDescription($row["description"]);
            $image->setFilePath($row["file_path"]);
            $image->setFileSize($row["file_size"]);
            $image->setFileType($row["file_type"]);
            $image->setCreatedAt($row["created_at"]);
            $image->setUpdatedAt($row["updated_at"]);
        }

        $result->free();

        return $image;
    }

    /**
     * @param int $promotionId
     *
     * @return Image|null
     */
    public static function getImageAssignedToPromotionId(int $promotionId): ?Image {
        $conn = Connection::getConn();

        $query = "SELECT i.imagen_id AS id,
       i.imagen_name AS name,
       i.imagen_description AS description,
       i.imagen_file_path AS file_path,
       i.imagen_file_size AS file_size,
       i.imagen_file_type AS file_type,
       i.imagen_created_at AS created_at,
       i.imagen_updated_at AS updated_at
FROM imagenes i,
     promotions_images pi
WHERE i.imagen_id = pi.image_id
  AND pi.promotion_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $promotionId);

        $stmt->execute();

        $result = $stmt->get_result();

        $image = null;

        if ($row = $result->fetch_assoc()) {
            $image = new Image();
            $image->setId($row["id"]);
            $image->setName($row["name"]);
            $image->setDescription($row["description"]);
            $image->setFilePath($row["file_path"]);
            $image->setFileSize($row["file_size"]);
            $image->setFileType($row["file_type"]);
            $image->setCreatedAt($row["created_at"]);
            $image->setUpdatedAt($row["updated_at"]);
        }

        $result->free();

        return $image;
    }
}