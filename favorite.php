<?php
include_once('header.php');
?>
<style>
:root {
    --primary-color: #1a78c2;
    --primary-hover: #1565a7;
    --background-color: #f8fafc;
    --border-radius: 0.5rem;
    --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.papers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 1rem 0;
}

.paper-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

.paper-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.favorite-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: white;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--card-shadow);
    z-index: 1;
    cursor: pointer;
    transition: all 0.2s ease;
}

.favorite-btn:hover {
    transform: scale(1.1);
    background-color: #fef2f2;
}

.favorite-btn i {
    color: #ef4444;
    font-size: 1rem;
}

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

.card-text {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.card-footer {
    padding: 1rem 1.5rem;
    background: none;
    border-top: 1px solid #e5e7eb;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    padding: 0.75rem 1rem;
    font-weight: 500;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.btn-primary:hover {
    background: var(--primary-hover);
    color: white;
    text-decoration: none;
}

.btn-primary i {
    font-size: 1rem;
}

/* Loading, Empty and Error States */
.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}

.empty-state, .error-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state i, .error-state i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.7;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .papers-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
}

/* Animation for card removal */
@keyframes fadeOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(20px);
    }
}

.fade-out-right {
    animation: fadeOutRight 0.3s ease forwards;
}
</style>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary-color);">My Favorite Papers</h2>
    </div>
    <div class="papers-grid" id="allFavList">
        <!-- Favorites will be loaded dynamically -->
    </div>
</div>

<script src="js/favorite.js"></script>