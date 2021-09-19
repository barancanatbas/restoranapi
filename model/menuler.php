<?php
include_once 'config/model.php';

class menulerdb extends model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($menuId, $restoranId)
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblmenuler where id =? and restoranFK =?");
            $sorgu->execute([$menuId, $restoranId]);
            if ($sorgu->rowCount() > 0) {
                $veriler = $sorgu->fetch(PDO::FETCH_ASSOC);
                return $veriler;
            } else return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function gets($restoranid)
    {
        try {
            $sorgu = $this->conn->prepare("select * from tblmenuler where restoranFK =?");
            $sorgu->execute([$restoranid]);
            if ($sorgu->rowCount() > 0) {
                $veriler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
                return $veriler;
            } else return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function insert($menuad, $menuFiyat, $menuBilgi, $menurestoran, $yol, $qr)
    {
        try {

            $sorgu = $this->conn->prepare("insert into tblmenuler (menuAd,menuFiyat,menuBilgi,menuFoto,restoranFK,menuQR) values (?,?,?,?,?,?)");
            $sorgu->execute([$menuad, $menuFiyat, $menuBilgi, $yol, $menurestoran, $qr]);
            if ($sorgu) {
                return $this->conn->lastInsertId();
            } else return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id, $restoranId)
    {
        try {
            $sorgu = $this->conn->prepare("delete from tblmenuler where id = ? and restoranFK = ?");
            $sorgu->execute([$id, $restoranId]);
            if ($sorgu) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateGenel($menuad, $menuFiyat, $menuBilgi, $menuId, $restoranId)
    {
        try {

            $sorgu = $this->conn->prepare("update tblmenuler set menuAd = ?,menuFiyat = ?,menuBilgi = ? where id = ? and restoranFK = ?");
            $sorgu->execute([$menuad, $menuFiyat, $menuBilgi, $menuId, $restoranId]);
            if ($sorgu) {
                return true;
            } else return  false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateResim($resim, $menuId, $restoranId)
    {
        try {

            $sorgu = $this->conn->prepare("update tblmenuler set menuFoto = ? where id = ? and restoranFK = ?");
            $sorgu->execute([$resim, $menuId, $restoranId]);
            if ($sorgu) {
                return true;
            } else return  false;
        } catch (PDOException $e) {
            return false;
        }
    }
}
