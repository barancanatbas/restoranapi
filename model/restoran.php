<?php
include_once 'config/model.php';

class restoranDb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login($mail,$pass)
    {

        try {
            $sorgu = $this->conn->prepare("select restoranAd,restoranTelefon,restoranMail,restoranMasaSayisi,id from tblrestoran where restoranMail =? and password =?");
            $sorgu->execute([$mail,$pass]);
            if ($sorgu and $sorgu->rowCount() > 0)
            {
                $restoran = $sorgu->fetch(PDO::FETCH_ASSOC);
                return $restoran;
            }
            else return false;

        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function checkrestoran($mail)
    {
        try {
            $sorgu = $this->conn->prepare("select id from tblrestoran where restoranMail =?");
            $sorgu->execute([$mail]);
            if ($sorgu->rowCount() >0) return true;
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function gets()
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblrestoran");
            $sorgu->execute();
            if ($sorgu)
            {
                $veri = $sorgu->fetchAll(PDO::FETCH_ASSOC);
                return $veri;
            }
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function get($id)
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblrestoran where id = ?");
            $sorgu->execute([$id]);
            if ($sorgu)
            {
                $veri = $sorgu->fetch(PDO::FETCH_ASSOC);
                return $veri;
            }
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function insert($ad,$telefon,$mail,$masaSayisi,$pass)
    {
        try {
            $sorgu = $this->conn->prepare("insert into tblrestoran (restoranAd,restoranTelefon,restoranMail,restoranMasaSayisi,password) values (?,?,?,?,?)");
            $sorgu->execute([$ad,$telefon,$mail,$masaSayisi,$pass]);
            if ($sorgu)
            {
                return true;
            }
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function updateGenel($ad, $telefon, $mail, $masaSayisi,$id)
    {
        try {
            $sorgu = $this->conn->prepare("update tblrestoran set restoranAd =?,restoranTelefon =?,restoranMail =?,restoranMasaSayisi =? where id =?");
            $sorgu->execute([$ad,$telefon,$mail,$masaSayisi,$id]);
            if ($sorgu)
            {
                return true;
            }
            else return false;

        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function updatePass($pas,$id)
    {
        try {
            $sorgu = $this->conn->prepare("update tblrestoran set password = ? where id =?");
            $sorgu->execute([$pas,$id]);
            if ($sorgu)
            {
                return true;
            }
            else return false;

        }catch (PDOException $e)
        {
            return false;
        }
    }
}
