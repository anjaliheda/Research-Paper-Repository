<?php
require_once 'db_connection.php';

if (isset($_GET['q'])) {
    $searchTerm = $_GET['q'];

    try {
        $db = Database::getInstance()->connect();
        error_log('Database connection successful.');

        $query = "
            SELECT 
                papers.paper_id AS paper_id, 
                papers.title AS paper_title, 
                papers.pdf_link AS pdf_link, 
                users.name AS author_name 
            FROM papers 
            INNER JOIN users ON users.user_id = papers.authors
            WHERE 
                papers.title LIKE :searchTerm 
                OR users.name LIKE :searchTerm
                OR papers.keywords LIKE :searchTerm
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        
        // Log the number of rows fetched
        error_log('Rows fetched: ' . $stmt->rowCount());

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            error_log('No results found');
        } else {
            error_log('Results found: ' . print_r($results, true));
        }

        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result['paper_id'],
                'text' => $result['paper_title'] . ' - ' . $result['author_name'],
                'url' => $result['pdf_link'],
            ];
        }

        echo json_encode($data);

    } catch (PDOException $e) {
        error_log('PDO Error: ' . $e->getMessage());
        echo json_encode(['blank']);
    } catch (Exception $e) {
        error_log('General Error: ' . $e->getMessage());
        echo json_encode(['blank']);
    }
}
