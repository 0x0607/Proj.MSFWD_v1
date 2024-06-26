<?php

/*************************************************************
 * 
 * 連線至DataBase
 * @param string $dbname 資料庫
 * @param string $host 位址
 * @param int $port 端口
 * @param string $user 用戶
 * @param string $passwd 密碼
 * @param string $charset 字符集
 * 
 *************************************************************/
class DBConnection
{
    private static $debugmode = false;
    private $connection;
    private $dbname;
    private $host;
    private $port;
    private $user;
    private $passwd;
    private $charset;
    /************************************************
     * ### DBConnection ###
     
     ************************************************/
    public function __construct($dbname, $host = "127.0.0.1", $port = 3306, $user = "root", $passwd = "root", $charset = 'utf8mb4')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->passwd = $passwd;
        $this->charset = $charset;
        $this->resetDBname($dbname);
    }
    /************************************************
     * ### 設置debug模式 ###
     * @param bool $isdebug 設置debug模式
     ************************************************/
    public static function deBugMode($debugmode = true)
    {
        self::$debugmode = $debugmode;
    }
    /************************************************
     * ### 設置DB資訊 ###
     * @param string $host 位址
     * @param int $port 端口
     * @param string $user 用戶
     * @param string $passwd 密碼
     * @param string $charset 字符集
     ************************************************/
    private function setDBConnection()
    {
        $dsn = "mysql:dbname=$this->dbname;host=$this->host;port=$this->port;charset=$this->charset";
        try {
            $this->connection = new PDO($dsn, $this->user, $this->passwd);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Exception $e) {
            if (self::$debugmode) exit($e);
            else exit('Error: Connect to MySQL was failed.');
        }
        //$link = new PDO($dsn,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_PERSISTENT => false));
    }
    /************************************************
     * ### 修改配置資料庫 ###
     * @param string $dbname 資料庫
     ************************************************/
    public function resetDBname($dbname)
    {
        $this->dbname = $dbname;
        $this->setDBConnection();
    }
    /************************************************
     * ### 抓取單筆資料 ###
     * @param string $SQL SQL語句
     * @param bool $print_r 是否直接印出
     ************************************************/
    public function single($SQL, $print_r = false) //: array | bool
    {
        try {
            $outputValue = $this->connection->query($SQL)->fetch(PDO::FETCH_ASSOC);
            return $print_r ? print_r($outputValue, true) : $outputValue;
        } catch (PDOException $e) {
            if (self::$debugmode) exit($e);
            else exit('Error: Single catch data was failed.');
        }
    }
    /************************************************
     * ### 抓取多筆資料 ###
     * @param string $SQL SQL語句
     * @param bool $print_r 是否直接印出
     ************************************************/
    public function each($SQL, $print_r = false) //: array | bool
    {
        try {
            $outputValue = $this->connection->query($SQL)->fetchAll(PDO::FETCH_ASSOC);
            return $print_r ? print_r($outputValue, true) : $outputValue;
        } catch (PDOException $e) {
            if (self::$debugmode) exit($e);
            else exit('Error: each catch datas was failed.');
        }
    }
    // 
    /************************************************
     * ### 抓取多筆預填入 ###
     * @param string $SQL SQL語句
     * @param array $DATA 代填入值
     * @param bool $print_r 是否直接印出
     ************************************************/
    public function prepare($SQL, $DATA = array(null), $print_r = false): array //| bool
    {
        try {
            if (!is_array($DATA)) {
                throw new Exception("Error: wrong data type.");
            }
            // $SQL = "SELECT * FROM table WHERE col1 = ?,col2 = ?, col3= ?"
            $value = $this->connection->prepare($SQL);
            // $DATA = ['col1DATA','col2DATA','col3DATA'];
            $value->execute($DATA);
            $outputValue = $value->fetchAll(PDO::FETCH_ASSOC);
            return $print_r ? print_r($outputValue, true) : $outputValue;
        } catch (PDOException $e) {
            if (self::$debugmode) exit($e);
            else exit('Error: prepare catch datas was failed.');
        }
    }
    /************************************************
     * ### 計算結果的行數 ###
     * @param string $SQL SQL語句
     * @param bool $print 是否直接印出
     ************************************************/
    // 棄用
    // public function queryCount($SQL, $print = false): int
    // {
    //     try {
    //         $value = $this->connection->query($SQL);
    //         $rowCount = $value->rowCount();
    //         return $print ? print($rowCount) : $rowCount;
    //     } catch (PDOException $e) {
    //         if (self::$debugmode) exit($e);
    //         else exit('Error: Counting rows failed.');
    //     }
    // }
    /************************************************
     * ### 計算結果的行數 ###
     * @param string $SQL SQL語句
     * @param array $DATA 代填入值
     * @param bool $print 是否直接印出
     ************************************************/
    // 棄用
    // public function prepareCount($SQL, $DATA = array(null), $print = false): int
    // {
    //     try {
    //         $value = $this->connection->prepare($SQL);
    //         $value->execute($DATA);
    //         $rowCount = $value->rowCount();
    //         return $print ? print($rowCount) : $rowCount;
    //     } catch (PDOException $e) {
    //         if (self::$debugmode) exit($e);
    //         else exit('Error: Counting rows failed.');
    //     }
    // }
}
