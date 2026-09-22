<?php

namespace models;

use JsonSerializable;

/**
 * Modelo de Transporte para vendedores-nvd
 */
class Transporte implements JsonSerializable {
    /**
     * @var int
     */
    private int $transporte_id;

    /**
     * @var string
     */
    private string $transporte_nombre;

    /**
     * Constructor
     */
    public function __construct() {
        $this->transporte_id = 0;
        $this->transporte_nombre = '';
    }

    /**
     * @return int
     */
    public function getTransporteId(): int {
        return $this->transporte_id;
    }

    /**
     * @param int $transporte_id
     * @return $this
     */
    public function setTransporteId(int $transporte_id): self {
        $this->transporte_id = $transporte_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransporteNombre(): string {
        return $this->transporte_nombre;
    }

    /**
     * @param string $transporte_nombre
     * @return $this
     */
    public function setTransporteNombre(string $transporte_nombre): self {
        $this->transporte_nombre = $transporte_nombre;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return [
            'transporte_id' => $this->transporte_id,
            'transporte_nombre' => $this->transporte_nombre,
        ];
    }

    /**
     * Obtiene todos los transportes de la base de datos ordenados por nombre.
     *
     * @return Transporte[]
     */
    public static function getAll(): array {
        $conn = Connection::getConn();

        $query = "SELECT transporte_id, transporte_nombre FROM transportes ORDER BY transporte_nombre ASC";

        $conn->real_query($query);

        $result = $conn->store_result();

        $transportes = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $transporte = new Transporte();
                $transporte->setTransporteId((int)$row['transporte_id']);
                $transporte->setTransporteNombre($row['transporte_nombre'] ?? '');
                $transportes[] = $transporte;
            }
            $result->free();
        }

        return $transportes;
    }

    /**
     * Obtiene un transporte por su ID.
     *
     * @param int $id
     * @return Transporte|null
     */
    public static function getById(int $id): ?Transporte {
        $conn = Connection::getConn();

        $stmt = $conn->prepare("SELECT transporte_id, transporte_nombre FROM transportes WHERE transporte_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $transporte = new Transporte();
            $transporte->setTransporteId((int)$row['transporte_id']);
            $transporte->setTransporteNombre($row['transporte_nombre'] ?? '');
            $stmt->close();
            return $transporte;
        }

        $stmt->close();
        return null;
    }
}
