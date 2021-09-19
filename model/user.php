<?php
include_once 'config/model.php';

class userDb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login($username,$pass)
    {
        try {
            $sorgu = $this->conn->prepare("select id,ad,soyad,username,mail,telefon from tbluser where username = ? and password =?");
            $sorgu->execute([$username,$pass]);
            $rowcount = $sorgu->rowCount();
            if ($rowcount > 0)
            {
                $user = $sorgu->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function checkuser($username)
    {
        try {
            $sorgu = $this->conn->prepare("select id,ad from tbluser where username = ?");
            $sorgu->execute([$username]);
            $rowcount = $sorgu->rowCount();
            if ($rowcount > 0)
            {
                $user = $sorgu->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }

    public function insert($ad,$soyad,$username,$password,$mail,$telefon)
    {
        try{
            $sorgu = $this->conn->prepare("insert into tbluser (ad,soyad,username,password,mail,telefon) values (?,?,?,?,?,?)");
            $sorgu->execute([$ad,$soyad,$username,$password,$mail,$telefon]);
            if($sorgu)
            {
                return true;
            }
            else return false;
        }catch(PDOExcaption $e)
        {
            return false;
        }
    }

    public function updateGenel($ad,$soyad,$username,$mail,$telefon,$id)
    {
        try{
            $sorgu = $this->conn->prepare("update tbluser set ad =?,soyad=?,username=?,mail =?,telefon=? where id =?");
            $sorgu->execute([$ad,$soyad,$username,$mail,$telefon,$id]);
            if($sorgu)
            {
                return true;
            }
            else return false;
        }catch(PDOExcaption $e)
        {
            return false;
        }
    }

    public function updatePass($sifre,$id)
    {
        try{
            $sorgu = $this->conn->prepare("update tbluser set password =? where id =?");
            $sorgu->execute([$sifre,$id]);
            if($sorgu)
            {
                return true;
            }
            else return false;
        }catch(PDOExcaption $e)
        {
            return false;
        }
    }
}