<?php
class Rsa {    
    public $private_key;
    public $public_key;   
    
    /**
     *  Pasamos por parametros la clave publica y la privada
     */
    function __construct($priv = "", $pub = "") {
        $this->private_key  = $priv;
        $this->public_key   = $pub;
    }

    /**     
           * Obtener clave privada     
     * @return bool|resource     
     */    
    function getPrivateKey() 
    {        
        $privKey = $this->private_key;        
        return openssl_pkey_get_private($privKey);    
    }    
 
    /**     
           * Obtener clave pública     
     * @return bool|resource     
     */    
    function getPublicKey()
    {        
        $publicKey = $this->public_key;        
        return openssl_pkey_get_public($publicKey);    
    }    
 

    function generateKeys()
    {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        
        
        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        $this->private_key  = $privKey;
        $this->public_key   = $pubKey;
    }

    /**     
           * Cifrado de clave privada     
     * @param string $data     
     * @return null|string     
     */    
    function privEncrypt($data = '')    
    {        
        if (!is_string($data)) {            
            return null;       
        }        
        return openssl_private_encrypt($data,$encrypted,$this->getPrivateKey()) ? base64_encode($encrypted) : null;    
    }    
 
    /**     
           * Cifrado de clave pública     
     * @param string $data     
     * @return null|string     
     */    
    function publicEncrypt($data = '')   
    {        
        if (!is_string($data)) {            
            return null;        
        }        
        return openssl_public_encrypt($data,$encrypted,$this->getPublicKey()) ? base64_encode($encrypted) : null;    
    }    
 
    /**     
           * Descifrado de clave privada     
     * @param string $encrypted     
     * @return null     
     */    
    function privDecrypt($encrypted = '')    
    {        
        if (!is_string($encrypted)) {            
            return null;        
        }        
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->getPrivateKey())) ? $decrypted : null;    
    }    
 
    /**     
           * Descifrado de clave pública     
     * @param string $encrypted     
     * @return null     
     */    
    function publicDecrypt($encrypted = '')    
    {        
        if (!is_string($encrypted)) {            
            return null;        
        }        
    return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, $this->getPublicKey())) ? $decrypted : null;    
    }
}