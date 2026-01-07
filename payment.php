<?php
// This file is for handling payment processing in PHP

class Payment {
    private $amount;

    public function __construct($amount) {
        $this->amount = $amount;
    }

    public function processPayment() {
        // Logic to process payment goes here
        echo "Processing payment of amount: " . $this->amount;
    }
}

// Example of using the Payment class
$payment = new Payment(100);
$payment->processPayment();