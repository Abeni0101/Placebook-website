<?php
class DB {
    private $host = 'localhost';
    private $user = 'root';
    private $pwd = '';
    private $db_name = 'place2';

    protected $conn;

    protected function connection() {
        $this->conn = new mysqli($this->host, $this->user, $this->pwd, $this->db_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    // Function to execute a prepared statement
    protected function executePrepared($sql, $params = []){
        $stmt = $this->connection()->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $this->close_conn();
        return $result;
        
    }

    
    protected function close_conn(){
        if($this->conn)
        {
            $this->conn->close();
        }
    }









    // // Function for SELECT queries
    // protected function select($sql, $params = []) {
    //     return $this->executePrepared($sql, $params);
    // }

    // // Function for INSERT queries
    // protected function insert($sql, $params = []) {
    //     $stmt = $this->conn->prepare($sql);
    //     if ($stmt === false) {
    //         return false;
    //     }

    //     $types = str_repeat('s', count($params));
    //     $stmt->bind_param($types, ...$params);

    //     if ($stmt->execute()) {
    //         return $this->conn->insert_id;
    //     } else {
    //         return false;
    //     }
    // }

    // // Function for UPDATE and DELETE queries
    // protected function modify($sql, $params = []) {
    //     return $this->executePrepared($sql, $params);
    // }
}
