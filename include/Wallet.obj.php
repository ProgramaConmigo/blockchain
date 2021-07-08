<?php
class Wallet {
    public $hash;
    public $pubKey;
    public $balance;

    function __construct ($hash, $pubKey)
    {
        $this->hash     = $hash;
        $this->pubKey   = $pubKey;
        $this->balance  = 0;
    }
}
?>