<?php
require_once 'db_connection.php';
require_once 'Backend.php';

try {
    $database = Database::getInstance();
    $dbConnection = $database->connect();

    $backend = new Backend($dbConnection);
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'login':
            $email = $_POST['email'];
            $password = $_POST['password'];
            $result = $backend->login($email, $password);
            if ($result === true) {
                http_response_code(200); 
                echo json_encode(['success' => true]);
            } else {
                http_response_code(401); 
                echo json_encode(['success' => false, 'message' => $result]); 
            }
            break;

            case 'logout':
                
                $result = $backend->logout();
                if ($result === true) {
                    http_response_code(200); 
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(401); 
                    echo json_encode(['success' => false, 'message' => $result]); 
                }
                break;

            case 'deleteUser':
                    $id = $_POST['id'];
                    $result = $backend->deleteUser($id);
                    if ($result === true) {
                        http_response_code(200);
                        echo json_encode(['success' => true]);
                    } else {
                        http_response_code(401); 
                        echo json_encode(['success' => false, 'message' => $result]); 
                    }
                    break;
            case 'getAuthors':
                        
                        $result = $backend->getAuthors();
                        if ($result !== false && count($result) > 0) {
                            http_response_code(200); 
                            echo json_encode(['success' => true,'data'=>$result]);
                        } else {
                            http_response_code(401); 
                            echo json_encode(['success' => false, 'message' => $result]); 
                        }
                        break;
            case 'getUsers':
                    $result = $backend->getUsers();
                
                    if ($result !== false && count($result) > 0) {
                        http_response_code(200); 
                        echo json_encode(['success' => true, 'data' => $result]); 
                    } else {
                        http_response_code(404); 
                        echo json_encode(['success' => false, 'message' => 'No users found or an error occurred.']);
                    }
                    break;                

                    case 'getUser':
                        $user_id = $_POST['user_id'];
                        $result = $backend->getUser($user_id);
                        if ($result) {
                            http_response_code(200); 
                            echo json_encode([
                                'success' => true,
                                'data' => $result 
                            ]);
                        } else {
                            http_response_code(404); 
                            echo json_encode([
                                'success' => false,
                                'message' => 'User not found or an error occurred'
                            ]);
                        }
                        break;
                    

        case 'createUser':
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'];
        
            $profileImagePath = null; 
            if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profileImage']['tmp_name'];
                $fileName = $_FILES['profileImage']['name'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
        
                $uploadDir = 'uploads/profile_images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); 
                }
        
                $profileImagePath = $uploadDir . $newFileName;
        
                if (!move_uploaded_file($fileTmpPath, $profileImagePath)) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to upload profile image.']);
                    exit;
                }
            }
        
            $result = $backend->createUser($name, $email, $password, $role, $profileImagePath);
            if ($result === true) {
                http_response_code(200); 
                echo json_encode(['success' => true]);
            } else {
                http_response_code(401); 
                echo json_encode(['success' => false, 'message' => $result]);
            }
            break;
        
        case 'updateUser':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'];
        
            $profileImagePath = null; 
            if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profileImage']['tmp_name'];
                $fileName = $_FILES['profileImage']['name'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
                $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
        
                $uploadDir = 'uploads/profile_images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); 
                }
        
                $profileImagePath = $uploadDir . $newFileName;
        
                if (!move_uploaded_file($fileTmpPath, $profileImagePath)) {
                    http_response_code(500); 
                    echo json_encode(['success' => false, 'message' => 'Failed to upload profile image.']);
                    exit;
                }
            }
        
            $result = $backend->updateUser($id, $name, $email, $password, $role, $profileImagePath);
            if ($result === true) {
                http_response_code(200); 
                echo json_encode([
                    'success' => true,
                    'message' => 'User updated successfully!'
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => $result 
                ]);
            }
            break;
        
            case 'addFavorite':
                // Check if user is logged in
                if (!isset($_SESSION['user_id'])) {                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Please log in to add favorites']);
                    exit;
                }
                    
                $paperId = $_POST['paper_id'];
                $userId = $_SESSION['user_id']; // Get user_id from session
                
                $result = $backend->addFavorite($userId, $paperId);
                if ($result === true) {
                    http_response_code(200);                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $result]);
                }
                break;
    
            case 'removeFavorite':
                $userId = $_SESSION['user_id'];
                $paperId = $_POST['paper_id'];
                $result = $backend->removeFavorite($userId, $paperId);
                if ($result === true) {
                    http_response_code(200);
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(401); 
                    echo json_encode(['success' => false, 'message' => $result]); 
                }
                break;
    
            case 'getFavorites':
                try {
                    if (!isset($_SESSION['user_id'])) {
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
                        exit;
                    }
                    
                    $userId = $_SESSION['user_id'];
                    $result = $backend->getFavorites($userId);
                    
                    if ($result !== false) {
                        http_response_code(200);
                        echo json_encode([
                            'success' => true,
                            'data' => $result
                        ]);
                    } else {
                        http_response_code(200); // Still 200 because empty favorites is not an error
                        echo json_encode([
                            'success' => true,
                            'data' => []
                        ]);
                    }
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Server error occurred'
                    ]);
                }
                break;
            
        case 'createPaper':
            $title = $_POST['title'];
            $authors = $_POST['authors'];
            $keywords = $_POST['keywords'];
            $publication_info = $_POST['publication_info'];
            $pdf_link = $_POST['pdf_link'];
            $featured = $_POST['featured'];
        
            $result = $backend->createPaper($title, $authors, $keywords, $publication_info, $pdf_link, $featured);
            
            if ($result === true) {
                http_response_code(200); 
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400); 
                echo json_encode(['success' => false, 'message' => $result]); 
            }
            break;
        

        case 'getPaper':
            $paperId = $_POST['paper_id'];
            $result = $backend->getPaper($paperId);
            if ($result !== false && count($result) > 0) {
                http_response_code(200); 
                echo json_encode(['success' => true,'data' => $result]);
            } else {
                http_response_code(401); 
                echo json_encode(['success' => false, 'message' => $result]);
            }
            break;
            case 'getPapers':
                
                $result = $backend->getPapers();
                if ($result !== false && count($result) > 0) {
                    http_response_code(200); 
                    echo json_encode(['success' => true,'data' => $result]);
                } else {
                    http_response_code(401); 
                    echo json_encode(['success' => false, 'message' => $result]); 
                }
                break;

                case 'updatePaper':
                    $paperId = $_POST['paper_id']; 
                    $title = $_POST['title'];
                    $authors = $_POST['authors'];
                    $keywords = $_POST['keywords'];
                    $publication_info = $_POST['publication_info'];
                    $pdf_link = $_POST['pdf_link'];
                    $featured = $_POST['featured'];
                
                    $result = $backend->updatePaper($paperId, $title, $authors, $keywords, $publication_info, $pdf_link, $featured);
                
                    if ($result === true) {
                        http_response_code(200); 
                        echo json_encode(['success' => true, 'message' => 'Paper updated successfully']);
                    } else {
                        http_response_code(400); 
                        echo json_encode(['success' => false, 'message' => $result]); 
                    }
                    break;
                

        case 'deletePaper':
            $paperId = $_POST['paper_id'];
            $result = $backend->deletePaper($paperId);
            if ($result === true) {
                http_response_code(200); 
                echo json_encode(['success' => true]);
            } else {
                http_response_code(401); 
                echo json_encode(['success' => false, 'message' => $result]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
