<?php
  class Database {
    //DB params
    private $host = ''; //enter host
    private $db_name = ''; //enter database name
    private $username = ''; //enter username
    private $password = ''; //enter password
    private $conn;

    //DB connect
    public function connect() {
      $conn = mysqli_connect('ftp.digiterahost.com', 'leadfeas_bruh', 'JHTygzziLso3', 'leadfeas_dude') or die(mysqli_connect_error());
      
      return $conn;
    }

    public function find_row($conn, $table, $column, $param) {
      //prepare statement
      $stmt = $conn->prepare("SELECT * FROM ".$table." WHERE ".$column." = ?");
      //bind data
      $stmt->bind_param("s", $param);
      //execute query
      if(!$stmt->execute()){
        return false;
      }
      $result = $stmt->get_result();
      $user_info = $result->fetch_assoc();
      return $user_info;
    }

    /*
      $param array structure

      {
        {"column","value"},
        {"column","value"},
        etc,,,
      }
    */
    public function insert_values($conn, $table, $params) {
      foreach($params as $query_values){
        $columns[] = $query_values[0];
        $values[] = $query_values[1];
      }
      $placeholders = array_fill(0, count($params), '?');
      $columns = implode(",", $columns);
      $string_values = implode(",", $values);
      $placeholders = implode(",",$placeholders);

      //create query and prepare statement
      $stmt = $conn->prepare("INSERT INTO ".$table."(".$columns.") VALUES (".$placeholders.")");

      //bind data
      $types = str_repeat('s', count($params));

      $vals = '';
      for ($i = 0; $i<count($values); $i++){ // build type string and parameters
        $vals .= '$values[' . $i . '],';
      }

      $sql_stmt = '$stmt->bind_param("' . $types . '",' . $vals . ');'; // put bind_param line together
      echo $sql_stmt;
      eval($sql_stmt); // execute bind_param

      //execute query
      if($stmt->execute()) {
        return true;
      }

      return false;
    }

  }

?>
