<?php
class model{
    protected $host;
    protected $dbname;
    protected $password;
    protected $username;
    protected $conn;

    public function __construct()
    {
        $this->dbname ="restorandb";
        $this->host = "localhost:3306";
        $this->password = "mysql123";
        $this->username = "root";

        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname.";charset=utf8",$this->username,$this->password);
        }catch (PDOException $e)
        {
            echo "bir hata var";
            die();
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}