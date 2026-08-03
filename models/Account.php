<?php

namespace models;

use JsonSerializable;

/**
 *
 */
class Account implements JsonSerializable {
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
    private string $email;

    /**
     * @var string
     */
    private string $passwordSalt;

    /**
     * @var string
     */
    private string $passwordHash;

    /**
     * @var string
     */
    private string $profileImage;

    /**
     * @var bool
     */
    private bool $verifiedEmail;

    /**
     * @var bool
     */
    private bool $googleSignIn;

    /**
     * @var bool
     */
    private bool $deleted;

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
        $this->email = '';
        $this->passwordSalt = '';
        $this->passwordHash = '';
        $this->profileImage = '';
        $this->verifiedEmail = false;
        $this->googleSignIn = false;
        $this->deleted = false;
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
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string {
        return $this->passwordSalt;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getProfileImage(): string {
        return $this->profileImage;
    }

    /**
     * @return bool
     */
    public function isVerifiedEmail(): bool {
        return $this->verifiedEmail;
    }

    /**
     * @return bool
     */
    public function isGoogleSignIn(): bool {
        return $this->googleSignIn;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool {
        return $this->deleted;
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
     *
     * @return $this
     */
    public function setId(int $id): Account {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): Account {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): Account {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $passwordSalt
     *
     * @return $this
     */
    public function setPasswordSalt(string $passwordSalt): Account {
        $this->passwordSalt = $passwordSalt;
        return $this;
    }

    /**
     * @param string $passwordHash
     *
     * @return $this
     */
    public function setPasswordHash(string $passwordHash): Account {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     * @param string $profileImage
     *
     * @return $this
     */
    public function setProfileImage(string $profileImage): Account {
        $this->profileImage = $profileImage;
        return $this;
    }

    /**
     * @param bool $verifiedEmail
     *
     * @return $this
     */
    public function setVerifiedEmail(bool $verifiedEmail): Account {
        $this->verifiedEmail = $verifiedEmail;
        return $this;
    }

    /**
     * @param bool $googleSignIn
     *
     * @return $this
     */
    public function setGoogleSignIn(bool $googleSignIn): Account {
        $this->googleSignIn = $googleSignIn;
        return $this;
    }

    /**
     * @param bool $deleted
     *
     * @return $this
     */
    public function setDeleted(bool $deleted): Account {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(string $createdAt): Account {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): Account {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        $account = get_object_vars($this);

        unset($account['passwordSalt']);
        unset($account['passwordHash']);

        return $account;
    }

    /**
     * @return array
     */
    public static function getAccounts(): array {
        $conn = Connection::getConn();

        $query = "SELECT cuenta_id AS id,
       cuenta_name AS name,
       cuenta_email AS email,
       cuenta_password_salt AS password_salt,
       cuenta_password_hash AS password_hash,
       cuenta_profile_image AS profile_image,
       cuenta_verified_email AS verified_email,
       cuenta_google_sign_in AS google_sign_in,
       cuenta_deleted AS deleted,
       cuenta_created_at AS created_at
FROM accounts";

        $conn->real_query($query);

        $result = $conn->store_result();

        $accounts = [];

        while (($row = $result->fetch_assoc())) {
            $account = new Account();
            $account->setId($row['id']);
            $account->setName($row['name']);
            $account->setEmail($row['email']);
            $account->setPasswordSalt($row['password_salt']);
            $account->setPasswordHash($row['password_hash']);
            $account->setProfileImage($row['profile_image']);
            $account->setVerifiedEmail($row['verified_email']);
            $account->setGoogleSignIn($row['google_sign_in']);
            $account->setDeleted($row['deleted']);
            $account->setCreatedAt($row['created_at']);
            $accounts[] = $account;
        }

        $result->free();

        return $accounts;
    }

    /**
     * @param int $id
     *
     * @return Account|null
     */
    public static function getAccountById(int $id): ?Account {
        $conn = Connection::getConn();

        $query = "SELECT cuenta_id AS id,
       cuenta_name AS name,
       cuenta_email AS email,
       cuenta_password_salt AS password_salt,
       cuenta_password_hash AS password_hash,
       cuenta_profile_image AS profile_image,
       cuenta_verified_email AS verified_email,
       cuenta_google_sign_in AS google_sign_in,
       cuenta_deleted AS deleted,
       cuenta_created_at AS created_at
FROM accounts
WHERE cuenta_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $account = null;

        if (($row = $result->fetch_assoc())) {
            $account = new Account();
            $account->setId($row['id']);
            $account->setName($row['name']);
            $account->setEmail($row['email']);
            $account->setPasswordSalt($row['password_salt']);
            $account->setPasswordHash($row['password_hash']);
            $account->setProfileImage($row['profile_image']);
            $account->setVerifiedEmail($row['verified_email']);
            $account->setGoogleSignIn($row['google_sign_in']);
            $account->setDeleted($row['deleted']);
            $account->setCreatedAt($row['created_at']);
        }

        $result->free();

        return $account;
    }

    /**
     * @param string $email
     *
     * @return Account|null
     */
    public static function getAccountByEmail(string $email): ?Account {
        $conn = Connection::getConn();

        $query = "SELECT cuenta_id AS id,
       cuenta_name AS name,
       cuenta_email AS email,
       cuenta_password_salt AS password_salt,
       cuenta_password_hash AS password_hash,
       cuenta_profile_image AS profile_image,
       cuenta_verified_email AS verified_email,
       cuenta_google_sign_in AS google_sign_in,
       cuenta_deleted AS deleted,
       cuenta_created_at AS created_at
FROM accounts
WHERE cuenta_email = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $account = null;

        if (($row = $result->fetch_assoc())) {
            $account = new Account();
            $account->setId($row['id']);
            $account->setName($row['name']);
            $account->setEmail($row['email']);
            $account->setPasswordSalt($row['password_salt']);
            $account->setPasswordHash($row['password_hash']);
            $account->setProfileImage($row['profile_image']);
            $account->setVerifiedEmail($row['verified_email']);
            $account->setGoogleSignIn($row['google_sign_in']);
            $account->setDeleted($row['deleted']);
            $account->setCreatedAt($row['created_at']);
        }

        $result->free();

        return $account;
    }

    /**
     * @param Account $account
     *
     * @return void
     */
    public static function createAccount(Account $account): void {
        $conn = Connection::getConn();

        $query = "INSERT INTO accounts (cuenta_name, cuenta_email, cuenta_password_salt, cuenta_password_hash, cuenta_profile_image, cuenta_verified_email, cuenta_google_sign_in, cuenta_deleted, cuenta_created_at, cuenta_updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('sssssiii',
            $account->name,
            $account->email,
            $account->passwordSalt,
            $account->passwordHash,
            $account->profileImage,
            $account->verifiedEmail,
            $account->googleSignIn,
            $account->deleted
        );

        $stmt->execute();

        $account->setId($stmt->insert_id);
    }

    /**
     * @param Account $account
     *
     * @return void
     */
    public static function updateAccount(Account $account): void {
        $conn = Connection::getConn();

        $query = "UPDATE accounts
SET cuenta_name=?,
    cuenta_email=?,
    cuenta_password_salt=?,
    cuenta_password_hash=?,
    cuenta_profile_image=?,
    cuenta_verified_email=?,
    cuenta_google_sign_in=?,
    cuenta_deleted=?,
    cuenta_updated_at=CURRENT_TIMESTAMP
WHERE cuenta_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('sssssiiii',
            $account->name,
            $account->email,
            $account->passwordSalt,
            $account->passwordHash,
            $account->profileImage,
            $account->verifiedEmail,
            $account->googleSignIn,
            $account->deleted,
            $account->id
        );

        $stmt->execute();
    }

    /**
     * @param Account $account
     *
     * @return void
     */
    public static function deleteAccount(Account $account): void {
        $conn = Connection::getConn();

        $query = "DELETE FROM accounts WHERE cuenta_id=?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("i", $account->id);

        $stmt->execute();
    }
}
