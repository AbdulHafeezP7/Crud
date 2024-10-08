<?php
class Database
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "real_madrid";
    protected $conn;

    // Constructor
    public function __construct()
    {
        try {
            $dsn = "mysql:host={$this->servername};dbname={$this->dbname};charset=utf8";
            $options=array(PDO::ATTR_PERSISTENT);
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
?>
