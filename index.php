<?php
//echo "hii";

define('DATABASE', 'sn478');
define('USERNAME', 'sn478');
define('PASSWORD', 'RJRNIIe0');
define('CONNECTION', 'sql1.njit.edu');
class dbConn{
    //variable to hold connection object.
    protected  $db;
    //private construct - class cannot be instatiated externally.
    public function __construct() {
        try {
            // assign PDO object to db variable
            $this->db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            $this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            echo 'Connection successfull';
        }
        catch (PDOException $e) {
        
            echo "Connection Error: " . $e->getMessage();
        }
    }
    
    public function getConnection() {
        
        
       if (!$this->db) {
      //new connection object.
      new dbConn();
      }
       
     else{  //return connection.
       return $this->db;
       }
       
}
}
$dbconn = new dbConn();
?>