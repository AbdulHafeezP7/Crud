<?php 
require_once 'Database.php';

class Player extends Database
{
    protected $tableName = "players";
    
    // Function to add players
    public function addPlayer($data){
        if(!empty($data)){
            $fields = $placeholders = [];
            foreach($data as $field => $value){
                $fields[] = $field;
                $placeholders[] = ":{$field}";
            }
            $sql = "INSERT INTO {$this->tableName} (".implode(',', $fields).") VALUES (".implode(',', $placeholders).")";
            
            $stmt = $this->conn->prepare($sql);
            try {
                $this->conn->beginTransaction();
                $stmt->execute($data);
                $lastInsertedId = $this->conn->lastInsertId();
                $this->conn->commit();  
                return $lastInsertedId;
            } catch(PDOException $e) {
                echo "Error: ".$e->getMessage();
                $this->conn->rollBack();
            }
        }
    }
    
    // Function to get rows
    public function getRows($start = 0, $limit = 4){
        $sql = "SELECT * FROM {$this->tableName} ORDER BY player_id DESC LIMIT {$start}, {$limit}"; 
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $results = [];
        }
        return $results;
    }
    
    // Function to get a single row
    public function getRow($field, $value){
        $sql = "SELECT * FROM {$this->tableName} WHERE {$field} = :{$field}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":{$field}", $value);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $result = [];
        }
        return $result;
    }

    
    // Function to count number of rows
    public function getCount(){
        $sql = "SELECT count(*) as pcount FROM {$this->tableName}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pcount'];
    }
    
    // Function to upload photo
    public function uploadPhoto($file) {
        if (!empty($file['name'])) {
            $fileTempPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileType = $file['type'];
            $fileNameCmps = explode('.', $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $allowedExtn = ["png", "jpg", "jpeg"];
    
            if (in_array($fileExtension, $allowedExtn)) {
                $uploadFileDir = getcwd() . '/uploads/';
                $destFilePath = $uploadFileDir . $newFileName;
    
                if (move_uploaded_file($fileTempPath, $destFilePath)) {
                    return $newFileName;
                }
            }
        }
        return false;
    }
    
    // function to update player data
    public function update($data, $id) {
        if (!empty($data) && !empty($id)) {
            $fields = '';
            $x = 1;
            $fieldsCount = count($data);
            foreach ($data as $field => $value) {
                $fields .= "{$field} = :{$field}";
                if ($x < $fieldsCount) {
                    $fields .= ", ";
                }
                $x++;
            }
            $sql = "UPDATE {$this->tableName} SET {$fields} WHERE player_id = :player_id";
    
            $stmt = $this->conn->prepare($sql);
            try {
                $this->conn->beginTransaction();
                $data['player_id'] = $id;  
                $stmt->execute($data);
                $this->conn->commit(); 
                return true; 
            } catch(PDOException $e) {
                echo "Error: ".$e->getMessage();
                $this->conn->rollBack();
                return false;  
            }
        }
        return false; 
    }    
    
    // Function to delete player data
    public function delete($id) {
        $sql = "DELETE FROM {$this->tableName} WHERE player_id = :player_id";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([':player_id' => $id]);
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
     // function for viewing players profile
     public function viewProfile($id) {
        // Check if ID is provided
        if (!empty($id)) {
            $player = $this->getRow('player_id', $id);
            if (!empty($player)) {
                return $player;
            } else {
                return ['error' => 'Player not found'];
            }
        } else {
            return ['error' => 'Player ID is required'];
        }
    }  
    
    // Function to search players
    public function searchRows($query, $start = 0, $limit = 4){
        $sql = "SELECT * FROM {$this->tableName} 
                WHERE player_name LIKE :query 
                OR player_email LIKE :query 
                OR player_nationality LIKE :query 
                OR player_position LIKE :query
                ORDER BY player_id DESC LIMIT {$start}, {$limit}"; 
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':query', '%'.$query.'%');
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $results = [];
        }
        return $results;
    }

    // Function to count number of search results
    public function getSearchCount($query){
        $sql = "SELECT count(*) as pcount FROM {$this->tableName} 
                WHERE player_name LIKE :query 
                OR player_email LIKE :query 
                OR player_nationality LIKE :query 
                OR player_position LIKE :query";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':query', '%'.$query.'%');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pcount'];
    }

}
?>
