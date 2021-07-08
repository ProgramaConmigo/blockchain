<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    include "../include/main.php";
    $blockchain = new Blockchain("/var/www/html/blockchain/database");
    if(!isset($_SESSION["error"]))
    $_SESSION["error"] = "";
    if(count($_POST) > 0)
    {
        $error = "";
        if($_POST["action"] == "importWallet")
        {
            $pkey_txt = file_get_contents($_FILES["key"]["tmp_name"]);
            $private_key = openssl_pkey_get_private($pkey_txt);
            $pem_public_key = openssl_pkey_get_details($private_key)['key'];
            $public_key = openssl_pkey_get_public($pem_public_key);
            $_SESSION["wallet"]         = hash("sha256", $pem_public_key);
            $_SESSION["private_key"]    = $pkey_txt;
            $_SESSION["public_key"]     = $pem_public_key;
        }
        if($_POST["action"] == "createWallet")
        {
            $RSA = new RSA();
            $RSA->generateKeys();
            $_SESSION["private_key"]    = $RSA->private_key;
            $_SESSION["public_key"]     = $RSA->public_key;
            $_SESSION["wallet"]         = hash("sha256", $RSA->public_key);
            $blockchain->newWallet($RSA->public_key);
        }
        if($_POST["action"] == "newTransaction")
        {
            $RSA = new RSA($_SESSION["private_key"], $_SESSION["public_key"]);
            //$error = file_get_contents("http://127.0.0.1/blockchain/public/api/index.php?data=".urlencode($_SESSION["wallet"].$RSA->privEncrypt($_POST["destination"].date("YmdHis").$_POST["amount"])));
            echo "http://127.0.0.1/blockchain/public/api/index.php?data=".urlencode($_SESSION["wallet"].$RSA->privEncrypt($_POST["destination"].date("YmdHis").$_POST["amount"]));
            exit;
        }
        if($_POST["action"] == "logout")
        {
            $_SESSION = array();
        }
        if($error != "")
        $_SESSION["error"] = $error;
        echo "<script>window.location = window.location</script>";
        if($error != "")
        exit;
    }
    ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BlockChain</title>
        <link rel="stylesheet" href="css/main.css">
        <script src="js/jquery.js"></script>
        <script src="js/main.js"></script>
    </head>
    <body>
    <?php

    if(isset($_SESSION["wallet"]) && $_SESSION["wallet"] != "")
    {
?>

        <div class="modal" id="modal-wallet">
            <form action="" method="post" enctype="multipart/form-data">
                <h1>BlockChain</h1>
                    <?php
                    if($_SESSION["error"] != "")
                    {
                    ?>
                    <div id="modal-error">
                        <?php echo $_SESSION["error"]; ?>
                        <br>
                        <a href="#" onclick="$('#modal-error').remove()">Haz click aquí para cerrar el error</a>
                    </div>
                    <?php
                    unset($_SESSION["error"]);
                    }
                    ?>
                <h3>Wallet : <span style="font-family:monospace"><?php echo $_SESSION["wallet"]; ?></span></h3>
                <h3>Balance: <span style="font-family:monospace"><?php echo $blockchain->wallets[$_SESSION["wallet"]]->balance; ?></span></h3>
                <input type="button" name="action" value="Exportar Claves" onclick="window.open('key_export')">
                <hr>
                <h2 for="destination">Destino</h2>
                <input type="text" name="destination" id="destination" value=""><br>
                <h2 for="amount">Importe</h2>
                <input type="number" name="amount" id="amount" value=""><br>
                <button type="submit" name="action" value="newTransaction">TRANSFERIR</button>
                <button type="submit" name="action" value="logout" onclick="return (confirm('¿Seguro que quieres cerrar sesión?'))">Logout</button>
            </form>
        </div>
<?php
    }
    else
    {
?>
        <div class="modal" id="modal-login">
            <form action="" method="post" enctype="multipart/form-data">
                <h1>BlockChain</h1>
                <label for="modal-login-key" id="modal-login-key-label">
                <div>
                    PULSA AQUI PARA IMPORTAR TU WALLET
                </div>
                </label>
                <input type="file" name="key" id="modal-login-key" onchange="$(this).parent().append('<input type=\'hidden\' name=\'action\' value=\'importWallet\'>');$(this).parent().submit()"><br>                
                <button type="submit" name="action" value="createWallet">CREAR NUEVA</button>
            </form>
        </div>
<?php
    }
?>
    </body>
</html>