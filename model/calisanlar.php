<?php
include_once 'config/model.php';

class calisanlarDb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function gets($restoranFk)
    {
        try {
            $sorgu = $this->conn->prepare("select c.ad,c.soyad,c.mail,c.id,m.meslekAd from tblcalisan c inner join tblmeslek m on c.meslekFK = m.id where restoranFK = ?");
            $sorgu->execute([$restoranFk]);
            if ($sorgu)
            {
                return $sorgu->fetchAll(PDO::FETCH_ASSOC);
            }
            else return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get($id)
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblcalisan where id = ?");
            $sorgu->execute([$id]);
            if ($sorgu) {
                return $sorgu->fetch(PDO::FETCH_ASSOC);
            } else return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function login($tc, $sifre)
    {
        try {
            $sorgu = $this->conn->prepare("select ad,soyad,mail,telefon,tc,status,id,restoranFK from tblcalisan where tc = ? and sifre = ?");
            $sorgu->execute([$tc, $sifre]);
            $rowcount = $sorgu->rowCount();
            if ($sorgu and $rowcount > 0) {
                $calisan = $sorgu->fetch(PDO::FETCH_ASSOC);
                return $calisan;
            } else return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function checkcalisan($tc)
    {
        try {
            $sorgu = $this->conn->prepare("select ad from tblcalisan where tc = ?");
            $sorgu->execute([$tc]);
            if ($sorgu->rowCount() == 1) {
                return true;
            } else return false;
        } catch (PDOException $e) {
            return false;
        }
    }


    public function insert($veri)
    {
        try {

            $sorgu = $this->conn->prepare("insert into tblcalisan (ad,soyad,tc,telefon,mail,adres,restoranFK,sifre,status) values (?,?,?,?,?,?,?,?,?)");
            $sorgu->execute([$veri["ad"], $veri["soyad"], $veri["tc"], $veri["tel"], $veri["mail"], $veri["adres"], $veri["restoranFK"], $veri["sifre"], $veri["status"]]);

            if ($sorgu) {
                return true;
            } else return false;
        } catch (PDOException $exception) {
            return false;
        }
    }

    public function updateGenel($veri)
    {
        try {
            $sorgu = $this->conn->prepare("update tblcalisan set ad =?,soyad =?,telefon =?,mail =?,adres =? where id = ? and restoranFK = ?");
            $sorgu->execute([$veri["ad"], $veri["soyad"], $veri["tel"], $veri["mail"], $veri["adres"], $veri["id"], $veri["restoranFK"]]);
            if ($sorgu) return true;
            else return false;
        } catch (PDOException $exception) {
            return false;
        }
    }

    public function delete($id, $restoranid)
    {
        try {
            $sorgu = $this->conn->prepare("delete from tblcalisan where id =? and restoranFK = ?");
            $sorgu->execute([$id, $restoranid]);
            if ($sorgu) return true;
            else return false;
        } catch (PDOException $exception) {
            return false;
        }
    }
}
