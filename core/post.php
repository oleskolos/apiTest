<?php

class Post {
    private $conn;
    private $table = "users";

    public $id;
    public $phone;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validateData() {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400); 
            echo json_encode(array('message' => 'Invalid email address'));
            exit;
        }
    
        $this->phone = str_replace('+', '', $this->phone);
    
        if (strlen($this->phone) < 10 || !ctype_digit($this->phone)) {
            http_response_code(400);
            echo json_encode(array('message' => 'Invalid phone number'));
            exit;
        }
    
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email OR phone = :phone';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':phone', $this->phone);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            http_response_code(409); 
            echo json_encode(array('message' => 'Duplicate email or phone number'));
            exit;
        }
    }

    public function create() {
        try {
            $this->validateData();
    
            $query = 'INSERT INTO ' . $this->table . ' SET phone = ?, email = ?';
    
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(1, $this->phone, PDO::PARAM_STR);
            $stmt->bindParam(2, $this->email, PDO::PARAM_STR);
    
            if ($stmt->execute()) {
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
    
                if (strpos($errorInfo[2], 'Duplicate entry') !== false) {
                    http_response_code(409); 
                    echo json_encode(array('status' => 'error', 'message' => 'Duplicate email or phone number'));
                } elseif (strpos($errorInfo[2], 'Invalid phone number') !== false) {
                    http_response_code(400); 
                    echo json_encode(array('status' => 'error', 'message' => 'Invalid phone number'));
                } elseif (strpos($errorInfo[2], 'Invalid email address') !== false) {
                    http_response_code(400); 
                    echo json_encode(array('status' => 'error', 'message' => 'Invalid email address'));
                } else {
                    http_response_code(500); 
                    echo json_encode(array('status' => 'error', 'message' => 'Internal server error.'));
                }
    
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500); 
            echo json_encode(array('status' => 'error', 'message' => 'Internal server error.'));
            exit;
        }
    }
    

    public function read_single() {
        $query = 'SELECT
        p.id,
        p.phone,
        p.email
        FROM    
        ' .$this->table . ' p';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->phone = $row['phone'];
        $this->email = $row['email'];
    }
}
?>
