<?php

namespace models;

use api\Images;
use JsonSerializable;
use PDO;
use api\exceptions\ApiException;

/**
 *
 */
class Product implements JsonSerializable
{
    /**
     * @var string
     */
    private string $code;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var float
     */
    private float $stock;

    /**
     * @var float
     */
    private float $price;

    /**
     * @var float
     */
    private float $price1;

    /**
     * @var float
     */
    private float $price2;

    /**
     * @var float
     */
    private float $price3;

    /**
     * @var float
     */
    private float $price4;

    /**
     * @var float
     */
    private float $price5;

    /**
     * @var float
     */
    private float $price6;

    /**
     * @var float
     */
    private float|string $arsUsd;

    /**
     * @var bool
     */
    private bool $featured;

    /**
     * @var bool
     */
    private bool $deleted;


    /**
     * @var string
     */
    private string $updatedAt;

    /**
     * @var int
     */
    private int $categoryCode;

    /**
     * @var int|null
     */
    private ?int $subcategoryCode;

    /**
     * @var Image|null
     */
    private ?Image $image;

    /**
     * @var Category|null
     */
    private ?Category $category;

    /**
     * @var Subcategory|null
     */
    private ?Subcategory $subcategory;

    /**
     *
     */
    public function __construct()
    {
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->stock = 0.0;
        $this->price = 0.0;
        $this->price1 = 0.0;
        $this->price2 = 0.0;
        $this->price3 = 0.0;
        $this->price4 = 0.0;
        $this->price5 = 0.0;
        $this->price6 = 0.0;
        $this->arsUsd = 0.0;
        $this->featured = false;
        $this->deleted = false;
        $this->updatedAt = '';
        $this->categoryCode = 0;
        $this->subcategoryCode = null;

        $this->image = null;
        $this->category = null;
        $this->subcategory = null;
}

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getStock(): float
    {
        return $this->stock;
    }

    /**
     * @param int $priceList
     *
     * @return float
     */
    public function getCustomerPrice(int $priceList): float
    {
        return match ($priceList) {
            1 => $this->price1,
            2 => $this->price2,
            3 => $this->price3,
            4 => $this->price4,
            5 => $this->price5,
            6 => $this->price6,
            default => $this->price,
        };
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getPrice1(): float
    {
        return $this->price1;
    }

    /**
     * @return float
     */
    public function getPrice2(): float
    {
        return $this->price2;
    }

    /**
     * @return float
     */
    public function getPrice3(): float
    {
        return $this->price3;
    }

    /**
     * @return float
     */
    public function getPrice4(): float
    {
        return $this->price4;
    }

    /**
     * @return float
     */
    public function getPrice5(): float
    {
        return $this->price5;
    }

    /**
     * @return float
     */
    public function getPrice6(): float
    {
        return $this->price6;
    }

    /**
     * @return float
     */
    public function getArsUsd(): float|string
    {
        return $this->arsUsd;
    }

    /**
     * Actualiza la propiedad arsUsd de un array de productos con el valor actual de cotizacion
     * Esto mantiene la compatibilidad con el frontend que espera esta propiedad
     * @param array $products
     * @param float|null $dolarOverride Si se proporciona, usa este valor en lugar de la cotización global
     * @return void
     */
    private static function updateArsUsdFromCotizacion(array $products, ?float $dolarOverride = null): void
    {
        try {
            $dolar = $dolarOverride ?? self::getDolar();
            foreach ($products as $product) {
                if ($product instanceof Product) {
                    $product->setArsUsd($dolar);
                }
            }
        } catch (ApiException $e) {
            // Si falla al obtener la cotización, no rompemos la carga de productos
                // El valor arsUsd vendrá de la base de datos (articulos.articulo_ars_usd)
            error_log('Error al actualizar ars_usd desde cotizacion: ' . $e->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getCategoryCode(): int
    {
        return $this->categoryCode;
    }

    /**
     * @return int|null
     */
    public function getSubcategoryCode(): ?int
    {
        return $this->subcategoryCode;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @return Subcategory|null
     */
    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): Product
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param float $stock
     *
     * @return $this
     */
    public function setStock(float $stock): Product
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param float $price1
     *
     * @return $this
     */
    public function setPrice1(float $price1): Product
    {
        $this->price1 = $price1;
        return $this;
    }

    /**
     * @param float $price2
     *
     * @return $this
     */
    public function setPrice2(float $price2): Product
    {
        $this->price2 = $price2;
        return $this;
    }

    /**
     * @param float $price3
     *
     * @return $this
     */
    public function setPrice3(float $price3): Product
    {
        $this->price3 = $price3;
        return $this;
    }

    /**
     * @param float $price4
     *
     * @return $this
     */
    public function setPrice4(float $price4): Product
    {
        $this->price4 = $price4;
        return $this;
    }

    /**
     * @param float $price5
     *
     * @return $this
     */
    public function setPrice5(float $price5): Product
    {
        $this->price5 = $price5;
        return $this;
    }

    /**
     * @param float $price6
     *
     * @return $this
     */
    public function setPrice6(float $price6): Product
    {
        $this->price6 = $price6;
        return $this;
    }

    /**
     * @param float $arsUsd
     *
     * @return void
     */
    public function setArsUsd(float|string $arsUsd): void
    {
        $this->arsUsd = $arsUsd;
    }

    /**
     * @param bool $featured
     *
     * @return $this
     */
    public function setFeatured(bool $featured): Product
    {
        $this->featured = $featured;
        return $this;
    }

    /**
     * @param bool $deleted
     *
     * @return $this
     */
    public function setDeleted(bool $deleted): Product
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): Product
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @param int $categoryCode
     *
     * @return $this
     */
    public function setCategoryCode(int $categoryCode): Product
    {
        $this->categoryCode = $categoryCode;
        return $this;
    }

    /**
     * @param int|null $subcategoryCode
     *
     * @return $this
     */
    public function setSubcategoryCode(?int $subcategoryCode): Product
    {
        $this->subcategoryCode = $subcategoryCode;
        return $this;
    }

    /**
     * @param Image|null $image
     *
     * @return $this
     */
    public function setImage(?Image $image): Product
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @param Category|null $category
     *
     * @return $this
     */
    public function setCategory(?Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param Subcategory|null $subcategory
     *
     * @return $this
     */
    public function setSubcategory(?Subcategory $subcategory): Product
    {
        $this->subcategory = $subcategory;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $object = get_object_vars($this);

        unset($object['price_1']);
        unset($object['price_2']);
        unset($object['price_3']);
        unset($object['price_4']);
        unset($object['price_5']);
        unset($object['price_6']);

        return $object;
    }

    /**
     * @return array
     */
    public static function getProducts(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT p.articulo_code AS code,
       p.articulo_name AS name,
       p.articulo_description AS description,
       p.articulo_price AS price,
       p.articulo_price_1 AS price_1,
       p.articulo_price_2 AS price_2,
       p.articulo_price_3 AS price_3,
       p.articulo_price_4 AS price_4,
       p.articulo_price_5 AS price_5,
       p.articulo_price_6 AS price_6,
       p.articulo_ars_usd AS ars_usd,
       p.articulo_stock AS stock,
       p.articulo_featured AS featured,
       p.articulo_deleted AS deleted,
       p.articulo_updated_at AS updated_at,
       p.articulo_genero_code AS category_code,
       p.articulo_familia_code AS subcategory_code,
       g.genero_name AS category_name,
       f.familia_name AS subcategory_name,
       img.image_id AS image_id
FROM articulos p
         LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
         LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
         LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
         LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
WHERE p.articulo_deleted = FALSE
ORDER BY category_name, p.articulo_name";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrice($row['price']);
            $product->setPrice1($row['price_1']);
            $product->setPrice2($row['price_2']);
            $product->setPrice3($row['price_3']);
            $product->setPrice4($row['price_4']);
            $product->setPrice5($row['price_5']);
            $product->setPrice6($row['price_6']);
            $product->setArsUsd($row['ars_usd']);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }

            $products[] = $product;
        }

        $result->free();

        // Actualizar ars_usd con el valor actual de cotizacion
        self::updateArsUsdFromCotizacion($products);

        return $products;
    }



    /**
     * @return array
     */
    public static function getAvailableProducts(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT p.articulo_code AS code,
       p.articulo_name AS name,
       p.articulo_description AS description,
       p.articulo_price AS price,
       p.articulo_price_1 AS price_1,
       p.articulo_price_2 AS price_2,
       p.articulo_price_3 AS price_3,
       p.articulo_price_4 AS price_4,
       p.articulo_price_5 AS price_5,
       p.articulo_price_6 AS price_6,
       p.articulo_ars_usd AS ars_usd,
       p.articulo_stock AS stock,
       p.articulo_featured AS featured,
       p.articulo_deleted AS deleted,
       p.articulo_updated_at AS updated_at,
       p.articulo_genero_code AS category_code,
       p.articulo_familia_code AS subcategory_code,
       g.genero_name AS category_name,
       f.familia_name AS subcategory_name,
       img.image_id AS image_id
FROM articulos p
         LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
         LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
         LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
         LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
WHERE p.articulo_deleted = 0
ORDER BY category_name, p.articulo_name";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrice($row['price']);
            $product->setPrice1($row['price_1']);
            $product->setPrice2($row['price_2']);
            $product->setPrice3($row['price_3']);
            $product->setPrice4($row['price_4']);
            $product->setPrice5($row['price_5']);
            $product->setPrice6($row['price_6']);
            $product->setArsUsd($row['ars_usd']);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }

            $products[] = $product;
        }

        $result->free();

        // Actualizar ars_usd con el valor actual de cotizacion
        self::updateArsUsdFromCotizacion($products);

        return $products;
    }

    public static function count(): int
    {
        $conn = Connection::getConn();
        $query = "SELECT COUNT(*) as count FROM articulos WHERE articulo_deleted = 0";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        return (int) $row['count'];
    }

    /**
     * @return array
     */
    public static function getFeaturedProducts(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT p.articulo_code AS code,
       p.articulo_name AS name,
       p.articulo_description AS description,
       p.articulo_price AS price,
       p.articulo_price_1 AS price_1,
       p.articulo_price_2 AS price_2,
       p.articulo_price_3 AS price_3,
       p.articulo_price_4 AS price_4,
       p.articulo_price_5 AS price_5,
       p.articulo_price_6 AS price_6,
       p.articulo_ars_usd AS ars_usd,
       p.articulo_stock AS stock,
       p.articulo_featured AS featured,
       p.articulo_deleted AS deleted,
       p.articulo_updated_at AS updated_at,
       p.articulo_genero_code AS category_code,
       p.articulo_familia_code AS subcategory_code,
       g.genero_name AS category_name,
       f.familia_name AS subcategory_name,
       img.image_id AS image_id
FROM articulos p
         LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
         LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
         LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
         LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
WHERE p.articulo_deleted = 0 AND p.articulo_featured = 1";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrice($row['price']);
            $product->setPrice1($row['price_1']);
            $product->setPrice2($row['price_2']);
            $product->setPrice3($row['price_3']);
            $product->setPrice4($row['price_4']);
            $product->setPrice5($row['price_5']);
            $product->setPrice6($row['price_6']);
            $product->setArsUsd($row['ars_usd']);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }

            $products[] = $product;
        }

        $result->free();

        // Actualizar ars_usd con el valor actual de cotizacion
        self::updateArsUsdFromCotizacion($products);

        return $products;
    }

    /**
     * @return array
     */
    public static function getNonFeaturedProducts(): array
    {
        $conn = Connection::getConn();

        $query = "SELECT p.articulo_code AS code,
       p.articulo_name AS name,
       p.articulo_description AS description,
       p.articulo_price AS price,
       p.articulo_price_1 AS price_1,
       p.articulo_price_2 AS price_2,
       p.articulo_price_3 AS price_3,
       p.articulo_price_4 AS price_4,
       p.articulo_price_5 AS price_5,
       p.articulo_price_6 AS price_6,
       p.articulo_ars_usd AS ars_usd,
       p.articulo_stock AS stock,
       p.articulo_featured AS featured,
       p.articulo_deleted AS deleted,
       p.articulo_updated_at AS updated_at,
       p.articulo_genero_code AS category_code,
       p.articulo_familia_code AS subcategory_code,
       g.genero_name AS category_name,
       f.familia_name AS subcategory_name,
       img.image_id AS image_id
FROM articulos p
         LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
         LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
         LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
         LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
WHERE p.articulo_deleted = FALSE AND p.articulo_featured = FALSE";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrice($row['price']);
            $product->setPrice1($row['price_1']);
            $product->setPrice2($row['price_2']);
            $product->setPrice3($row['price_3']);
            $product->setPrice4($row['price_4']);
            $product->setPrice5($row['price_5']);
            $product->setPrice6($row['price_6']);
            $product->setArsUsd($row['ars_usd']);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }

            $products[] = $product;
        }

        $result->free();

        // Actualizar ars_usd con el valor actual de cotizacion
        self::updateArsUsdFromCotizacion($products);

        return $products;
    }

    /**
     * @param string $lastUpdatedAt
     *
     * @return array
     */
    public static function getNotUpdatedProducts(string $lastUpdatedAt): array
    {
        $conn = Connection::getConn();

        $query = "SELECT p.articulo_code AS code,
       p.articulo_name AS name,
       p.articulo_description AS description,
       p.articulo_price AS price,
       p.articulo_price_1 AS price_1,
       p.articulo_price_2 AS price_2,
       p.articulo_price_3 AS price_3,
       p.articulo_price_4 AS price_4,
       p.articulo_price_5 AS price_5,
       p.articulo_price_6 AS price_6,
       p.articulo_ars_usd AS ars_usd,
       p.articulo_stock AS stock,
       p.articulo_featured AS featured,
       p.articulo_deleted AS deleted,
       p.articulo_updated_at AS updated_at,
       p.articulo_genero_code AS category_code,
       p.articulo_familia_code AS subcategory_code,
       g.genero_name AS category_name,
       f.familia_name AS subcategory_name,
       img.image_id AS image_id
FROM articulos p
         LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
         LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
         LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
         LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
WHERE p.articulo_updated_at != ?
ORDER BY category_name, p.articulo_name";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $lastUpdatedAt);

        $stmt->execute();

        $result = $stmt->get_result();

        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrice($row['price']);
            $product->setPrice1($row['price_1']);
            $product->setPrice2($row['price_2']);
            $product->setPrice3($row['price_3']);
            $product->setPrice4($row['price_4']);
            $product->setPrice5($row['price_5']);
            $product->setPrice6($row['price_6']);
            $product->setArsUsd($row['ars_usd']);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }

            $products[] = $product;
        }

        $result->free();

        // Actualizar ars_usd con el valor actual de cotizacion
        self::updateArsUsdFromCotizacion($products);

        return $products;
    }

    /**
     * @param string $productCode
     *
     * @return Product|null
     */
    public static function getProductByCode(string $productCode): ?Product
    {
        $conn = Connection::getConn();

        $query = "SELECT p.articulo_code AS code,
       p.articulo_name AS name,
       p.articulo_description AS description,
       p.articulo_price AS price,
       p.articulo_price_1 AS price_1,
       p.articulo_price_2 AS price_2,
       p.articulo_price_3 AS price_3,
       p.articulo_price_4 AS price_4,
       p.articulo_price_5 AS price_5,
       p.articulo_price_6 AS price_6,
       p.articulo_ars_usd AS ars_usd,
       p.articulo_stock AS stock,
       p.articulo_featured AS featured,
       p.articulo_deleted AS deleted,
       p.articulo_updated_at AS updated_at,
       p.articulo_genero_code AS category_code,
       p.articulo_familia_code AS subcategory_code,
       g.genero_name AS category_name,
       f.familia_name AS subcategory_name,
       img.image_id AS image_id
FROM articulos p
         LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
         LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
         LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
         LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
WHERE p.articulo_code = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $productCode);

        $stmt->execute();

        $result = $stmt->get_result();

        $product = null;

        if ($row = $result->fetch_assoc()) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrice($row['price']);
            $product->setPrice1($row['price_1']);
            $product->setPrice2($row['price_2']);
            $product->setPrice3($row['price_3']);
            $product->setPrice4($row['price_4']);
            $product->setPrice5($row['price_5']);
            $product->setPrice6($row['price_6']);
            $product->setArsUsd($row['ars_usd']);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }
        }

        $result->free();

        // Actualizar ars_usd con el valor actual de cotizacion para el producto individual
        if ($product instanceof Product) {
            try {
                $dolar = self::getDolar();
                $product->setArsUsd($dolar);
            } catch (ApiException $e) {
                // Si falla al obtener la cotización, no rompemos la carga del producto
            // El valor arsUsd vendrá de la base de datos (articulos.articulo_ars_usd)
                error_log('Error al actualizar ars_usd desde cotizacion: ' . $e->getMessage());
            }
        }

        return $product;
    }

    /**
     * @param Product $product
     *
     * @return void
     */
    public static function createUpdateProduct(Product $product): void
    {
        $conn = Connection::getConn();

        $query = "
            INSERT INTO articulos (
                articulo_code, articulo_name, articulo_stock, articulo_price, articulo_price_1, articulo_price_2, articulo_price_3, articulo_price_4, articulo_price_5, articulo_price_6,
                articulo_ars_usd, articulo_featured, articulo_deleted, articulo_updated_at, articulo_genero_code, articulo_familia_code
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                articulo_name = VALUES(articulo_name),
                articulo_stock = VALUES(articulo_stock),
                articulo_price = VALUES(articulo_price),
                articulo_price_1 = VALUES(articulo_price_1),
                articulo_price_2 = VALUES(articulo_price_2),
                articulo_price_3 = VALUES(articulo_price_3),
                articulo_price_4 = VALUES(articulo_price_4),
                articulo_price_5 = VALUES(articulo_price_5),
                articulo_price_6 = VALUES(articulo_price_6),
                articulo_ars_usd = VALUES(articulo_ars_usd),
                articulo_featured = VALUES(articulo_featured),
                articulo_deleted = VALUES(articulo_deleted),
                articulo_updated_at = VALUES(articulo_updated_at),
                articulo_genero_code = VALUES(articulo_genero_code),
                articulo_familia_code = VALUES(articulo_familia_code)
        ";

        $stmt = $conn->prepare($query);
        error_log($product->code);

        $featured = $product->featured;
        $deleted = $product->deleted;

        $stmt->bind_param(
            'ssdddddddddiisii',
            $product->code,
            $product->name,
            $product->stock,
            $product->price,
            $product->price1,
            $product->price2,
            $product->price3,
            $product->price4,
            $product->price5,
            $product->price6,
            $product->arsUsd,
            $featured,
            $deleted,
            $product->updatedAt,
            $product->categoryCode,
            $product->subcategoryCode
        );

        $stmt->execute();
    }

    /**
     * @param Product $product
     *
     * @return void
     */
    public static function updateProduct(Product $product): void
    {
        $conn = Connection::getConn();

        $query = "UPDATE articulos
SET articulo_name=?,
    articulo_description=?,
    articulo_stock=?,
    articulo_price=?,
    articulo_price_1=?,
    articulo_price_2=?,
    articulo_price_3=?,
    articulo_price_4=?,
    articulo_price_5=?,
    articulo_price_6=?,
    articulo_ars_usd=?,
    articulo_featured=?,
    articulo_deleted=?,
    articulo_updated_at=?,
    articulo_genero_code=?,
    articulo_familia_code=?
WHERE articulo_code = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param(
            'ssdddddddddiisiii',
            $product->name,
            $product->description,
            $product->stock,
            $product->price,
            $product->price1,
            $product->price2,
            $product->price3,
            $product->price4,
            $product->price5,
            $product->price6,
            $product->arsUsd,
            $product->featured,
            $product->deleted,
            $product->updatedAt,
            $product->categoryCode,
            $product->subcategoryCode,
            $product->code
        );

        $stmt->execute();
    }


    /**
     * @param Product $product
     * @throws ApiException
     */
    public static function deleteProduct(Product $product): void
    {
        $conn = Connection::getConn();
        $query = "UPDATE articulos SET articulo_deleted = 1 WHERE articulo_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $product->code);
        $stmt->execute();
    }

    /**
     * @param string $productCode
     * @param int    $imageId
     *
     * @return void
     */
    public static function assignImage(string $productCode, int $imageId): void
    {
        $conn = Connection::getConn();

        $query = "INSERT INTO articulos_images (articulos_image_articulo_code, articulos_image_image_id) VALUES (?, ?)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('si', $productCode, $imageId);

        $stmt->execute();
    }

    /**
     * @param string $productCode
     * @param int    $imageId
     *
     * @return void
     */
    public static function unassignImage(string $productCode, int $imageId): void
    {
        $conn = Connection::getConn();

        $query = "DELETE FROM articulos_images WHERE articulos_image_articulo_code = ? AND articulos_image_image_id = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param('si', $productCode, $imageId);

        $stmt->execute();
    }

    public static function searchByQueryAndCustomerPrice($querySearch, $customerPrice, $page = 1, $perPage = 10, ?float $dolar = null): array
    {
        $conn = Connection::getConn();

        if ($customerPrice < 1 || $customerPrice > 6) {
            throw new ApiException('Invalid customer price', 400);
        }

        // Preparar la consulta
$query = "SELECT p.articulo_code AS code,
                      p.articulo_name AS name,
                      p.articulo_description AS description,
                      p.articulo_price_$customerPrice AS price,
                     p.articulo_ars_usd AS ars_usd,
                     CAST(p.articulo_stock AS UNSIGNED) AS stock,
                     p.articulo_featured AS featured,
                     p.articulo_deleted AS deleted,
                     p.articulo_updated_at AS updated_at,
                     p.articulo_genero_code AS category_code,
                     p.articulo_familia_code AS subcategory_code,
                     g.genero_name AS category_name,
                     f.familia_name AS subcategory_name,
                     img.image_id AS image_id
               FROM articulos p
                        LEFT JOIN generos g ON p.articulo_genero_code = g.genero_code
                        LEFT JOIN familias f ON p.articulo_familia_code = f.familia_code
                        LEFT JOIN articulos_images pi ON p.articulo_code = pi.articulos_image_articulo_code
                        LEFT JOIN images img ON pi.articulos_image_image_id = img.image_id
                WHERE p.articulo_deleted = 0 AND (p.articulo_name LIKE ? OR p.articulo_code LIKE ?)
               ORDER BY p.articulo_name";


        // Preparar la consulta
        $stmt = $conn->prepare($query);
        $offset = ($page - 1) * $perPage;
        // Vincular los parámetros
        $searchTerm = '%' . $querySearch . '%';
        $stmt->bind_param('ss', $searchTerm, $searchTerm);

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);

        // Obtener el total de productos para la paginación
        $countQuery = "SELECT COUNT(*) as total 
                    FROM articulos p 
                    WHERE p.articulo_deleted = 0
                    AND (p.articulo_name LIKE ? OR p.articulo_code LIKE ?)";

        $countStmt = $conn->prepare($countQuery);
        $countStmt->bind_param('ss', $searchTerm, $searchTerm);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];

        $countResult->free();
        $result->free();

        // Convertir array asociativo a objetos Product y actualizar ars_usd desde cotizacion
        $productObjects = [];
        foreach ($products as $row) {
            $product = new Product();
            $product->setCode($row['code']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $precioSinIva = round($row['price'] / 1.21, 2);
            $product->setPrice($precioSinIva);
            $product->setStock($row['stock']);
            $product->setFeatured($row['featured']);
            $product->setDeleted($row['deleted']);
            $product->setUpdatedAt($row['updated_at']);
            $product->setCategoryCode($row['category_code']);
            $product->setSubcategoryCode($row['subcategory_code']);

            if (!empty($row['image_id'])) {
                $product->setImage(new Image());
                $product->getImage()->setId($row['image_id']);
                $product->getImage()->setFilePath('placeholder-image');
            }

            $product->setCategory(new Category());
            $product->getCategory()->setCode($row['category_code']);
            $product->getCategory()->setName($row['category_name']);

            if (!empty($row['subcategory_code'])) {
                $product->setSubcategory(new Subcategory());
                $product->getSubcategory()->setCode($row['subcategory_code']);
                $product->getSubcategory()->setName($row['subcategory_name']);
            }

            $productObjects[] = $product;
        }

        // Actualizar ars_usd con el valor actual de cotizacion
        self::updateArsUsdFromCotizacion($productObjects, $dolar);

        return [
            'products' => $productObjects,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
            'total' => $total
        ];
    }

    /**
     * Obtiene la cotización del dólar desde la tabla cotizacion
     * @return float
     * @throws ApiException
     */
    public static function getDolar()
    {
        $conn = Connection::getConn();

        // Leer el valor de la tabla cotizacion (registro único: id=1)
        $query = "SELECT valor 
              FROM cotizacion 
              WHERE id = 1";

        $result = $conn->query($query);

        if (!$result) {
            throw new ApiException('Error al obtener la cotización del dólar', 500);
        }

        $row = $result->fetch_assoc();
        $result->free();

        if (!$row) {
            throw new ApiException('No se encontró cotización del dólar', 404);
        }

        return (float) $row['valor'];
    }

    /**
     * @param array $codes
     * @return void
     */
    public static function markDeletedNotIn(array $codes): void
    {
        if (empty($codes)) {
            return;
        }

        $conn = Connection::getConn();

        $placeholders = implode(",", array_fill(0, count($codes), "?"));

        $stmt = $conn->prepare("
            UPDATE articulos
            SET articulo_deleted = 1
            WHERE articulo_code NOT IN ($placeholders)
        ");

        $stmt->bind_param(str_repeat("s", count($codes)), ...$codes);

        $stmt->execute();
    }

    /**
     * @return int
     */
    public static function countDeleted(): int
    {
        $conn = Connection::getConn();

        $result = $conn->query("SELECT COUNT(*) AS total FROM articulos WHERE articulo_deleted = 1");

        $row = $result->fetch_assoc();

        return (int) $row['total'];
    }
    public static function fromArray(array $data): self
    {
        $product = new self();
        foreach ($data as $key => $value) {
            if (property_exists($product, $key)) {
                $product->{$key} = $value;
            }
        }
        return $product;
    }

    public static function markDeleted(string $code): bool
    {
        $conn = Connection::getConn();

        $stmt = $conn->prepare("
            UPDATE articulos SET articulo_deleted = 1, articulo_updated_at = NOW()
            WHERE articulo_code = ?
        ");

        $stmt->bind_param("s", $code);

        return $stmt->execute();
    }

    public static function updateStock(string $code, float $stock): bool
    {
        $conn = Connection::getConn();

        $stmt = $conn->prepare("
            UPDATE articulos SET articulo_stock = ?, articulo_updated_at = NOW()
            WHERE articulo_code = ?
        ");

        $stmt->bind_param("ds", $stock, $code);

        return $stmt->execute();
    }
}
