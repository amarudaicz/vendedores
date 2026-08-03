<?php

namespace api;

use api\exceptions\ApiException;
use filters\SessionFilter;
use helpers\Logger;
use helpers\Request;
use helpers\Response;
use models\AuthorizedEmail;
use models\Connection;
use models\Seller;

abstract class AuthSellers
{
    public static function authenticate()
    {
        SessionFilter::validateApiSession();

        if (empty($_SESSION['account'])) {
            http_response_code(400); // Envía el código HTTP adecuado
            Response::setCode(401);
            return;
        }

        $account = unserialize($_SESSION['account']);
        if ($account instanceof Seller) {
            Response::append('seller', $account);
            http_response_code(200);
            Response::setCode(200);
            return;
        }

        // Fallback por si algo sale mal
        http_response_code(500);
        Response::setCode(500);
    }

    public static function signOut(): void
    {
        if (empty($_SESSION['account']))
            throw new ApiException('Forbidden. You are not logged in.', 403);

        $_SESSION = [];
        session_destroy();
        Response::setCode(204);
    }

    public static function signIn(): void
    {

        $data = Request::getJson();

        if (empty($data->email)) {
            http_response_code(400);
            throw new ApiException('El usuario es obligatorio', 400);
        }

        if (empty($data->password)) {
            http_response_code(400);
            throw new ApiException('La contraseña es obligatoria', 400);
        }

        $account = Seller::getByEmail($data->email);

        if (!$account) {
            http_response_code(400);
            throw new ApiException('Credenciales inválidas', 400);
        }

        if ($account->getDeleted()) {
            http_response_code(400);
            throw new ApiException('Credenciales inválidas', 409);
        }

        if (!password_verify($data->password, $account->getPasswordHash())) {
            http_response_code(400);
            throw new ApiException('Credenciales inválidas', 401);
        }


        $_SESSION['timestamp'] = time();
        $_SESSION['account'] = serialize($account);

        Response::setCode(204);
    }

    public static function signUp(): void
    {
        if (!empty($_SESSION['account']))
            throw new ApiException('Forbidden. You are already logged in.', 403);

        Connection::getConn()->begin_transaction();

        $data = Request::getJson();

        if (empty($data->name))
            throw new ApiException('Name is required', 400);

        if (empty($data->email))
            throw new ApiException('Email is required', 400);

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL))
            throw new ApiException('Email format is invalid', 400);

        if (empty($data->password))
            throw new ApiException('Password is required', 400);

        $account = Seller::getByEmail($data->email);

        if (!empty($account))
            throw new ApiException('The email is already in use', 409);

        $authorizedEmail = AuthorizedEmail::getByEmail($data->email);

        if (empty($authorizedEmail))
            throw new ApiException('The email is not authorized', 403);

        $account = new Seller();
        $account->setName($data->name);
        $account->setEmail($authorizedEmail->getEmail());
        $account->setPasswordSalt(base64_encode(random_bytes(9)));
        $account->setPasswordHash(password_hash($account->getPasswordSalt() . $data->password, PASSWORD_DEFAULT));
        $account->setDeleted(false);
        $account->setCreatedAt(date('Y-m-d H:i:s'));
        $account->setUpdatedAt(date('Y-m-d H:i:s'));

        Seller::createOrUpdate($account);
        AuthorizedEmail::delete($authorizedEmail);

        Connection::getConn()->commit();

        $_SESSION['timestamp'] = time();
        $_SESSION['account'] = serialize($account);

        Response::setCode(204);
    }

    public static function changePassword(): void
    {
        SessionFilter::validateApiSession();

        if (empty($_SESSION['account'])) {
            http_response_code(401);
            throw new ApiException('Unauthorized access.', 400);
        }

        $account = unserialize($_SESSION['account']);

        if (!$account instanceof Seller) {
            http_response_code(500);
            throw new ApiException('Invalid session data.', 500);
        }

        $data = Request::getJson();

        // Determine target seller ID
        $targetSellerId = isset($data->sellerId) ? (int)$data->sellerId : $account->getId();
        $isSelfUpdate = $targetSellerId === $account->getId();

        if (!$isSelfUpdate) {
            // Check if current user is admin
            if (!$account->getIsAdmin()) {
                http_response_code(403);
                throw new ApiException('No tienes permisos para cambiar la contraseña de otro vendedor.', 403);
            }
        }

        if (empty($data->newPassword)) {
            http_response_code(400);
            throw new ApiException('La nueva contraseña es requerida.', 400);
        }

        $newPasswordHash = password_hash($data->newPassword, PASSWORD_DEFAULT);
        $result = Seller::updatePassword($targetSellerId, $newPasswordHash);

        if (!$result) {
            http_response_code(500);
            throw new ApiException('Error al actualizar la contraseña.', 500);
        }

        // If updating self, update session
        if ($isSelfUpdate) {
            $account->setPasswordHash($newPasswordHash);
            $_SESSION['account'] = serialize($account);
            $_SESSION['timestamp'] = time();
        }

        Response::setData(['message' => 'Contraseña actualizada correctamente']);
        Response::setCode(200);
    }
}
