<?php
//Defining Global variables
define('DATABASE', 'sn478');
define('USERNAME', 'sn478');
define('PASSWORD', 'RJRNIIe0');
define('CONNECTION', 'sql1.njit.edu');


//class to connect to datbase
class dbConn{
    protected static $db;
    private function __construct() {
        try {
            
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch (PDOException $e) {
            
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // To get connection for a single instance
    public static function getConnection() {
       
        if (!self::$db) {
            
            new dbConn();
        }
        return self::$db;
    }
}


//Collection class to show select statements
class collection {
protected $html;
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
}

//Accounts collection extended
class accounts extends collection {
    protected static $modelName = 'account';
}
//Todos collection extended
class todos extends collection {
    protected static $modelName = 'todo';
}

//Class to perform save,insert,delete and update records
class model {
protected $tableName;
public function save()
    
    {
        if ($this->id != '') {
            $sql = $this->update($this->id);
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
    private function insert() {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
    private function update($id) {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if( ! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$id;
        return $sql;
    }
    public function delete($id) {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id='.$id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
    
//Accounts model extended
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
        $tableName='accounts';
        return $tableName;
    }
}
//Todos model extended
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
        $tableName='todos';
        return $tableName;
    }
}

/*PRINITING THE RECORDS*/

// Printing "ACCOUNTS" table
echo"<h1>Search accounts table</h1>";
$records = accounts::findAll();
  
  $html = '<table>';
  $html .='<link rel="stylesheet" href="style.css" type="text/css">';
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    ;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    print_r($html);

//Finding by ID
    echo"<h1>Search account table by id</h1>";
$record = accounts::findOne(4);
  $html = '<table>';
  $html .='<link rel="stylesheet" href="style.css" type="text/css">';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    
    print_r($html);

// Inserting Record
 echo "<h1>Insert One Record</h1>";
$record = new account();
$record->email="testnjit.edu";
$record->fname="hh";
$record->lname="hhhh";
$record->phone="66697";
$record->birthday="00-00-0000";
$record->gender="male";
$record->password="12345";
$record->save();
$records = accounts::findAll();
$html = '<table>';
$html .='<link rel="stylesheet" href="style.css" type="text/css">';
$html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</table>';
echo "<h3>Table</h3>";
print_r($html);
//------------------------- Delete Record -------------------sjp77
echo "<h1>Delete One Record</h1>";
$record= new account();
$id=7;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';
//'<h3>After Delete</h3>';
$record = accounts::findAll();
//print_r($records);
$html = '<table>';
  // Displaying Header Row ...... hh292
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
echo "<h3>After Deleteing</h3>";
print_r($html);
//-----------------------------Update Record-------------------sjp77
echo "<h1>Update One Record</h1>";
$id=4;
$record = new account();
$record->id=$id;
$record->fname="fname_Update";
$record->lname="lname_Update";
$record->gender="gender_Update";
$record->save();
$record = accounts::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table>';
  // Displaying Header Row ...... sjp77
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
 
 print_r($html);
//------------------End Of Account Table -----------------------sjp77
 echo"<h1><marquee> TODO TABLE FUNCTION STARTS HERE</marquee></h1>";
//--------------- Todo Table-------------------------sjp77
 echo "<h1>Search all for todo table</h1>";
 $records = todos::findAll();
 // to print all accounts records in html table  
  $html = '<table>';
  // Displaying Header Row ...... sjp77
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
    echo "Todo table";
    print_r($html);
//------------------Find Unique id-------------------sjp77
    echo"<h1>Search by uniqui id</h1>";
 $record = todos::findOne(3);
 // Displaying Header Row ...... sjp77
  print_r("Todo table id - 3");
  
  $html = '<table>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
    
    print_r($html);
//-------------------------Insert Record-----------------sjp77
   echo "<h2>Insert One Record</h2>";
        $record = new todo();
        $record->owneremail="sjp77@njit.edu";
        $record->ownerid=06;
        $record->createddate="11-09-2017";
        $record->duedate="11-13-2017";
        $record->message="Active record Assignment";
        $record->isdone=1;
        $record->save();
        $records = todos::findAll();
        echo"<h3>After Inserting</h3>";
 
     $html = '<table>';
  // Displaying Header Row ...... sjp77
  
      $html .= '<tr>';
      foreach($records[0] as $key=>$value)
         {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
//echo "<h3>After Inserting</h3>";
print_r($html);
//------------------------------Delete record for todo ------------------sjp77
echo "<h1>Delete One Record</h1>";
$record= new todo();
$id=7;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';
//'<h3>After Delete</h3>';
$record = todos::findAll();
//print_r($records);
$html = '<table>';
  // Displaying Header Row ...... hh292
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
echo "<h3>After Deleteing</h3>";
print_r($html);
//------------------------Update todos record -------------------------------------------------sjp77
echo "<h1>Update One Record</h1>";
$id=4;
$record = new todo();
$record->id=$id;
$record->owneremail="shivangi@gmail.com";
$record->ownerid="77";
$record->createddate="01-02-1995";
$record->duedate="02-01-1995";
$record->message="hi this is shiv";
$record->isdone="1";
$record->save();
$record = todos::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table>';
  // Displaying Header Row ...... sjp77
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    // Displayng Data Rows .......sjp77
    
    //$i = 0;
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }
    $html .= '</table>';
 
 print_r($html);
?>