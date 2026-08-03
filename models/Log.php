<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Log implements JsonSerializable {
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     *
     */
    public function __construct() {
        $this->id = 0;
        $this->description = '';
        $this->createdAt = '';
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
     * @return string
     */
    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): Log {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): Log {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Log {
        $this->createdAt = $createdAt;
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
    public static function getLog(): array {
        $conn = Connection::getConn();

        $query = "SELECT id, description, created_at FROM log ORDER BY id DESC";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $logs = [];

        while ($row = $result->fetch_assoc()) {
            $log = new Log();
            $log->setId($row["id"]);
            $log->setDescription($row["description"]);
            $log->setCreatedAt($row["created_at"]);
            $logs[] = $log;
        }

        $result->free();

        return $logs;
    }

    /**
     * @param Log $log
     * @return void
     */
    public static function createLog(Log $log): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO log(description, created_at) VALUES(?, CURRENT_TIMESTAMP)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $log->description);

        $stmt->execute();

        $log->setId($stmt->insert_id);
    }
}