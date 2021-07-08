<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
include "../../include/main.php";
$blockchain = new Blockchain("/var/www/html/blockchain/database");
$wallet = $blockchain->wallets[substr($_REQUEST["data"], 0, 64)];

$RSA = new RSA("", $wallet->pubKey);
$transfer   = $RSA->publicDecrypt(substr($_REQUEST["data"], 64, -1));
if($transfer != "")
{
    $destino = substr($transfer, 0, 64);
    $fecha =  substr($transfer, 64, 14);
    $importe =  substr($transfer, 78, strlen($transfer));
    $blockchain->newTransaction(
        substr($_REQUEST["data"], 0, 64),
        $destino,
        $importe
    );
} 
?>