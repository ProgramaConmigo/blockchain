<?php
    session_start();
    header('Content-Disposition: attachment; filename="wallet.blk"');
    ob_clean();
    echo $_SESSION["private_key"];
?>