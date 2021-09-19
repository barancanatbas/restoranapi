<?php
include_once 'config/controller.php';
include_once 'model/siparis.php';
include_once 'model/restoran.php';
include_once 'model/menuler.php';

class siparis extends controller
{

    protected $db;
    protected $restoranDb;
    protected $menuDb;
    public function __construct()
    {
        parent::__construct();
        $this->db = new siparisdb();
        $this->restoranDb = new restoranDb();
        $this->menuDb = new menulerdb();
    }

    // siparis methoduna gelen değerlere göre bu methodlar çalışır. Bu methodalara url ile ulaşılmaz buna program içinden ulaşman gerek.
    private function insert($post,$id)
    {
        $calisanfk = $this->filtre($post["calisanFK"]);
        $menuler = $this->filtre($post["menuler"]);
        $masaNo = $this->filtre($post["masaNo"]);
        $tarih = time();

        $result = $this->db->insert($id,$calisanfk,$menuler,$masaNo,$tarih);
        if($result)
        {
            $result2["status"] = 1;
            $result2["message"] = "Ekleme başarılı";
            echo json_encode($result2);
            die();
        }
    }
    private function update($post,$id)
    {
        $menuler = $this->filtre($post["menuler"]);
        $masaNo = $this->filtre($post["masaNo"]);
        $tarih = time();

        $result = $this->db->update($id,$menuler,$masaNo);
        if($result)
        {
            $result2["status"] = 1;
            $result2["message"] = "sipariş güncellendi başarılı";
            echo json_encode($result2);
            die();
        }
    }

    // bir sipariş geldiği zaman bu method çalışıcak
    // bu method içerisinde aktif masa bulunursa o masa üzerinde güncelleme yapılacak. Eğer aktif masa yoksa yeni bir masa kaydı açılır.
    public function siparis()
    {
        if (isset($_POST["jwtrestoran"]))
        {
            $jwtrestoran = $_POST["jwtrestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);

            if ($restoran["oturum"])
            {
                $id = $this->filtre($_POST["restoranid"]);
                if ($id != $restoran["id"])
                {
                    $result["status"] = 0;
                    $result["message"] = "Bilgilerin doğruluğundan emin olun";
                    echo json_encode($result);
                    die();
                }
                $masaNo = $this->filtre($_POST["masaNo"]);
                $aktifmasa = $this->db->get($id,$masaNo);
                if ($aktifmasa)
                {
                    $this->update($_POST,$id);
                }
                else{
                    $this->insert($_POST,$id);
                }
            }
            else{
                $result["status"] = 0;
                $result["message"] = "Oturum doğrulanamadı";
                echo json_encode($result);
                die();
            }
        }
    }

    // müşterilerin oldukları masalar
    public function aktifMasalar()
    {
        if (isset($_POST["jwtrestoran"]))
        {
            $jwt = $_POST["jwtrestoran"];
            $restoran = $this->decodeJWT($jwt);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"])
            {
                $id = $restoran["id"];
                $result = $this->db->gets($id);
                if ($result)
                {
                    echo json_encode($result);
                    die();
                }
                else{
                    echo "veriler gelmedi";
                }
            }
        }
    }

    // aktif masanın sipariş verdikleri ve detayları
    public function aktifMasa()
    {
        if (isset($_POST["jwtrestoran"]))
        {
            $jwt = $_POST["jwtrestoran"];
            $restoran = $this->decodeJWT($jwt);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"])
            {
                $restoranid = $restoran["id"];
                $masano = $this->filtre($_POST["masaNo"]);
                $result = $this->db->get($restoranid,$masano);
                if ($result)
                {
                    $hesap =0;
                    $result["menuler"] = explode(",",$result["menuler"]);
                    foreach ($result["menuler"] as $key => $value)
                    {
                        $result["menuler"][$key] = explode("x",$result["menuler"][$key]);
                        $menuresult = $this->menuDb->get($result["menuler"][$key][0],$restoranid);
                        if ($menuresult)
                        {
                            $result["menuler"][$key][0] = $menuresult;
                            $hesap += $menuresult["menuFiyat"]*$result["menuler"][$key][1];
                        }
                    }
                    $result["hesap"] = $hesap." TL";
                    echo json_encode($result);
                    die();
                }
                else{
                    echo "veriler gelmedi";
                }
            }
        }
    }

    // masa hesap kapatma
    public function aktifMasaKapat()
    {
        if (isset($_POST["jwtrestoran"]) and isset($_POST["masaNo"]))
        {
            $jwtrestoran = $_POST["jwtrestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restoranDb->checkrestoran($restoran["restoranMail"]);

            if ($restoran["oturum"])
            {
                $masano = $this->filtre($_POST["masaNo"]);

            }
        }
    }


}