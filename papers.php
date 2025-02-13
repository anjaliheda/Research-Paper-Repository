<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_paper'])) {
    header('Content-Type: application/json');
    try {
        require_once 'db_connection.php';
        $db = Database::getInstance()->connect();
        
        $paperId = $_POST['paper_id'];
        
        // First delete from favorites if exists
        $deleteFavQuery = "DELETE FROM favorites WHERE paper_id = :paper_id";
        $deleteFavStmt = $db->prepare($deleteFavQuery);
        $deleteFavStmt->bindParam(':paper_id', $paperId);
        $deleteFavStmt->execute();
        
        // Then delete the paper
        $deleteQuery = "DELETE FROM papers WHERE paper_id = :paper_id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':paper_id', $paperId);
        
        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete paper']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}
// Include necessary files
include_once('header.php');
require_once 'db_connection.php';

// Fetch all available authors
try {
    $db = Database::getInstance()->connect();
    $userQuery = "SELECT user_id, name FROM users WHERE role_id = 2";  // Assuming role_id 2 is for authors
    $userStmt = $db->prepare($userQuery);
    $userStmt->execute();
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching users: ' . $e->getMessage());
    $users = [];
}

// Fetch all papers with author information
try {
    $paperQuery = "
        SELECT 
            papers.paper_id,
            papers.title,
            papers.keywords,
            papers.pdf_link,
            papers.publication_info,
            papers.featured,
            papers.authors AS author_id,
            users.name AS author_name
        FROM papers
        INNER JOIN users ON users.user_id = papers.authors
        ORDER BY papers.created_at DESC
    ";
    $paperStmt = $db->prepare($paperQuery);
    $paperStmt->execute();
    $papers = $paperStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching papers: ' . $e->getMessage());
    $papers = [];
}
?>

<!-- Include necessary scripts -->
<script src="js/paper.js"></script>

<style>
    /* Global Theme Variables */
:root {
    --primary-color: #1a78c2;
    --primary-hover: #1d4ed8;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --background-color: #f8fafc;
    --border-radius: 0.75rem;
    --transition: all 0.3s ease;
    --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    --hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
}

/* Base Styles */
body {
    background-color: var(--background-color);
}

/* Header */
.page-header {
    background: linear-gradient(135deg, rgb(223, 223, 223) 0%, rgb(223, 223, 223) 100%);
    padding: 2rem 0;
    margin-bottom: 2rem;
    color: #333;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    box-shadow: var(--card-shadow);
}

/* Buttons */
.btn-primary, .view-paper-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: var(--transition);
}

.btn-primary:hover, .view-paper-btn:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    color: white;
}

.view-paper-btn {
    width: 100%;
    display: block;
    text-align: center;
    text-decoration: none;
    margin-top: 1rem;
}

/* Action Buttons */
.btn-outline-primary, .btn-outline-danger {
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: var(--transition);
    flex-grow: 1;
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-outline-danger {
    color: var(--danger-color);
    border-color: var(--danger-color);
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: white;
}

.btn-outline-danger:hover {
    background-color: var(--danger-color);
    color: white;
}

.btn-outline-primary:hover, .btn-outline-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Paper Card */
.paper-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.paper-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

/* Form Elements */
.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    transition: var(--transition);
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}

/* Typography */
.paper-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.paper-meta {
    color: var(--secondary-color);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

/* Modal */
.modal-content {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
    background: linear-gradient(135deg, rgb(232, 232, 232) 0%, rgb(233, 233, 233) 100%);
    padding: 1.5rem;
    border-bottom: none;
}

/* Badge */
.badge-featured {
    background-color: var(--success-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 500;
    margin-top: 1rem;
    display: inline-block;
}
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Paper Management</h1>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addPaperModal">
                <i class="fas fa-plus me-2"></i>Add Paper
            </button>
        </div>
    </div>
</div>

<!-- Main Content Container -->
<div class="container">
    <div class="row">
        <?php if (!empty($papers)): ?>
            <?php foreach ($papers as $paper): ?>
                <div class="col-md-4 mb-4">
                    <div class="paper-card">
                        <!-- Paper Title -->
                        <h3 class="paper-title"><?= htmlspecialchars($paper['title']) ?></h3>
                        
                        <!-- Paper Metadata -->
                        <div class="paper-meta mb-3">
                            <p class="mb-2">
                                <i class="fas fa-user me-2"></i>
                                <a href="author_papers.php?author_id=<?= htmlspecialchars($paper['author_id']) ?>" 
                                   class="text-decoration-none">
                                    <?= htmlspecialchars($paper['author_name']) ?>
                                </a>
                            </p>
                            <?php if (!empty($paper['keywords'])): ?>
                                <p class="mb-2"><i class="fas fa-tags me-2"></i><?= htmlspecialchars($paper['keywords']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($paper['publication_info'])): ?>
                                <p class="mb-2"><i class="fas fa-info-circle me-2"></i><?= htmlspecialchars($paper['publication_info']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mb-3">
                            <button class="btn btn-outline-primary flex-grow-1" 
                                    onclick="editPaper(<?= $paper['paper_id'] ?>)"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewProfileModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger flex-grow-1" 
                                    onclick="deletePaper(<?= $paper['paper_id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <!-- View Paper Button -->
                        <a href="<?= htmlspecialchars($paper['pdf_link']) ?>" 
                           class="view-paper-btn" 
                           target="_blank">
                           <i class="fas fa-file-pdf me-2"></i>View Paper
                        </a>
                        
                        <!-- Featured Badge -->
                        <?php if ($paper['featured']): ?>
                            <span class="badge badge-featured mt-2">
                                <i class="fas fa-star me-1"></i>Featured
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No papers found.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Add Paper Modal -->
<div class="modal fade" id="addPaperModal" tabindex="-1" aria-labelledby="addPaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaperModalLabel">Add Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPaperForm" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter paper title" required>
                    </div>
                    <div class="mb-4">
                        <label for="authors" class="form-label">Authors</label>
                        <select class="form-select" id="authors" name="authors" required>
                            <option value="">Select Author</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                                    <?= htmlspecialchars($user['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="keywords" class="form-label">Keywords</label>
                        <input type="text" class="form-control" id="keywords" name="keywords" placeholder="Enter keywords (comma-separated)" required>
                    </div>
                    <div class="mb-4">
                        <label for="publication_info" class="form-label">Publication Information</label>
                        <input type="text" class="form-control" id="publication_info" name="publication_info" placeholder="Enter publication details" required>
                    </div>
                    <div class="mb-4">
                        <label for="pdf_link" class="form-label">PDF Link</label>
                        <input type="url" class="form-control" id="pdf_link" name="pdf_link" placeholder="Enter URL to PDF document" required>
                    </div>
                    <div class="mb-4">
                        <label for="featured" class="form-label">Featured Status</label>
                        <select class="form-select" id="featured" name="featured" required>
                            <option value="">Select featured status</option>
                            <option value="1">Featured</option>
                            <option value="0">Not Featured</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addPaperForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Paper
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View/Edit Paper Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" aria-labelledby="viewProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProfileModalLabel">Edit Paper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPaperModal" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="viewPaperTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="viewPaperTitle" name="title" required>
                    </div>
                    <div class="mb-4">
                        <label for="viewPaperAuthors" class="form-label">Authors</label>
                        <select class="form-select" id="viewPaperAuthors" name="authors" required>
                            <option value="">Select Author</option>
                            <?php if (!empty($users)) : ?>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?= htmlspecialchars($user['user_id']) ?>"><?= htmlspecialchars($user['name']) ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">No authors found</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="viewPaperKeywords" class="form-label">Keywords</label>
                        <input type="text" class="form-control" id="viewPaperKeywords" name="keywords" required>
                    </div>
                    <div class="mb-4">
                        <label for="viewPaperPublicationInfo" class="form-label">Publication Information</label>
                        <input type="text" class="form-control" id="viewPaperPublicationInfo" name="publication_info" required>
                    </div>
                    <div class="mb-4">
                        <label for="viewPaperPdfLink" class="form-label">PDF Link</label>
                        <input type="url" class="form-control" id="viewPaperPdfLink" name="pdf_link" required>
                        <input type="hidden" id="viewPaperId">
                    </div>
                    <div class="mb-4">
                        <label for="viewPaperFeatured" class="form-label">Featured Status</label>
                        <select class="form-select" id="viewPaperFeatured" name="featured" required>
                            <option value="">Select featured status</option>
                            <option value="1">Featured</option>
                            <option value="0">Not Featured</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editPaperModal" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Paper
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Initialize Enhanced Form Elements -->
<script>
// Function to delete paper with confirmation
function deletePaper(paperId) {
    if (confirm('Are you sure you want to delete this paper?')) {
        const formData = new FormData();
        formData.append('paper_id', paperId);
        formData.append('delete_paper', '1');

        fetch(window.location.href, {  // Use current URL
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const paperCard = document.querySelector(`button[onclick="deletePaper(${paperId})"]`)
                    .closest('.col-md-4');
                if (paperCard) {
                    paperCard.remove();
                    // Optional: Show success message
                    alert('Paper deleted successfully');
                    
                    // Check if there are any papers left
                    const remainingPapers = document.querySelectorAll('.paper-card');
                    if (remainingPapers.length === 0) {
                        document.querySelector('.row').innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>No papers found.
                                </div>
                            </div>
                        `;
                    }
                }
            } else {
                throw new Error(data.message || 'Failed to delete paper');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting paper: ' + error.message);
        });
    }
}
</script>
