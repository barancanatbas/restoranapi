<?php
include_once 'vendor/autoload.php';
include_once 'model/calisanlar.php';
include_once 'model/user.php';
use Firebase\JWT\JWT;
class controller{

    protected $key;
    protected $alg;
    protected $calisandb;
    protected $userdb;
    public function __construct()
    {
        $this->key = md5("barancanatbas");
        $this->alg = "HS256";
        $this->calisandb = new calisanlarDb();
        $this->userdb = new userDb();
    }

    public function encodeJWT($payload)
    {
        try {
            $jwt = JWT::encode($payload,$this->key,$this->alg);
            return $jwt;
        }catch (Exception $e)
        {
            die(json_encode("geçerli bilgileri gönderin"));
        }
    }

    public function decodeJWT($jwt)
    {
        try {
            $decoded = (array)JWT::decode($jwt, $this->key, array($this->alg));
            return $decoded;
        }catch (Exception $e)
        {
            die(json_encode("JWT token bilgileri yanlış"));
        }
    }

    public function filtre($veri)
    {
        $bir = trim($veri);
        $iki = strip_tags($bir);
        $uc = htmlspecialchars($iki,ENT_QUOTES);
        return $uc;
    }

    public function filtreCoz($veri)
    {
        $bir = htmlspecialchars_decode($veri,ENT_QUOTES);
        return $bir;
    }
}