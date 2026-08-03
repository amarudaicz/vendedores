<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Cotizacion implements JsonSerializable {
    /**
     * @var int
     */
    private int $id;

    /**
     * @var float
     */
    private float $valor;

    /**
     * @var string
     */
    private string $created_at;

    /**
     * @var string
     */
    private string $updated_at;

    /**
     *
     */
    public function __construct() {
        $this->id = 0;
        $this->valor = 0.0;
        $this->created_at = '';
        $this->updated_at = '';
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getValor(): float {
        return $this->valor;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string {
        return $this->updated_at;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): Cotizacion {
        $this->id = $id;
        return $this;
    }

    /**
     * @param float $valor
     *
     * @return $this
     */
    public function setValor(float $valor): Cotizacion {
        $this->valor = $valor;
        return $this;
    }

    /**
     * @param string $created_at
     *
     * @return $this
     */
    public function setCreatedAt(string $created_at): Cotizacion {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @param string $updated_at
     *
     * @return $this
     */
    public function setUpdatedAt(string $updated_at): Cotizacion {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return get_object_vars($this);
    }

    /**
     * @param int $id
     *
     * @return Cotizacion|null
     */
    public static function getCotizacionById(int $id): ?Cotizacion {
        $conn = Connection::getConn();

        $query = "SELECT id, valor, created_at, updated_at
                  FROM cotizacion
                  WHERE id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('i', $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $cotizacion = null;

        if ($row = $result->fetch_assoc()) {
            $cotizacion = new Cotizacion();
            $cotizacion->setId($row['id']);
            $cotizacion->setValor((float) $row['valor']);
            $cotizacion->setCreatedAt($row['created_at']);
            $cotizacion->setUpdatedAt($row['updated_at']);
        }

        $result->free();

        return $cotizacion;
    }

    /**
     * @param Cotizacion $cotizacion
     *
     * @return void
     */
    public static function updateCotizacion(Cotizacion $cotizacion): void {
        $conn = Connection::getConn();

        $query = "UPDATE cotizacion SET valor=? WHERE id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('di', $cotizacion->valor, $cotizacion->id);

        $stmt->execute();
    }
}
