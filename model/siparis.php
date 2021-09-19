<?php 
include_once 'config/model.php';

class siparisdb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert($id,$calisanfk,$menuler,$masaNo,$tarih)
    {
        try {
            $sorgu = $this->conn->prepare("insert into tblsiparis (restoranFK,calisanFK,menuler,durum,tarih,masaNo) values (?,?,?,?,?,?)");
            $sorgu->execute([$id,$calisanfk,$menuler,0,$tarih,$masaNo]);
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

    public function update($restoranid,$menuler,$masaNo)
    {
        try {
            // gelen değeri var olan değerim üzerinde yazar.
            $sorgu = $this->conn->prepare("update tblsiparis set menuler = CONCAT(menuler,',',?) where masaNo = ? and restoranFK = ?");
            $sorgu->execute([$menuler,$masaNo,$restoranid]);
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

    public function gets($id)
    {
        try {
            $sorgu = $this->conn->prepare("select masaNo,durum from tblsiparis where restoranFK =?");
            $sorgu->execute([$id]);
            if ($sorgu and $sorgu->rowCount() > 0)
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

    public function getsForMasa($restoranId,$masaNo)
    {
        try {
            $sorgu = $this->conn->prepare("select  from tblsiparis where restoranFK =? and masaNo = ?");
            $sorgu->execute([$id]);
            if ($sorgu and $sorgu->rowCount() > 0)
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

    public function get($restoranid,$masano)
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblsiparis where restoranFK =? and masaNo = ?");
            $sorgu->execute([$restoranid,$masano]);
            if ($sorgu and $sorgu->rowCount() > 0)
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

    public function delete($restoranid,$masano)
    {
        try {
            $sorgu = $this->conn->prepare("delete from tblsiparis where masaNo =? and restoranFK =?");
            $sorgu->execute([$masano,$restoranid]);
            if ($sorgu) return true;
            else return false;
        }catch (PDOException $e)
        {
            return false;
        }
    }
}