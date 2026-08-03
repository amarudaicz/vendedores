<?php

namespace api;

use Exception;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Response;
use models\Image;
use api\exceptions\ApiException;

/**
 *
 */
abstract class Images {
    /**
     * @return void
     * @throws ApiException
     */
    public static function getImages(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $images = Image::getImages();

        Response::append('images', $images);

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws ApiException
     * @throws Exception
     */
    public static function createImage(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        if (empty($_FILES['file']))
            throw new ApiException('Archivo requerido', 400);

        if ($_FILES['file']['error'] !== 0)
            throw new ApiException('Hubo un error al subir el archivo', 500);

        if (!is_uploaded_file($_FILES['file']['tmp_name']))
            throw new ApiException('Hubo un error al subir el archivo', 500);

        if (!in_array($_FILES['file']['type'], ['image/png', 'image/jpeg', 'image/jpg']))
            throw new ApiException('El archivo debe ser un archivo tipo png, jpeg, jpg', 400);

        $pathInfo = pathinfo($_FILES['file']['name']);
        $filePath = sprintf("public/multimedia/uploads/%s-%s.%s", hash('md5', random_bytes(8) . uniqid(time())), $pathInfo['filename'], $pathInfo['extension']);

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath))
            throw new ApiException('Hubo un error al guardar el archivo', 500);

        $image = new Image();
        $image->setName($_FILES['file']['name']);
        $image->setDescription('');
        $image->setFilePath($filePath);
        $image->setFileSize($_FILES['file']['size']);
        $image->setFilePath($filePath);
        $image->setFileType($_FILES['file']['type']);
        $image->setCreatedAt(date('Y-m-d H:i:s'));
        $image->setUpdatedAt(date('Y-m-d H:i:s'));
        Image::createImage($image);

        Response::append('image', $image);

        Response::setCode(200);
    }

    /**
     * @param int $imageId
     *
     * @return void
     * @throws ApiException
     */
    public static function getImage(int $imageId): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $image = Image::getImageById($imageId);

        if (empty($image))
            throw new ApiException('La imagen no existe', 404);

        Response::append('image', $image);

        Response::setCode(200);
    }

    /**
     * @param int $imageId
     *
     * @return void
     * @throws ApiException
     */
    public static function deleteImage(int $imageId): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $image = Image::getImageById($imageId);

        if (empty($image))
            throw new ApiException('La imagen no existe', 404);

        if (!is_file($image->getFilePath()))
            throw new ApiException('La imagen no existe', 404);

        if (!unlink($image->getFilePath()))
            throw new ApiException('Error al eliminar la imagen', 500);

        Image::deleteImage($image);

        Response::setCode(204);
    }
}