<?php
include_once 'config/controller.php';
include "model/restoran.php";

class restoranlar extends controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new restoranDb();
    }
    // her restoran kendine ait hesap ile admin panellerine girebilmek için bu methoddan login olması gerekir
    public function login()
    {
        if (isset($_POST["restoranMail"])and isset($_POST["sifre"]))
        {

            $mail = $this->filtre($_POST["restoranMail"]);
            $sifre = md5($_POST["sifre"]);

            $restoran = $this->db->login($mail,$sifre);
            if ($restoran)
            {
                $restoran["jwtrestoran"] =$this->encodeJWT($restoran);
                echo json_encode($restoran);
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Restoran bulunamadı";
                echo json_encode($result);
            }
        }
        else{
            $result["status"] = 0;
            $result["message"] = "veriler yok";
            echo json_encode($result);
        }
    }

    public function get()
    {
        if (!isset($_POST["id"]) or empty($_POST["id"])) die("lütfen geçerli bir id değeri gönderin");
        $id = $this->filtre($_POST["id"]);
        $result = $this->db->get($id);
        if ($result)
        {
            echo json_encode($result);
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Restoran Bilgileri Bulunamadı";
            echo json_encode($result);
        }
    }

    public function gets()
    {
        $result = $this->db->gets();
        if ($result)
        {
            echo json_encode($result);
        }
        else{
            $result["status"] = 0;
            $result["message"] = "veriler bulunamadı";
            echo json_encode($result);
        }
    }

    // eklemeyi yapmak için bir admin jwt token bilgisi gelmesi gerekir aksi taktirde gerçekleşmez.
    public function insert()
    {
        if (isset($_POST["jwtUser"]))
        {
            $jwtUser = $_POST["jwtUser"];
            $user = $this->decodeJWT($jwtUser);
            $user["oturum"] = $this->userdb->checkuser($user["username"]);
            if ($user["oturum"])
            {
                $ad = $this->filtre($_POST["restoranAd"]);
                $telefon = $this->filtre($_POST["restoranTelefon"]);
                $mail = $this->filtre($_POST["restoranMail"]);
                $masaSayisi = $this->filtre($_POST["restoranMasaSayisi"]);
                $password = md5($_POST["sifre"]);

                $result = $this->db->insert($ad,$telefon,$mail,$masaSayisi,$password);
                if ($result)
                {
                    http_response_code(200);
                    echo json_encode("ekleme başarılı");
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "Başarısız";
                    echo json_encode($result);
                }
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
                echo json_encode($result);
            }
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Lütfen gerekli bilgileri gönderin";
            echo json_encode($result);
        }
    }

    // güncellemeyi yapmak için restoran hesabından bir jwt token gelmesi gerek aksi taktirde gerçekleşmez.
    public function update($id =0)
    {
        if (isset($_POST["jwtRestoran"]) and $id != 0)
        {
            $jwtRestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtRestoran);
            $restoran["oturum"] = $this->db->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"] and $id == $restoran["id"])
            {
                $islem = $this->filtre($_POST["islem"]);
                if ($islem == "genel") {
                    $ad = $this->filtre($_POST["restoranAd"]);
                    $telefon = $this->filtre($_POST["restoranTelefon"]);
                    $mail = $this->filtre($_POST["restoranMail"]);
                    $masaSayisi = $this->filtre($_POST["restoranMasaSayisi"]);

                    $result = $this->db->updateGenel($ad, $telefon, $mail, $masaSayisi,$id);
                    if ($result) {
                        http_response_code(200);
                        echo json_encode("güncelleme başarılı");
                    }
                    else{
                        $result["status"] = 0;
                        $result["message"] = "Başrısız";
                        echo json_encode($result);
                    }
                }
                elseif($islem == "sifre")
                {
                    $sifre = md5($_POST["sifre"]);
                    $result = $this->db->updatePass($sifre,$id);
                    if ($result) {
                        http_response_code(200);
                        echo json_encode("güncelleme başarılı");
                    }
                    else{
                        $result["status"] = 0;
                        $result["message"] = "başarısız";
                        echo json_encode($result);
                    }
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "işlem bilgisi alınamadı";
                    echo json_encode($result);
                }
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
                echo json_encode($result);
            }
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Lütfen bilgileri tam gönderin";
            echo json_encode($result);
        }
    }
}