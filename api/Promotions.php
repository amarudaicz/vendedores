<?php

namespace api;

use api\exceptions\ApiException;
use filters\AccountFilter;
use filters\SessionFilter;
use helpers\Request;
use helpers\Response;
use JsonException;
use models\Connection;
use models\Image;
use models\Promotion;

/**
 *
 */
abstract class Promotions {
    /**
     * @return void
     */
    public static function getPromotions(): void {
        $promotions = Promotion::getPromotions();

        Response::append('promotions', $promotions);

        Response::setCode(200);
    }

    /**
     * @return void
     * @throws JsonException
     * @throws ApiException
     */
    public static function createPromotion(): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        if (empty($data->description))
            throw new ApiException('Descripcion requerida', 400);

        if (empty($data->imageId))
            throw new ApiException('Imagen requerida', 400);

        Connection::getConn()->begin_transaction();

        $promotion = new Promotion();
        $promotion->setDescription($data->description);
        Promotion::createPromotion($promotion);

        $image = Image::getImageById($data->imageId);

        if (empty($image))
            throw new ApiException('Imagen no encontrada', 404);

        Promotion::assignImage($promotion->getId(), $image->getId());

        Connection::getConn()->commit();

        Response::append('promotion', $promotion);

        Response::setCode(200);
    }

    /**
     * @param int $promotionId
     *
     * @return void
     * @throws ApiException
     */
    public static function getPromotion(int $promotionId): void {
        $promotion = Promotion::getPromotionById($promotionId);

        if (empty($promotion))
            throw new ApiException('La promocion no existe', 404);

        Response::append('promotion', $promotion);

        Response::setCode(200);
    }

    /**
     * @param int $promotionId
     *
     * @return void
     * @throws ApiException
     * @throws JsonException
     */
    public static function updatePromotion(int $promotionId): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $data = Request::getJson();

        Connection::getConn()->begin_transaction();

        $promotion = Promotion::getPromotionById($promotionId);

        if (empty($promotion))
            throw new ApiException('La promocion no existe', 404);

        if (!empty($data->description))
            $promotion->setDescription($data->description);

        if (!empty($data->imageId) && $data->imageId !== $promotion->getImage()->getId()) {
            $image = Image::getImageById($data->imageId);

            if (empty($image))
                throw new ApiException('Imagen no encontrada', 404);

            Promotion::unassignImage($promotion->getId(), $promotion->getImage()->getId());

            Promotion::assignImage($promotion->getId(), $image->getId());
        }

        $promotion = Promotion::getPromotionById($promotion->getId());

        Promotion::updatePromotion($promotion);

        Connection::getConn()->commit();

        Response::append('promotion', $promotion);

        Response::setCode(200);
    }

    /**
     * @param int $promotionId
     *
     * @return void
     * @throws ApiException
     */
    public static function deletePromotion(int $promotionId): void {
        SessionFilter::validateApiSession();
        AccountFilter::filterApiCustomerAccount();

        $promotion = Promotion::getPromotionById($promotionId);

        if (empty($promotion))
            throw new ApiException('La promocion no existe', 404);

        Connection::getConn()->begin_transaction();

        $image = Image::getImageAssignedToPromotionId($promotion->getId());

        Promotion::unassignImage($promotion->getId(), $image->getId());

        Promotion::deletePromotion($promotion);

        Connection::getConn()->commit();

        Response::setCode(204);
    }
}