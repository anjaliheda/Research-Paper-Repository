<?php
// Include necessary files for database connection and header
include_once('header.php');
require_once 'db_connection.php';
?>

<!-- External CSS and JavaScript Dependencies -->
<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<script src="js/dashboard.js"></script>

<!-- Custom Styling -->
<style>
    /* Global Variables for consistent theming */
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --secondary-color: #64748b;
        --background-color: #f8fafc;
        --border-radius: 1rem;
        --transition: all 0.3s ease;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    }

    /* Base body styling */
    body {
        background-color: var(--background-color);
    }

    /* Author Header Section Styling */
    .author-header {
        background: #1a78c2;
        color: white;
        border-radius: var(--border-radius);
        padding: 4rem 2rem; /* Increased padding for better spacing */
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
    }

    /* Decorative pattern overlay for author header */
    .author-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect fill="white" fill-opacity="0.1" width="100" height="100"/></svg>') repeat;
        opacity: 0.1;
    }

    /* Author Profile Image Container - Increased size */
    .profile-image-container {
        position: relative;
        width: 160px; /* Increased from 160px */
        height: 240px; /* Increased from 160px */
        margin: 0 auto 2rem; /* Increased bottom margin */
    }

    /* Author Profile Image Styling */
    .profile-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 6px solid white; /* Increased border width */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
        background-color: white;
    }

    /* Author Name Typography */
    .author-name {
        font-size: 2.5rem; /* Increased size */
        font-weight: 700;
        margin-bottom: 1rem; /* Increased spacing */
        color: white;
    }

    /* Papers Count Display */
    .papers-count {
        font-size: 1.2rem; /* Slightly increased */
        opacity: 0.9;
    }

    /* Papers Grid Layout */
    .papers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        padding: 1rem 0;
    }

    /* Individual Paper Card Styling */
    .paper-card {
        background: white;
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
        position: relative;
    }

    /* Paper Card Hover Effects */
    .paper-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }

    /* Favorite Button Styling */
    .favorite-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
        z-index: 1;
        cursor: pointer; /* Added for better UX */
    }

    .favorite-btn:hover {
        transform: scale(1.1);
    }

    .favorite-btn i {
        color: #ef4444;
        font-size: 1.2rem;
    }

    /* Paper Card Image Section */
    .card-image {
        background-color: var(--background-color);
        padding: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card-image img {
        max-height: 120px;
        width: auto;
        transition: var(--transition);
    }

    /* Card Content Styling */
    .card-body {
        padding: 1.5rem;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    /* Meta Information Styling */
    .meta-info {
        display: flex;
        align-items: center;
        color: var(--secondary-color);
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .meta-info i {
        margin-right: 0.5rem;
        width: 16px;
    }

    /* Card Footer Styling */
    .card-footer {
        padding: 1.5rem;
        background: none;
        border-top: 1px solid #e5e7eb;
    }

    /* Primary Button Styling */
    .btn-primary {
        background: #1a78c2;
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: var(--transition);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary:hover {
        background: #1a78c2;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    /* Alert Message Styling */
    .alert {
        border-radius: var(--border-radius);
        padding: 1.5rem;
        text-align: center;
        background-color: white;
        border: none;
        box-shadow: var(--card-shadow);
    }
</style>

<?php
// Verify author ID is provided
if (isset($_GET['author_id'])) {
    $authorId = $_GET['author_id'];
} else {
    echo "Author ID not provided.";
    exit;
}

try {
    // Initialize database connection
    $db = Database::getInstance()->connect();
    
    // Fetch author details
    $authorQuery = "SELECT name, profile_image FROM users WHERE user_id = :author_id";
    $authorStmt = $db->prepare($authorQuery);
    $authorStmt->bindParam(':author_id', $authorId);
    $authorStmt->execute();
    $author = $authorStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch all papers by the author
    $query = "
        SELECT 
            papers.paper_id AS paper_id, 
            papers.title AS paper_title, 
            papers.pdf_link AS pdf_link,
            papers.keywords AS keywords,
            papers.publication_info AS publication_info,
            users.name AS author_name
        FROM papers
        INNER JOIN users ON users.user_id = papers.authors
        WHERE papers.authors = :author_id
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':author_id', $authorId);
    $stmt->execute();

    $papers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching papers: ' . $e->getMessage());
    $papers = [];
}
?>

<!-- Main Content Container -->
<div class="container my-4">
    <!-- Author Profile Header -->
    <div class="author-header text-center">
        <div class="profile-image-container">
            <img src="<?= $author['profile_image'] ? htmlspecialchars($author['profile_image']) : 'img/profile.jpg' ?>" 
                 class="profile-image" 
                 alt="<?= htmlspecialchars($author['name']) ?>">
        </div>
        <h1 class="author-name"><?= htmlspecialchars($author['name']) ?></h1>
        <div class="papers-count">
            <i class="fas fa-book-open me-2"></i>
            <?= count($papers) ?> Published Papers
        </div>
    </div>

    <!-- Papers Display Section -->
    <?php if (empty($papers)): ?>
        <!-- No Papers Found Message -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No papers found for this author.
        </div>
    <?php else: ?>
        <!-- Papers Grid Display -->
        <div class="papers-grid">
            <?php foreach ($papers as $paper): ?>
                <div class="paper-card">
                    <!-- Favorite Button -->
                    <button class="favorite-btn" 
                            data-paper-id="<?= htmlspecialchars($paper['paper_id']) ?>" 
                            data-user-id="<?= htmlspecialchars($_SESSION['user_id']) ?>">
                        <i class="far fa-heart"></i>
                    </button>
                    
                    <!-- Paper Information -->
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($paper['paper_title']) ?></h5>
                        <!-- Keywords -->
                        <div class="meta-info">
                            <i class="fas fa-tags"></i>
                            <span><?= htmlspecialchars($paper['keywords']) ?></span>
                        </div>
                        <!-- Publication Info -->
                        <div class="meta-info">
                            <i class="fas fa-info-circle"></i>
                            <span><?= htmlspecialchars($paper['publication_info']) ?></span>
                        </div>
                    </div>
                    
                    <!-- PDF Link -->
                    <div class="card-footer">
                        <a href="<?= htmlspecialchars($paper['pdf_link']) ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i>View PDF
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for favorite functionality -->
<script src="js/paper.js"></script>