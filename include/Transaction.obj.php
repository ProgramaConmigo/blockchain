<?php
class Transaction {
    public $source;
    public $destination;
    public $amount;

    function __construct($source, $destination, $amount) {
        $this->source       = $source;
        $this->destination  = $destination;
        $this->amount      = $amount;
    }
}
?>