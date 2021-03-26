<?php 

    // connection related
    class Connection {

        public object $dbh;

        public function __construct($server, $db, $user, $pass) {
            $server = 'mysql:host=' . $server . ';';
            ($db) ? $db = 'dbname=' . $db : $db = '';

            $this->connection($server, $db, $user, $pass);
        }

        //class body 
        private function connection($server, $db, $user, $pass) {
            try {
                $this->dbh = new PDO($server . $db, $user, $pass);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                (isset($_SESSION)) ? : session_start();

            } catch(PDOException $e) {
                die();
            }
        }
        
            //request methods
        public function select($sql, $values) {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($values);

            $result = array();
            while($row = $sth->fetch()) {
                array_push($result, $row);
            }

            return $result;
        }

        public function insert($sql, $values) {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($values);
        }

            //uniqueness check
        public function isUnique($value, $table, $column) {
            $coincidence = $this->select('SELECT * FROM ' . $table . ' WHERE ' . $column . '=:value', [':value' => $value]);

            (empty($coincidence)) ? return true : return false;  
        }

        //connection closure
        public function __destruct() {
            $this->dbh == null;
        }
    }

    //***$values variable in request methods should be an associative array

?>