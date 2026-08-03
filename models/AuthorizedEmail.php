<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class AuthorizedEmail implements JsonSerializable {
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $email;

    /**
     *
     */
    public function __construct() {
        $this->id = 0;
        $this->email = '';
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
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): AuthorizedEmail {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): AuthorizedEmail {
        $this->email = $email;
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
    public static function getAll(): array {
        $conn = Connection::getConn();

        $query = "SELECT id, email FROM authorized_emails";

        $conn->real_query($query);

        $result = $conn->store_result();

        $authorizedEmails = [];

        while (($row = $result->fetch_assoc())) {
            $authorizedEmail = new AuthorizedEmail();
            $authorizedEmail->setId($row['id']);
            $authorizedEmail->setEmail($row['email']);
            $authorizedEmails[] = $authorizedEmail;
        }

        $result->free();

        return $authorizedEmails;
    }

    /**
     * @param int $id
     *
     * @return AuthorizedEmail|null
     */
    public static function getById(int $id): ?AuthorizedEmail {
        $conn = Connection::getConn();

        $query = "SELECT id, email FROM authorized_emails WHERE id = %d";

        $query = sprintf($query, $id);

        $conn->real_query($query);

        $result = $conn->store_result();

        $authorizedEmail = null;

        if (($row = $result->fetch_assoc())) {
            $authorizedEmail = new AuthorizedEmail();
            $authorizedEmail->setId($row['id']);
            $authorizedEmail->setEmail($row['email']);
        }

        $result->free();

        return $authorizedEmail;
    }

    /**
     * @param string $email
     *
     * @return AuthorizedEmail|null
     */
    public static function getByEmail(string $email): ?AuthorizedEmail {
        $conn = Connection::getConn();

        $query = "SELECT id, email FROM authorized_emails WHERE email = '%s'";

        $query = sprintf($query,
            $conn->escape_string($email)
        );

        $conn->real_query($query);

        $result = $conn->store_result();

        $authorizedEmail = null;

        if (($row = $result->fetch_assoc())) {
            $authorizedEmail = new AuthorizedEmail();
            $authorizedEmail->setId($row['id']);
            $authorizedEmail->setEmail($row['email']);
        }

        $result->free();

        return $authorizedEmail;
    }

    /**
     * @param AuthorizedEmail $authorizedEmail
     *
     * @return void
     */
    public static function create(AuthorizedEmail $authorizedEmail): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO authorized_emails (email) VALUES ('%s')";

        $query = sprintf($query,
            $conn->escape_string($authorizedEmail->getEmail()),
        );

        $conn->real_query($query);

        $authorizedEmail->setId($conn->insert_id);
    }

    /**
     * @param AuthorizedEmail $authorizedEmail
     *
     * @return void
     */
    public static function update(AuthorizedEmail $authorizedEmail): void {
        $conn = Connection::getConn();

        $query = "UPDATE authorized_emails SET email='%s' WHERE id = %d";

        $query = sprintf($query,
            $conn->escape_string($authorizedEmail->getEmail()),
            $authorizedEmail->getId()
        );

        $conn->real_query($query);
    }

    /**
     * @param AuthorizedEmail $authorizedEmail
     *
     * @return void
     */
    public static function delete(AuthorizedEmail $authorizedEmail): void {
        $conn = Connection::getConn();

        $query = "DELETE FROM authorized_emails WHERE id=%d";

        $query = sprintf($query, $authorizedEmail->getId());

        $conn->real_query($query);
    }
}
