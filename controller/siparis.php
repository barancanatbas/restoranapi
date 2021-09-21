<?php
include_once 'config/controller.php';
include_once 'model/siparis.php';
include_once 'model/calisanlar.php';
include_once 'model/menuler.php';
include_once "model/siparisT.php";

class siparis extends controller
{

    protected $db;
    protected $restoranDb;
    protected $menuDb;
    protected $siparisTdb;
    public function __construct()
    {
        parent::__construct();
        $this->db = new siparisdb();
        $this->restoranDb = new calisanlarDb();
        $this->menuDb = new menulerdb();
        $this->siparisTdb = new siparisTdb();
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
    private function update($post,$restoranid)
    {
        $menuler = $this->filtre($post["menuler"]);
        $masaNo = $this->filtre($post["masaNo"]);

        $result = $this->db->update($restoranid,$menuler,$masaNo);
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
        if (isset($_POST["jwtcalisan"]))
        {
            $jwtcalisan = $_POST["jwtcalisan"];
            $calisan = $this->decodeJWT($jwtcalisan);
            $calisan["oturum"] = $this->calisandb->checkcalisan($calisan["tc"]);
            die(json_encode($calisan));
            if ($calisan["oturum"])
            {
                $restoranFK = $calisan["restoranFK"];
                $masaNo = $this->filtre($_POST["masaNo"]);

                $aktifmasa = $this->db->get($id,$masaNo);
                if ($aktifmasa)
                {
                    $this->update($_POST,$restoranFK);
                }
                else{
                    $this->insert($_POST,$restoranFK);
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
        if (isset($_POST["jwtcalisan"]))
        {
            $jwt = $_POST["jwtcalisan"];
            $calisan = $this->decodeJWT($jwt);
            $calisan["oturum"] = $this->calisandb->checkcalisan($calisan["tc"]);
            if ($calisan["oturum"])
            {
                $restoranId = $calisan["restoranFK"];
                $result = $this->db->gets($restoranId);
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
        if (isset($_POST["jwtcalisan"]))
        {
            $jwt = $_POST["jwtcalisan"];
            $calisan = $this->decodeJWT($jwt);
            $calisan["oturum"] = $this->calisandb->checkcalisan($calisan["tc"]);
            if ($calisan["oturum"])
            {
                $restoranid = $calisan["restoranFK"];
                $masano = $this->filtre($_POST["masaNo"]);
                $result = $this->db->get($restoranid,$masano);
                if ($result)
                {
                    $hesap =0;
                    $result["menuler"] = explode(",",$result["menuler"]);

                    foreach ($result["menuler"] as $key=>$value)
                    {
                        $veri = explode("x",$result["menuler"][$key]);
                        $menu = $veri[0];
                        $adet = $veri[1];

                        $menuveri = $this->menuDb->get($menu,$result["restoranFK"]);

                        $hesap += (int)$adet * (int)$menuveri["menuFiyat"];
                        $menuveri["adet"] = $adet;

                        $result["menuler"][$key] = $menuveri;
                    }
                    $result["hesap"] = $hesap;
                    echo json_encode($result);
                }
                else{
                    echo "veriler gelmedi";
                }
            }
        }
    }

    // masa hesap kapatma
    public function hesapode()
    {
        if (isset($_POST["jwtcalisan"]) and isset($_POST["masaNo"]))
        {
            $jwtcalisan = $_POST["jwtcalisan"];
            $calisan = $this->decodeJWT($jwtcalisan);
            $calisan["oturum"] = $this->calisandb->checkcalisan($calisan["tc"]);

            if ($calisan["oturum"])
            {
                $masano = $this->filtre($_POST["masaNo"]);
                $restoranFK= $calisan["restoranFK"];

                $result = $this->siparisTdb->insert($restoranFK,$masano);
                if ($result)
                {
                    $result2 = $this->db->delete($restoranFK,$masano);
                    if ($result2)
                    {
                        $result3["status"] = 1;
                        $result3["message"] = "hesap kapatıldı";
                        echo json_encode($result3);
                        die();
                    }
                    else{
                        $result3["status"] = 0;
                        $result3["message"] = "işlem başarısız";
                        echo json_encode($result3);
                        die();
                    }
                }
                $result3["status"] = 0;
                $result3["message"] = "hesap kapatıldı";
                echo json_encode($result3);
                die();
            }
        }
    }



    /*
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
     *
     * */
}