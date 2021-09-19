<?php
include_once 'config/model.php';

class cnotdb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($calisanid)
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblcalisannot where calisanFK =?");
            $sorgu->execute([$calisanid]);
            if ($sorgu->rowCount() > 0)
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

    public function insert($calisanid,$note)
    {
        try {
            $sorgu = $this->conn->prepare("insert into tblcalisannot (calisanFK,note) values (?,?)");
            $sorgu->execute([$calisanid,$note]);
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