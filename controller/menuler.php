<?php
include_once 'config/controller.php';
include_once 'model/menuler.php';
include_once 'model/restoran.php';
include_once 'vendor/phpqrcode/qrlib.php';
include_once 'model/calisanlar.php';

class menuler extends controller
{
    protected $db;
    protected $restoranDb;
    protected $calisandb;

    public function __construct()
    {
        parent::__construct();
        $this->db = new menulerdb();
        $this->restoranDb = new restoranDb();
        $this->calisandb = new calisanlarDb();
    }

    // gelen restoran token bilgisine göre id değerini alır ve ilgili restoran menu bilgilerini geriye döndürür.
    public function gets()
    {
        if (isset($_POST["jwtRestoran"])) {
            $jwtrestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"] == true) {

                $result = $this->db->gets($restoran["id"]);
                if ($result) {
                    echo json_encode($result);
                    die();
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "Bilgi Bulunamadı";
                    echo json_encode($result);
                }
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
                echo json_encode($result);
            }
        }
        elseif(isset($_POST["jwtCalisan"]))
        {
            $jwtcalisan = $_POST["jwtCalisan"];
            $calisan = $this->decodeJWT($jwtcalisan);

            $calisan["oturum"] = $this->calisandb->checkcalisan($calisan["tc"]);
            if($calisan["oturum"])
            {
                $result = $this->db->gets($calisan["restoranFK"]);
                if ($result) {
                    echo json_encode($result);
                    die();
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "Bilgi Bulunamadı";
                    echo json_encode($result);
                }
            }
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Lütfen bilgileri tam gönderin";
            echo json_encode($result);
        }
    }

    // gelen restoran token bilgisine göre id değerini alır ve menuid ile eşleşen değerleri getirir.
    public function get()
    {
        if (isset($_POST["jwtRestoran"]) and isset($_POST["menuId"])) {
            $jwtrestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"] == true) {
                $menuId = $this->filtre($_POST["menuId"]);
                $result = $this->db->get($menuId, $restoran["id"]);
                if ($result) {
                    $result["status"] = 1;
                    echo json_encode($result);
                    die();
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "menu bilgisi bulunamadı";
                    echo json_encode($result);
                }
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Oturum bilgilerinin doğruluğundan emin olun";
                echo json_encode($result);
            }
        }
        elseif(isset($_POST["jwtCalisan"]))
        {
            $jwtcalisan = $_POST["jwtCalisan"];
            $calisan = $this->decodeJWT($jwtcalisan);

            $calisan["oturum"] = $this->calisandb->checkcalisan($calisan["tc"]);
            if($calisan["oturum"])
            {
                $menuId = $this->filtre($_POST["menuId"]);
                $result = $this->db->get($menuId, $calisan["restoranFK"]);
                if ($result) {
                    echo json_encode($result);
                    die();
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "menu bilgisi bulunamadı";
                    echo json_encode($result);
                }
            }
            
        }
        else{
            $result["status"] = 0;
            $result["message"] = "Lütfen bilgileri tam gönderin";
            echo json_encode($result);
        }
    }

    // menu oluştururken gönderilen jwtRestoran token bilgisinde bulunan restoran id değerine göre kayıt edilir
    // menu oluşturulurken bir qr kod yaratılır bu qr kod gerekli olan resim adresine kayıt edilir.
    // bu method da QRcode kısmı hata verebilir bunun için php.ini de gd kısımnı aktif etmen gerekir.
    public function insert()
    {
        if (isset($_POST["jwtRestoran"]) and $_FILES) {
            $jwtrestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"]) {

                $menuad = $this->filtre($_POST["menuAd"]);
                $menuFiyat = $this->filtre($_POST["menuFiyat"]);
                $menuBilgi = $this->filtre($_POST["menuBilgi"]);

                $yol = "images/menuler/" . uniqid() . ".png";
                $qryol = "images/menuQR/QR" . uniqid() . ".png";
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $yol)) {
                    $result = $this->db->insert($menuad, $menuFiyat, $menuBilgi, $restoran["id"], $yol, $qryol);

                    if ($result) {
                        QRcode::png($result ,$qryol);
                        echo json_encode("başarılı");
                    }
                    else{
                        $result["status"] = 0;
                        $result["message"] = "Veri tabanına kayıt edilmedi";
                        echo json_encode($result);
                    }
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "Resim eklenmedi";
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

    // işlemler 2 ye ayrılır birisi genel bilgileri güncelleme diğeri ise menü fotoğraf güncelleme
    public function update()
    {
        if (isset($_POST["jwtRestoran"])) {
            $jwtrestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"]) {
                $islem = $this->filtre($_POST["islem"]);
                $menuId = $this->filtre($_POST["menuId"]);
                if ($islem == "genel") {
                    $menuad = $this->filtre($_POST["menuAd"]);
                    $menuFiyat = $this->filtre($_POST["menuFiyat"]);
                    $menuBilgi = $this->filtre($_POST["menuBilgi"]);

                    $result = $this->db->updateGenel($menuad, $menuFiyat, $menuBilgi, $menuId, $restoran["id"]);
                    if ($result) {
                        http_response_code(200);
                        echo json_encode("başarılı");
                    } else {
                        http_response_code(201);
                        echo json_encode("başarısız");
                    }
                }
                elseif ($islem == "resim" and isset($_FILES["image"])) {
                    $yol = "images/menuler/" . uniqid() . ".png";

                    $oldmenu = $this->db->get($menuId, $restoran["id"]);

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $yol)) {
                        $result = $this->db->updateResim($yol, $menuId, $restoran["id"]);
                        if ($result) {
                            unlink($oldmenu["menuFoto"]);
                            echo json_encode("Fotoğraf Güncellendi");
                        } else {
                            unlink($yol);
                            http_response_code(201);
                            echo json_encode("Fotoğraf Güncellenmedi");
                        }
                    }
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "Lütfen İşlem bilgisi gönderin";
                    echo json_encode($_POST);
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

    // bir restoran token bilgisine ve menu id bilgisine gerek var
    public function delete()
    {
        if (isset($_POST["jwtRestoran"]) and isset($_POST["id"])) {
            $jwt = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwt);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"]) {
                $id = $this->filtre($_POST["id"]);

                $result = $this->db->delete($id, $restoran["id"]);
                if ($result) {
                    echo "silme başarılı";
                    http_response_code(200);
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "silinmedi";
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
            $result["message"] = "lütfen bilgileri tam olarak gönderin";
            echo json_encode($result);
        }
    }
}
