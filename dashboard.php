<?php
// Include necessary files
include_once('header.php');
include_once('db_connection.php');

// Fetch featured papers
try {
    $db = Database::getInstance()->connect();
    $query = "
        SELECT 
            papers.paper_id AS paper_id, 
            papers.title AS paper_title, 
            papers.pdf_link AS pdf_link, 
            users.name AS author_name,
            users.profile_image AS profile_image
        FROM papers
        INNER JOIN users ON users.user_id = papers.authors
        WHERE papers.featured = 1
    ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching featured papers: ' . $e->getMessage());
    $results = [];
}

// Fetch featured authors
try {
    $query = "
        SELECT 
            users.user_id, 
            users.profile_image,
            users.name AS author_name, 
            COUNT(papers.paper_id) AS papers_count
        FROM users
        LEFT JOIN papers ON papers.authors = users.user_id
        WHERE users.role_id = 2
        GROUP BY users.user_id
    ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching authors: ' . $e->getMessage());
    $authors = [];
}
?>

<!-- External Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/dashboard.js"></script>

<style>
    /* Global Variables and Theme Colors */
    :root {
        --primary-color: #1a78c2;
        --primary-hover: #1d4ed8;
        --secondary-color: #64748b;
        --accent-color: #f8fafc;
        --border-radius: 1rem;
        --transition: all 0.3s ease;
    }

    /* Hero Section Styles */
    .hero-section {
        background: linear-gradient(135deg, rgb(230, 230, 230) 0%, rgb(230, 230, 230) 100%);
        padding: 4rem 0;
        margin-bottom: 3rem;
        color: white;
        border-radius: 0 0 2rem 2rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .hero-section h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    /* Search Container Styles */
    .search-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        height: 3.5rem;
        border: 2px solid #e2e8f0;
        border-radius: var(--border-radius);
        padding: 0.75rem 1rem;
        transition: var(--transition);
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    /* Section Title Styles */
    .section-title {
        position: relative;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
    }

    /* Paper Card Styles */
    .paper-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
        transition: var(--transition);
        overflow: hidden;
    }

    .paper-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }

    .paper-card .card-img-top {
        background: var(--accent-color);
        padding: 2rem;
    }

    /* Favorite Button Styles */
    .favorite-btn {
        background: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
    }

    .favorite-btn:hover {
        transform: scale(1.1);
    }

    .favorite-btn i {
        color: #ef4444;
    }

    /* Primary Button Styles */
    .btn-primary {
        background: var(--primary-color);
        border: none;
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
    }

    /* Author Card Styles */
    .author-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
        transition: var(--transition);
        overflow: hidden;
    }

    .author-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }

    /* Author Profile Image Styles */
    .author-card .profile-image {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }

    .author-card .profile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Author Stats Badge */
    .author-stats {
        background: var(--accent-color);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        color: var(--secondary-color);
        display: inline-block;
    }

    /* Horizontal Scroll Section Styles */
    .scroll-section {
        position: relative;
        padding: 1rem 0;
    }

    .scroll-container {
        display: flex;
        overflow-x: auto;
        gap: 1.5rem;
        padding: 1rem 0.5rem;
        scroll-padding: 1rem;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
    }

    /* Hide Scrollbar */
    .scroll-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari and Opera */
    }

    /* Card Sizing for Horizontal Scroll */
    .scroll-item {
        flex: 0 0 auto;
        width: 350px; /* Fixed width for paper cards */
    }

    .author-scroll-item {
        flex: 0 0 auto;
        width: 280px; /* Smaller width for author cards */
    }

    /* Scroll Navigation Buttons */
    .scroll-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 2;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .scroll-button:hover {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .scroll-button.left {
        left: -20px;
    }

    .scroll-button.right {
        right: -20px;
    }

    /* Gradient Fade Effect for Scroll Edges */
    .scroll-section::before,
    .scroll-section::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 50px;
        z-index: 1;
        pointer-events: none;
    }

    .scroll-section::before {
        left: 0;
        background: linear-gradient(to right, rgba(248, 250, 252, 1), rgba(248, 250, 252, 0));
    }

    .scroll-section::after {
        right: 0;
        background: linear-gradient(to left, rgba(248, 250, 252, 1), rgba(248, 250, 252, 0));
    }

    /* Reset Card Margins */
    .paper-card, .author-card {
        margin-bottom: 0;
    }
</style>

<!-- Hero Section with Search -->
<div class="hero-section">
    <div class="container">
        <div class="text-center">
            <h1>Explore ISE Publications</h1>
            <p class="lead mb-0">Discover groundbreaking research and connect with leading authors</p>
        </div>
        <div class="search-container">
            <select id="searchDropdown" class="form-control select2">
                <option value="">Search Papers, Authors, or Keywords...</option>
            </select>
        </div>
    </div>
</div>

<div class="container">
    <!-- Featured Papers Section -->
    <section class="mb-5">
        <h2 class="section-title">Featured Papers</h2>
        <div class="scroll-section">
            <!-- Scroll Navigation Buttons -->
            <button class="scroll-button left" onclick="scrollSection('papers-scroll', 'left')">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="scroll-button right" onclick="scrollSection('papers-scroll', 'right')">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Papers Scroll Container -->
            <div class="scroll-container" id="papers-scroll">
                <?php
                if (!empty($results)) {
                    foreach ($results as $paper) {
                        // Check if paper is favorited by current user
                        $isFavorited = false;
                        if (isset($_SESSION['user_id'])) {
                            $checkFavQuery = "SELECT 1 FROM favorites WHERE user_id = ? AND paper_id = ?";
                            $checkFavStmt = $db->prepare($checkFavQuery);
                            $checkFavStmt->execute([$_SESSION['user_id'], $paper['paper_id']]);
                            $isFavorited = $checkFavStmt->rowCount() > 0;
                        }
                        ?>
                        <div class="scroll-item">
                            <div class="card paper-card h-100">
                                <button class="favorite-btn" data-paper-id="<?= htmlspecialchars($paper['paper_id']) ?>">
                                    <i class="fa-heart <?= $isFavorited ? 'fas' : 'far' ?> fs-5"></i>
                                </button>
                                <div class="card-body d-flex flex-column p-4">
                                    <h5 class="card-title mb-3"><?= htmlspecialchars($paper['paper_title']) ?></h5>
                                    <p class="card-text text-muted mb-4">By <?= htmlspecialchars($paper['author_name']) ?></p>
                                    <div class="mt-auto">
                                        <a href="<?= htmlspecialchars($paper['pdf_link']) ?>" 
                                           class="btn btn-primary w-100" 
                                           target="_blank">
                                            <i class="fas fa-file-pdf me-2"></i>View PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="scroll-item"><p class="text-center text-muted">No featured papers found.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Featured Authors Section -->
    <section class="mb-5">
        <h2 class="section-title">Featured Authors</h2>
        <div class="scroll-section">
            <!-- Scroll Navigation Buttons -->
            <button class="scroll-button left" onclick="scrollSection('authors-scroll', 'left')">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="scroll-button right" onclick="scrollSection('authors-scroll', 'right')">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Authors Scroll Container -->
            <div class="scroll-container" id="authors-scroll">
                <?php
                if (!empty($authors)) {
                    foreach ($authors as $author) {
                        ?>
                        <div class="author-scroll-item">
                            <div class="card author-card h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="profile-image mb-4">
                                        <img src="<?= $author['profile_image'] ? htmlspecialchars($author['profile_image']) : 'img/profile.jpg' ?>" 
                                             alt="<?= htmlspecialchars($author['author_name']) ?>">
                                    </div>
                                    <h5 class="card-title mb-3"><?= htmlspecialchars($author['author_name']) ?></h5>
                                    <p class="author-stats mb-4"><?= $author['papers_count'] ?> Papers</p>
                                    <a href="author_papers.php?author_id=<?= $author['user_id'] ?>" 
                                       class="btn btn-primary w-100">
                                        <i class="fas fa-book me-2"></i>View Papers
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="scroll-item"><p class="text-center text-muted">No authors found.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>
</div>

<!-- Scroll Navigation JavaScript -->
<script>
    /**
     * Handles horizontal scrolling for paper and author sections
     * @param {string} elementId - ID of the scroll container
     * @param {string} direction - 'left' or 'right'
     */
    function scrollSection(elementId, direction) {
        const container = document.getElementById(elementId);
        const scrollAmount = direction === 'left' ? -400 : 400;
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }

    // Add scroll button visibility handlers
    document.querySelectorAll('.scroll-container').forEach(container => {
        // Show/hide scroll buttons based on scroll position
        container.addEventListener('scroll', function() {
            const scrollButtons = this.parentElement.querySelectorAll('.scroll-button');
            const isAtStart = this.scrollLeft === 0;
            const isAtEnd = this.scrollLeft >= (this.scrollWidth - this.clientWidth - 1);
            
            // Update button opacity based on scroll position
            scrollButtons[0].style.opacity = isAtStart ? '0' : '1';
            scrollButtons[1].style.opacity = isAtEnd ? '0' : '1';
        });

        // Trigger initial scroll check
        container.dispatchEvent(new Event('scroll'));
    });
</script>