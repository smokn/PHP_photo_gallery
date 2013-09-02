<?php

/*
 * Class for the database 
 */

    //For getting database constants
    include 'db_config.php';
    
    class MySQLDatabase implements IDBAdapter {
        
        public $last_query = null;
        private $connection = null;
        private $query = null;
        private $db = null;
        
        function __construct(){
            $this->open_connection();
        }
        
        /*
         * $connection getter 
         */
        public function get_connection(){
            return isset($this->db) ? $this->connection : null;
        }
        
        
        /*
         * Method for openning the connection 
         */
        private function open_connection(){
//            $this->connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            $connection_string = "mysql:host=". DB_HOST;
            $connection_string .= ";dbname=". DB_NAME;
            $this->db = new PDO($connection_string, DB_USERNAME, DB_PASSWORD); 
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(!$this->db){
                die("Error while openning the connection.");
            }
        }
                
        /*
         * Querying method
         */        
        public function query($sql,array $param = null){
            $this->last_query = $sql;
            try{
                $query = $this->db->prepare($sql);
                $query->execute($param);
                $this->query = $query;
            } catch(PDOException $e){
                $this->confirm_query($query);
            }
            return $query;
        }
        
        /*
         * Helper method : for confirming query
         */
        private function confirm_query($query){
            if($query->errorInfo()[0] != 0){
                $output = "<hr>";
                $output .= $query->errorInfo()[2];
                $output .= "<hr>";
                $output .= "Last query : " . $this->last_query;
                $output .= "<hr>";
                die($output);                
            }
        }
        
        /*
         * Helper method : for fetching result
         */
        public function fetch_result($result){
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        
        /*
         * Helper method : for getting number of rows for a query
         */
        public function num_rows($result){
            return $result->rowCount();
        }
        
        /*
         * Helper method : for getting number of affected rows
         */
        public function affected_rows(){
            return $this->query->rowCount();
        }
        
        /*
         * Helper method : for getting id of the latest inserted row
         */
        public function inserted_id(){
            return $this->db->lastInsertId();
        }
        
        /*
         * Method for closing the connection
         */
        public function close_connection(){
            if(isset($this->db)){
                $this->db = null;
            }
        }
    }

?>
