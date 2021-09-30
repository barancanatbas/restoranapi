<?php
include_once "config/controller.php";
include_once "model/restoran.php";

class calisanlar extends controller
{
    protected $db;
    protected $restoranDb;
    public function __construct()
    {
        parent::__construct();
        // calisan status 1 olursa admin 0 olursa normal kullanıcı demektir. !!!!!
        $this->db = new calisanlarDb();
        $this->restoranDb = new restoranDb();
    }

    public function login()
    {
        if (isset($_POST["tc"]) and isset($_POST["sifre"])) {
            $tc = $this->filtre($_POST["tc"]);
            $sifre = md5($_POST["sifre"]);

            $result = $this->db->login($tc, $sifre);
            if ($result) {
                $result["jwt"] = $this->encodeJWT($result);
                echo json_encode($result);
                die();
            } else {
                $result["status"] = 0;
                $result["message"] = "Login işlemi başarısız";
                echo json_encode($result);
            }
        }
    }

    public function gets()
    {
        if(isset($_POST["jwtRestoran"])){
            $jwtrestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if($restoran["oturum"])
            {
                $id = $restoran["id"];
                $result = $this->db->gets($id);
                if ($result)
                {
                    echo json_encode($result);
                    die();
                }
            }
            else{
                http_response_code(201);
                $result["status"] = 0;
                $result["message"] = "oturum bilgisi yok";
                echo json_encode($result);
            }
        }
    }

    public function insert()
    {
        if (isset($_POST["jwtRestoran"])) {
            $jwt = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwt);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"]) {
                $insert["ad"] = $this->filtre($_POST["ad"]);
                $insert["soyad"] = $this->filtre($_POST["soyad"]);
                $insert["tc"] = $this->filtre($_POST["tc"]);
                $insert["mail"] = $this->filtre($_POST["mail"]);
                $insert["tel"] = $this->filtre($_POST["tel"]);
                $insert["adres"] = $this->filtre($_POST["adres"]);
                $insert["restoranFK"] = $restoran["id"];
                $insert["sifre"] = md5($_POST["sifre"]);
                $insert["status"] = 0;

                $resultTc = $this->db->checkcalisan($insert["tc"]);
                if ($resultTc) die(json_encode("bu tc kimlık numarası yanlış"));
                $result = $this->db->insert($insert);
                if ($result) {
                    echo json_encode("başarılı");
                    http_response_code(200);
                } else {
                    http_response_code(201);
                    $result["status"] = 0;
                    $result["message"] = "ekleme işlemi başarısız";
                    echo json_encode($result);
                }
            }
        }
    }

    public function update()
    {
        if (isset($_POST["jwt"])) {
            $jwt = $_POST["jwt"];
            $calisan = $this->decodeJWT($jwt);
            $calisan["oturum"] = $this->db->checkcalisan($calisan["tc"]);

            if ($calisan["oturum"] == true and $calisan["status"] == 1) {
                $update["id"] = $this->filtre($_POST["id"]);
                if ($update["id"] != $calisan["id"]) die("kendi hesabından düzenlemeler yap");

                $islem = $this->filtre($_POST["islem"]);
                if ($islem == "genel") {

                    $update["ad"] = $this->filtre($_POST["ad"]);
                    $update["soyad"] = $this->filtre($_POST["soyad"]);
                    $update["mail"] = $this->filtre($_POST["mail"]);
                    $update["tel"] = $this->filtre($_POST["tel"]);
                    $update["adres"] = $this->filtre($_POST["adres"]);
                    $update["restoranFK"] = $calisan["restoranFK"];

                    $result = $this->db->updateGenel($update);
                    if ($result) {
                        echo json_encode("başarılı");
                        http_response_code(200);
                    } else {
                        http_response_code(201);
                        $result["status"] = 0;
                        $result["message"] = "İşlem Gerçekleşmedi";
                        echo json_encode($result);
                    }
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "lütfen geçerli bir işlem giriniz.";
                    echo json_encode($result);
                }
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
                echo json_encode($result);
            }
        }
    }

    public function delete()
    {
        if (isset($_POST["jwtRestoran"])) {
            $jwt = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwt);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"]) {
                $id = $this->filtre($_POST["id"]);

                $result = $this->db->delete((int)$id, (int)$restoran["id"]);
                if ($result) {
                    echo json_encode($result);
                    http_response_code(200);
                } else http_response_code(201);
            } else {
                http_response_code(201);
                $result["status"] = 0;
                $result["message"] = "Oturum Bilgileri Eksik";
                echo json_encode($result);
            }
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
            echo json_encode($result);
        }
    }
}
