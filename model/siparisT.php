<?php
include_once 'config/model.php';

class siparisTdb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    // burdaki method tblsipariş tablosunda veriyi tblsiparist tablosuna aktarır.
    public function insert($restoranid,$masano)
    {
        try {
            $sorgu = $this->conn->prepare("insert into tblsiparist (restoranFK,calisanFK,masaNo,menuler,tarih) select restoranFK,calisanFK,masaNo,menuler,tarih from tblsiparis where masaNo =? and restoranFK =?;");
            $sorgu->execute([$masano,$restoranid]);
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