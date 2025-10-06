<?php
/*
// Usage
$dbHandler = new DBHandler($dbh);
$data = $_POST;
$table = "your_table_name";
$primaryKey = "id"; // The primary key field of your table
$exclude = ['submit', 'another_key_to_exclude']; // Add the keys you want to exclude

// For insert
$dbHandler->insert($table, $data, $exclude);

// For update
$id = 1; // The id of the record you want to update
$dbHandler->update($table, $primaryKey, $id, $data, $exclude);

// For delete
$id = 1; // Replace with the id of the record you want to delete
$dbHandler->delete($table, $primaryKey, $id);
*/

class DBHandler {
    private $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    private function sanitizeInput($data, $exclude = []) {
        $sanitizedData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $exclude)) {
                continue;
            }
            $sanitizedKey = htmlspecialchars(strip_tags($key));
            if (is_array($value)) {
                // Handle array input - you might want to recursively call sanitizeInput 
                // or convert it to string or any other logic.
                $sanitizedValue = implode(", ", $value);  // Just an example to convert array to string
            } else {
                $sanitizedValue = htmlspecialchars(strip_tags($value));
            }
            $sanitizedData[$sanitizedKey] = $sanitizedValue;
        }
        return $sanitizedData;
    }
    

    public function insert($table, $data, $exclude = []) {
        $sanitizedData = $this->sanitizeInput($data, $exclude);
        $fields = array_keys($sanitizedData);
        $values = array_values($sanitizedData);

        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        $sql = "INSERT INTO $table (" . implode(",", $fields) . ") VALUES ($placeholders)";
        
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);
    }

    public function update($table, $primaryKeys, $data, $exclude = []) {
        $sanitizedData = $this->sanitizeInput($data, $exclude);
        $fields = array_keys($sanitizedData);
        $values = array_values($sanitizedData);
    
        $sql = "UPDATE $table SET ";
        foreach ($fields as $field) {
            $sql .= "$field = ?, ";
        }
        $sql = rtrim($sql, ", "); // Remove last comma
    
        $whereClause = " WHERE ";
        foreach ($primaryKeys as $key => $value) {
            $whereClause .= "$key = ? AND ";
            $values[] = $value; // Add the value to the end of the values array
        }
        $whereClause = rtrim($whereClause, "AND "); // Remove last AND
    
        $sql .= $whereClause;
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);
    }
    
    public function delete($table, $primaryKeys) {
        $sql = "DELETE FROM $table WHERE ";
        $values = [];
    
        foreach ($primaryKeys as $key => $value) {
            $sql .= "$key = ? AND ";
            $values[] = $value; // Add the value to the values array
        }
    
        $sql = rtrim($sql, "AND "); // Remove last AND
    
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($values);
    }

    public function testSanitizeInput($data, $exclude = []) {
        return $this->sanitizeInput($data, $exclude);
    }
}


?>