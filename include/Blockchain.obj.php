<?php
    include "Block.obj.php";
    include "Miner.obj.php";
    include "Wallet.obj.php";
    include "Transaction.obj.php";
    include "RSA.obj.php";

    class Blockchain {
        public $path;
        public $chain;
        public $block;
        public $wallets;

        function __construct ($path) {
            $this->path     = "";
            $this->block    = array();
            $this->chain    = array();
            $this->wallets  = array();

            $this->loadChain($path);
        }

        function loadChain ($path) {
            if($path[strlen($path)-1] != "/")
            $path = $path."/";
            if(is_dir($path))
            {
                $this->wallets["0000000000000000000000000000000000000000000000000000000000000000"] = new Wallet("0000000000000000000000000000000000000000000000000000000000000000", "0000000000000000000000000000000000000000000000000000000000000000");
                $this->path     = $path;
                if(file_exists($this->path."chain"))
                foreach(json_decode(file_get_contents($this->path."chain")) as $block)
                $this->chain[] = $block;
                if(file_exists($this->path."block"))
                $this->block = json_decode(file_get_contents($this->path."block"));
                if(file_exists($this->path."wallets"))
                foreach(json_decode(file_get_contents($this->path."wallets")) as $hash => $pkey)
                $this->wallets[$hash] = new Wallet($hash, $pkey);
                $this->loadWallets();
            }
            else
            {
                echo "ERROR: La ruta indicada no es una carpeta o no existe";
                exit;
            }
        }

        function newWallet($pubKey) {
            $wallet = new Wallet(hash("sha256", $pubKey), $pubKey);
            $this->wallets[hash("sha256", $pubKey)] = $wallet;
            $this->saveWallets();
        }

        function saveWallets () {
            $libreta = array();
            foreach($this->wallets as $wallet)
            $libreta[$wallet->hash] = $wallet->pubKey; 
            file_put_contents($this->path."wallets", json_encode($libreta));
        }

        function loadWallets () {
            foreach($this->chain as $block)
            {
                foreach($block->data as $transaction)
                {
                    $this->wallets[$transaction->source]->balance      = $this->wallets[$transaction->source]->balance-$transaction->amount;
                    $this->wallets[$transaction->destination]->balance = $this->wallets[$transaction->destination]->balance+$transaction->amount;
                }
            }
            foreach($this->block as $transaction)
            {
                $this->wallets[$transaction->source]->balance      = $this->wallets[$transaction->source]->balance-$transaction->amount;
                $this->wallets[$transaction->destination]->balance = $this->wallets[$transaction->destination]->balance+$transaction->amount;
            }
        }

        function newTransaction ($source, $destination, $amount) {
            $transaction = new Transaction($source, $destination, $amount);
            if($this->validateTransaction($transaction))
            $this->addTransaction($transaction);
            else
            {
                if(!isset($this->wallets[$transaction->source]))
                echo "ERROR: La cuenta origen no aparece registrada<br>";
                if(!isset($this->wallets[$transaction->destination]))
                echo "ERROR: La cuenta destino no aparece registrada<br>";
                if($this->wallets[$transaction->source]->balance < $transaction->amount)
                echo "ERROR: Saldo de la cuenta insuficiente<br>";
                exit;
            }
        }

        function addTransaction ($transaction) {
            $this->block[] = $transaction;
            if(count($this->block) == 10)
            $this->addBlock();
            else
            file_put_contents($this->path."block", json_encode($this->block));
        }

        function validateTransaction ($transaction) {
            return (
                isset($this->wallets[$transaction->source]) &&
                isset($this->wallets[$transaction->destination])  &&
                $this->wallets[$transaction->source]->balance >= $transaction->amount      
            );
        }

        function addBlock () {
            $this->chain[] = array(
                "id" => count($this->chain),
                "data" => $this->block,
                "hash" => hash(
                    "sha256",
                    json_encode($this->block).(
                        (
                            count($this->chain) > 0) ? 
                                $this->chain[count($this->chain)-1]->hash : 
                                "000000000000000000000000000000000000000000000000"
                        )
                    )
            );
            file_put_contents($this->path."chain", json_encode($this->chain));
            unlink($this->path."block");
        }
    }
?>