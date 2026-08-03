<?php

namespace models;

use api\exceptions\ApiException;
use DateTime;
use helpers\Logger;
use JsonSerializable;


class Payment implements JsonSerializable
{
    private string $customer_code = '';
    private string $seller_code = '';
    private string $type_receipt = '';
    private string $number_receipt = '';
    private string $date_receipt = '';
    private string $subtotal_receipt = '';
    private string $total_receipt = '';
    private string $balance_receipt = '';
    private string $iva_receipt = '';
    private string $condition_sale = '';
    private string $delay_receipt = '';
    private string $balance_accumulated = '';

    function __construct(
        $customer_code,
        $seller_code,
        $type_receipt,
        $number_receipt,
        $date_receipt,
        $subtotal_receipt,
        $total_receipt,
        $balance_receipt,
        $iva_receipt,
        $condition_sale,
        $delay_receipt,
        $balance_accumulated
    ) {
        $this->customer_code = $customer_code;
        $this->seller_code = $seller_code;
        $this->type_receipt = $type_receipt;
        $this->number_receipt = $number_receipt;
        $this->date_receipt = $date_receipt;
        $this->subtotal_receipt = $subtotal_receipt;
        $this->total_receipt = $total_receipt;
        $this->balance_receipt = $balance_receipt;
        $this->iva_receipt = $iva_receipt;
        $this->condition_sale = $condition_sale;
        $this->delay_receipt = $delay_receipt;
        $this->balance_accumulated = $balance_accumulated;
    }

    public function setCustomerCode($customer_code)
    {
        $this->customer_code = $customer_code;
        return $this;
    }

    public function getCustomerCode()
    {
        return $this->customer_code;
    }

    public function setSellerCode($seller_code)
    {
        $this->seller_code = $seller_code;
        return $this;
    }

    public function getSellerCode()
    {
        return $this->seller_code;
    }

    public function setTypeReceipt($type_receipt)
    {
        $this->type_receipt = $type_receipt;
        return $this;
    }

    public function getTypeReceipt()
    {
        return $this->type_receipt;
    }

    public function setNumberReceipt($number_receipt)
    {
        $this->number_receipt = $number_receipt;
        return $this;
    }

    public function getNumberReceipt()
    {
        return $this->number_receipt;
    }

    public function setDateReceipt($date_receipt)
    {
        $this->date_receipt = $date_receipt;
        return $this;
    }

    public function getDateReceipt()
    {
        return $this->date_receipt;
    }

    public function setSubtotalReceipt($subtotal_receipt)
    {
        $this->subtotal_receipt = $subtotal_receipt;
        return $this;
    }

    public function getSubtotalReceipt()
    {
        return $this->subtotal_receipt;
    }

    public function setTotalReceipt($total_receipt)
    {
        $this->total_receipt = $total_receipt;
        return $this;
    }

    public function getTotalReceipt()
    {
        return $this->total_receipt;
    }

    public function setBalanceReceipt($balance_receipt)
    {
        $this->balance_receipt = $balance_receipt;
        return $this;
    }

    public function getBalanceReceipt()
    {
        return $this->balance_receipt;
    }

    public function setIvaReceipt($iva_receipt)
    {
        $this->iva_receipt = $iva_receipt;
        return $this;
    }

    public function getIvaReceipt()
    {
        return $this->iva_receipt;
    }

    public function setConditionSale($condition_sale)
    {
        $this->condition_sale = $condition_sale;
        return $this;
    }

    public function getConditionSale()
    {
        return $this->condition_sale;
    }

    public function setDelayReceipt($delay_receipt)
    {
        $this->delay_receipt = $delay_receipt;
        return $this;
    }

    public function getDelayReceipt()
    {
        return $this->delay_receipt;
    }

    public function setBalanceAccumulated($balance_accumulated)
    {
        $this->balance_accumulated = $balance_accumulated;
        return $this;
    }

    public function getBalanceAccumulated()
    {
        return $this->balance_accumulated;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'customer_code' => $this->customer_code,
            'seller_code' => $this->seller_code,
            'type_receipt' => $this->type_receipt,
            'number_receipt' => $this->number_receipt,
            'date_receipt' => $this->date_receipt,
            'subtotal_receipt' => $this->subtotal_receipt,
            'total_receipt' => $this->total_receipt,
            'balance_receipt' => $this->balance_receipt,
            'iva_receipt' => $this->iva_receipt,
            'condition_sale' => $this->condition_sale,
            'delay_receipt' => $this->delay_receipt,
            'balance_accumulated' => $this->balance_accumulated
        ];
    }

    public static function getPayments(string|int $customer_code, string|int $customer_zone)
    {
        $conn = Connection::getConn();

        // Consulta para obtener la suma de balance_accumulated
        $queryTotalBalance = "
            SELECT 
                SUM(pago_balance_receipt) AS total_balance 
            FROM 
                pagos 
            WHERE 
                pago_cliente_code = ? AND pago_cliente_zone = ?
        ";

        // Consulta para obtener los pagos
        $queryPayments = "
            SELECT 
                pago_cliente_code AS customer_code,
                pago_cliente_zone AS customer_zone,
                pago_vendedor_code AS seller_code,
                pago_type_receipt AS type_receipt,
                pago_number_receipt AS number_receipt,
                pago_date_receipt AS date_receipt,
                pago_subtotal_receipt AS subtotal_receipt,
                pago_total_receipt AS total_receipt,
                pago_balance_receipt AS balance_receipt,
                pago_iva_receipt AS iva_receipt,
                pago_condition_sale AS condition_sale,
                pago_delay_receipt AS delay_receipt,
                pago_balance_accumulated AS balance_accumulated
            FROM 
                pagos 
            WHERE 
                pago_cliente_code = ? AND pago_cliente_zone = ?
                ORDER BY pago_date_receipt DESC;
        ";

        // Obtener el total_balance
        $stmtTotalBalance = $conn->prepare($queryTotalBalance);
        if (!$stmtTotalBalance) {
            throw new ApiException("Error al preparar la consulta de total_balance: " . $conn->error);
        }
        $stmtTotalBalance->bind_param('ii', $customer_code, $customer_zone);
        $stmtTotalBalance->execute();
        $resultTotalBalance = $stmtTotalBalance->get_result();
        $totalBalanceRow = $resultTotalBalance->fetch_assoc();
        $totalBalance = $totalBalanceRow['total_balance'] ?? 0; // Si no hay resultados, default 0
        $stmtTotalBalance->close();

        // Obtener los pagos
        $stmtPayments = $conn->prepare($queryPayments);
        if (!$stmtPayments) {
            throw new ApiException("Error al preparar la consulta de pagos: " . $conn->error);
        }
        $stmtPayments->bind_param('ii', $customer_code, $customer_zone);
        $stmtPayments->execute();
        $resultPayments = $stmtPayments->get_result();

        error_log($resultPayments->num_rows);
        // Verificar si hay pagos
        $payments = [];
        if ($resultPayments->num_rows > 0) {
            while ($row = $resultPayments->fetch_assoc()) {
                $payments[] = $row;
            }
        }
        $stmtPayments->close();

        // Retornar el resultado combinado
        return [
            'total_balance' => $totalBalance,
            'receipts' => $payments
        ];
    }

    public function save()
    {

        $conn = Connection::getConn();
        // $conn->real_query("TRUNCATE TABLE payments");

        $query = "INSERT INTO pagos (
        pago_cliente_code, 
        pago_vendedor_code, 
        pago_type_receipt, 
        pago_number_receipt,
        pago_date_receipt,
        pago_subtotal_receipt,
        pago_total_receipt,
        pago_balance_receipt,
        pago_iva_receipt,
        pago_condition_sale,
        pago_delay_receipt,
        pago_balance_accumulated ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepara la sentencia
        $stmt = $conn->prepare($query);


        $dateString = stripslashes($this->date_receipt);
        $date = DateTime::createFromFormat('d/m/Y', $dateString);
        $formattedDate = $date->format('Y-m-d');

        $stmt->bind_param(
            "ssssssssssss",
            $this->customer_code,
            $this->seller_code,
            $this->type_receipt,
            $this->number_receipt,
            $formattedDate,
            $this->subtotal_receipt,
            $this->total_receipt,
            $this->balance_receipt,
            $this->iva_receipt,
            $this->condition_sale,
            $this->delay_receipt,
            $this->balance_accumulated
        );

        $stmt->execute();
    }
}
