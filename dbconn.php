<?php
//db connection class using singleton pattern
class dbConn{
 
 //variable to hold connection object.
 protected static $db;
  
  //private construct - class cannot be instatiated externally.
  private function __construct() {
   
   try {
   // assign PDO object to db variable
   self::$db = new PDO( 'mysql:host=sql1.njit.edu;dbname=sn478', 'sn478', 'RJRNIIe0' );
   self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
   echo "Connection success ";
   }
   catch (PDOException $e) {
   //Output error - would normally log this to error file rather than output to user.
   echo "Connection Error: " . $e->getMessage();
   }
    
    }
     
     // get connection function. Static method - accessible without instantiation
     public static function getConnection() {
      
      //Guarantees single instance, if no connection object exists then create one.
      if (!self::$db) {
      //new connection object.
      new dbConn();
      }
       
       //return connection.
       return self::$db;
       }
}
$db = dbConn::getConnection();
//print_r($db);
class collection {
    static protected $table;
    static protected $modelClass;
    protected $records;
    static private function setTable()
    {
        self::$table;
    }
     protected function loadCollection() {
        $db = dbConn::getConnection();
        $table = self::$table;
        $class = self::$modelClass;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare("SELECT * FROM :tableName");
        $stmt->execute(array(':tableName'=> get_class($this)));
        //$records = $db->prepare("SELECT * FROM $table")->setFetchMode(PDO::FETCH_CLASS, $class)->fetchAll();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
        $stmt->execute();
        $records = $stmt->fetchAll();
       return $records;
    }
}
class accounts extends collection {
    public function __construct()
    {
        collection::$table = 'accounts';
        collection::$modelClass = 'account';
        $records = self::loadCollection();
        $this->records = $records;
    }
}
$accounts = new accounts();
print_r($accounts);
?>