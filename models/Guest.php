<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Guest implements JsonSerializable {
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
    private string $tin;

    /**
     * @var string
     */
    private string $phone;

    /**
     * @var string
     */
    private string $location;

    /**
     * @var string
     */
    private string $postalCode;

    /**
     * @var string|null
     */
    private string|null $email;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     *
     */
    public function __construct() {
        $this->id = 0;
        $this->name = '';
        $this->tin = '';
        $this->phone = '';
        $this->location = '';
        $this->postalCode = '';
        $this->email = '';
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
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTin(): string {
        return $this->tin;
    }

    /**
     * @return string
     */
    public function getPhone(): string {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string {
        return $this->postalCode;
    }

    public function getEmail(): string|null {
        return $this->email;
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
    public function setId(int $id): Guest {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Guest {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $tin
     * @return $this
     */
    public function setTin(string $tin): Guest {
        $this->tin = $tin;
        return $this;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): Guest {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation(string $location): Guest {
        $this->location = $location;
        return $this;
    }

    /**
     * @param string $postalCode
     * @return $this
     */
    public function setPostalCode(string $postalCode): Guest {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function setEmail(string|null $email): Guest {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Guest {
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
    public static function getGuests(): array {
        $conn = Connection::getConn();

        $query = "SELECT invitado_id AS id, invitado_name AS name, invitado_tin AS tin, invitado_phone AS phone, invitado_location AS location, invitado_postal_code AS postal_code, guest_email AS email, invitado_created_at AS created_at FROM guests";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $guests = [];

        while ($row = $result->fetch_assoc()) {
            $guest = new Guest();
            $guest->setName($row["name"]);
            $guest->setTin($row["tin"]);
            $guest->setPhone($row["phone"]);
            $guest->setLocation($row["location"]);
            $guest->setPostalCode($row["postal_code"]);
            $guest->setEmail($row["email"] ?? null);
            $guest->setCreatedAt($row["created_at"]);
            $guests[] = $guest;
        }

        $result->free();

        return $guests;
    }

    /**
     * @param int $guestId
     * @return Guest|null
     */
    public static function getGuestById(int $guestId): ?Guest {
        $conn = Connection::getConn();

        $query = "SELECT invitado_id AS id, invitado_name AS name, invitado_tin AS tin, invitado_phone AS phone, invitado_location AS location, invitado_postal_code AS postal_code, guest_email AS email, invitado_created_at AS created_at FROM guests WHERE invitado_id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $guestId);

        $stmt->execute();

        $result = $stmt->get_result();

        $guest = null;

        if ($row = $result->fetch_assoc()) {
            $guest = new Guest();
            $guest->setName($row["name"]);
            $guest->setTin($row["tin"]);
            $guest->setPhone($row["phone"]);
            $guest->setLocation($row["location"]);
            $guest->setPostalCode($row["postal_code"]);
            $guest->setEmail($row["email"] ?? null);
            $guest->setCreatedAt($row["created_at"]);
        }

        $result->free();

        return $guest;
    }

    /**
     * @param Guest $guest
     * @return void
     */
    public static function createGuest(Guest $guest): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO guests (invitado_name, invitado_tin, invitado_phone, invitado_location, invitado_postal_code, guest_email, invitado_created_at) VALUE (?,?,?,?,?,?,NOW())";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("ssssss",
            $guest->name,
            $guest->tin,
            $guest->phone,
            $guest->location,
            $guest->postalCode,
            $guest->email
        );

        $stmt->execute();

        $guest->setId($stmt->insert_id);
    }
}
