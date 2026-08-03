<?php
namespace models;

use JsonSerializable;
use models\Customer;
use models\Payment;

class Balances implements JsonSerializable {
    private Customer|null $customer = null; 
    /**
     * @var Payment[]|null
     */
    private ?array $payments = null;

    function __construct($customer, $payments) {
        $this->customer = $customer;
        $this->payments = $payments;
    }

    public function setCustomer(Customer $customer) {
        $this->customer = $customer;
        return $this;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function setPayments(Payment $payments) {
        $this->payments = $payments;
        return $this;
    }

    public function getPayments() {
        return $this->payments;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'customer' => $this->customer,
            'payments' => $this->payments
        ];
    }

  
}
