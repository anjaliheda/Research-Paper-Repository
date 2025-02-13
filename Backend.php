<?php

class Backend {

    private $db;
    
    public function __construct(PDO $db) {
        session_start();
        $this->db = $db;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role_id'];
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    public function createUser($name, $email, $password, $role, $profileImagePath = null) {
        $sql = "INSERT INTO users (name, email, password, role_id, profile_image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $profileImagePath]);
    }

    public function getUser($id) {
        $query = "SELECT * FROM users WHERE user_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUsers() {
        $query = "SELECT * FROM users"; 
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    
    public function getAuthor($id) {
        $query = "SELECT * FROM users WHERE id = :id AND role_id = 2";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAuthors() {
        $query = "SELECT * FROM users WHERE role_id = 2";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateUser($id, $name, $email, $password, $role, $profileImagePath = null) {
        $sql = "UPDATE users SET name = ?, email = ?, password = ?, role_id = ?";
        $params = [$name, $email, password_hash($password, PASSWORD_DEFAULT), $role];
        if ($profileImagePath !== null) {
            $sql .= ", profile_image = ?";
            $params[] = $profileImagePath;
        }
        $sql .= " WHERE user_id = ?";
        $params[] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE user_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function createPaper($title, $authors, $keywords, $publication_info, $pdf_link, $featured) {
        $sql = "INSERT INTO papers (title, authors, keywords, publication_info, pdf_link, featured)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([$title, $authors, $keywords, $publication_info, $pdf_link, $featured])) {
            return true; 
        } else {
            return "Failed to create paper."; 
    }
    }
    

    public function getPaper($id) {
        $query = "SELECT * FROM papers WHERE paper_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getPapers() {
        $query = "
            SELECT 
                papers.*, 
                users.name AS author_name 
            FROM 
                papers 
            INNER JOIN 
                users 
            ON 
                users.user_id = papers.authors
        ";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updatePaper($paperId, $title, $authors, $keywords, $publication_info, $pdf_link, $featured) {
        $sql = "UPDATE papers SET title = ?, authors = ?, keywords = ?, publication_info = ?, pdf_link = ?, featured = ? WHERE paper_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([$title, $authors, $keywords, $publication_info, $pdf_link, $featured, $paperId])) {
            return true; 
        } else {
            return "Failed to update paper."; 
        }
    }
    

    public function deletePaper($id) {
        $query = "DELETE FROM papers WHERE paper_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function addFavorite($userId, $paperId) {
        // First check if already favorited
        $checkQuery = "SELECT * FROM favorites WHERE user_id = :user_id AND paper_id = :paper_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $userId);
        $checkStmt->bindParam(':paper_id', $paperId);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            // Already favorited, so remove it
            return $this->removeFavorite($userId, $paperId);
        }
        
        // Not favorited, so add it
        $query = "INSERT INTO favorites (user_id, paper_id) VALUES (:user_id, :paper_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':paper_id', $paperId);
        return $stmt->execute();
    }
    
    public function removeFavorite($userId, $paperId) {
        $query = "DELETE FROM favorites WHERE user_id = :user_id AND paper_id = :paper_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':paper_id', $paperId);
        return $stmt->execute();
    }
    
    public function getFavorites($userId) {
        try {
            $query = "
                SELECT 
                    p.paper_id,
                    p.title AS paper_title,
                    p.pdf_link,
                    u.name AS author_name
                FROM papers p
                JOIN favorites f ON p.paper_id = f.paper_id
                JOIN users u ON p.authors = u.user_id
                WHERE f.user_id = :user_id
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getFavorites: " . $e->getMessage());
            return false;
        }
    }

    public function isFavorited($userId, $paperId) {
        $query = "SELECT 1 FROM favorites WHERE user_id = :user_id AND paper_id = :paper_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':paper_id', $paperId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
}
?>