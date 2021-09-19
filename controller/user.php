<?php
include_once 'config/controller.php';
include_once 'model/user.php';

class user extends controller
{
    protected $db;
    public function __construct()
    {
        // bu class kurucuların ve sistemin arkasında çalışan kişilerin classı
        parent::__construct();
        $this->db = new userDb();
    }

    public function login()
    {
        if(isset($_POST["username"]) and isset($_POST["password"]))
        {
            $username = $this->filtre($_POST["username"]);
            $password = md5($_POST["password"]);

            $result = $this->db->login($username,$password);
            if ($result)
            {
                $result["jwt"] = $this->encodeJWT($result);
                echo json_encode($result);
            }
            else{
                $result["status"] = 0;
                $result["message"] = "lütfen geçerli bir bilgi gönderin";
                echo json_encode($result);
            }
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
            echo json_encode($result);
        }
    }

    public function insert()
    {
        if(isset($_POST["jwtuser"]))
        {
            $jwtuser = $_POST["jwtuser"];
            $user = $this->decodeJWT($jwtuser);
            $user["oturum"] = $this->db->checkuser($user["username"]);
            if($user["oturum"])
            {
                $ad = $this->filtre($_POST["ad"]);
                $soyad = $this->filtre($_POST["soyad"]);
                $username = $this->filtre($_POST["username"]);
                $password = md5($_POST["password"]);
                $mail = $this->filtre($_POST["mail"]);
                $telefon = $this->filtre($_POST["telefon"]);

                // bu kullanıcı adında birisi var mı diye kontrol ediyorum
                $issetusername = $this->db->checkuser($username);
                if($issetusername){
                    $result["status"] = 0;
                    $result["message"] = "bu kullanıcı adı kullanılıyor.";
                    echo json_encode($result);
                    die();
                }

                $result = $this->db->insert($ad,$soyad,$username,$password,$mail,$telefon);
                if($result)
                {
                    $result2["status"] = 1;
                    $result2["message"] = "Ekleme başarılı";
                    echo json_encode($result2);
                    die();
                }
                else{
                    $result2["status"] = 0;
                    $result2["message"] = "kullanıcı eklenmedi";
                    echo json_encode($result2);
                    die();
                }
            }
            else{
                $result2["status"] = 0;
                $result2["message"] = "Oturum yok";
                echo json_encode($result2);
                die();
            }
        }
        else{
            $result2["status"] = 0;
            $result2["message"] = "Bilgiler eksik";
            echo json_encode($result2);
            die();
        }
        
    }

    public function update()
    {
        if(isset($_POST["jwtuser"]))
        {
            $jwtuser = $_POST["jwtuser"];
            $user = $this->decodeJWT($jwtuser);
            $user["oturum"] = $this->db->checkuser($user["username"]);
            if($user["oturum"] and isset($_POST["islem"]))
            {
                $islem = $this->filtre($_POST["islem"]);
                $id = $this->filtre($_POST["id"]);
                if($islem == "genel")
                {
                    $ad = $this->filtre($_POST["ad"]);
                    $soyad = $this->filtre($_POST["soyad"]);
                    $username = $this->filtre($_POST["username"]);
                    $mail = $this->filtre($_POST["mail"]);
                    $telefon = $this->filtre($_POST["telefon"]);
                    
                    // bu kullanıcı adında birisi var mı diye kontrol ediyorum
                    $issetusername = $this->db->checkuser($username);
                    if($issetusername != false and $issetusername["id"] != $id){
                        $result["status"] = 0;
                        $result["message"] = "bu kullanıcı adı kullanılıyor.";
                        echo json_encode($result);
                        die();
                    }
                    
                    $result = $this->db->updateGenel($ad,$soyad,$username,$mail,$telefon,$id);

                    if($result)
                    {
                        $result2["status"] = 1;
                        $result2["message"] = "Güncelleme işlemi başarılı";
                        echo json_encode($result2);
                        die();
                    }
                    else{
                        $result2["status"] = 0;
                        $result2["message"] = "Güncelleme yapılamadı";
                        echo json_encode($result2);
                        die();
                    }
                }
                elseif($islem == "sifre")
                {
                    $sifre = md5($_POST["password"]);

                    $result = $this->db->updatePass($sifre,$id);
                    if($result)
                    {
                        $result2["status"] = 1;
                        $result2["message"] = "Güncelleme işlemi başarılı";
                        echo json_encode($result2);
                        die();
                    }
                    else{
                        $result2["status"] = 0;
                        $result2["message"] = "Güncelleme yapılamadı";
                        echo json_encode($result2);
                        die();
                    }
                }
            }
            else{
                $result2["status"] = 0;
                $result2["message"] = "Oturum yok";
                echo json_encode($result2);
                die();
            }
        }
        else{
            $result2["status"] = 0;
            $result2["message"] = "Bilgiler eksik";
            echo json_encode($result2);
            die();
        }
    }
}