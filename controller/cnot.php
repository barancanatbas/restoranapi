<?php
include_once 'config/controller.php';
include_once 'model/cnot.php';
include_once 'model/restoran.php';

class cnot extends controller
{
    protected $db;
    protected $restorandb;
    public function __construct()
    {
        parent::__construct();
        $this->db = new cnotdb();
        $this->restorandb = new restoranDb();
    }

    public function get()
    {
        if (isset($_POST["jwtRestoran"]) and isset($_POST["calisanid"]))
        {
            $jwtrestoran = $_POST["jwtRestoran"];
            $calisanid = $this->filtre($_POST["calisanid"]);
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restorandb->checkrestoran($restoran["restoranMail"]);
            if($restoran["oturum"])
            {
                $result = $this->db->get($calisanid);
                if ($result)
                {
                    echo json_encode($result);
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "veri yok";
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

    public function insert()
    {
        if (isset($_POST["jwtRestoran"]))
        {
            $jwtrestoran = $_POST["jwtRestoran"];
            $restoran = $this->decodeJWT($jwtrestoran);
            $restoran["oturum"] = $this->restorandb->checkrestoran($restoran["restoranMail"]);
            if ($restoran["oturum"] == true)
            {
                $calisanid = $this->filtre($_POST["calisanid"]);
                $note = $this->filtre($_POST["note"]);

                $result = $this->db->insert($calisanid,$note);
                if ($result)
                {
                    $result["status"] = 1;
                    $result["message"] = "Ekleme işlemi başarılı";
                    echo json_encode($result);
                }
                else{
                    $result["status"] = 0;
                    $result["message"] = "Ekleme yapılamadı";
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
