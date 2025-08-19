<?php
// Handle POST requests for create/edit/delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submissions here if needed
    // For now, we'll let the controller handle this
}
?>

<style>
/* Table Styles */
.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.table-info {
    flex: 1;
}

.table-title {
    margin: 0 0 0.5rem 0;
    color: #495057;
    font-weight: 600;
    font-size: 1.1rem;
}

.table-description {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
    line-height: 1.4;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

/* Drag Handle */
.drag-handle {
    cursor: grab;
    color: #6c757d;
    text-align: center;
    padding: 0.5rem;
}

.drag-handle:hover {
    color: var(--primary-color);
}

.drag-handle:active {
    cursor: grabbing;
}

/* Slide Thumbnail */
.slide-thumbnail {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.slide-thumbnail-placeholder {
    width: 60px;
    height: 40px;
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Order Badge */
.order-badge {
    background: #e9ecef;
    color: #495057;
    font-weight: 500;
}

/* Drag and Drop Styles */
.slide-row {
    transition: background-color 0.2s ease;
}

.slide-row:hover {
    background-color: #f8f9fa;
}

.slide-row.dragging {
    opacity: 0.5;
    background-color: #e9ecef;
}

/* Statistics Grid Layout */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.stat-card:nth-child(1) .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card:nth-child(2) .stat-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-card:nth-child(3) .stat-icon {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.stat-card:nth-child(4) .stat-icon {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #343a40;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.stat-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    
    .table-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .table-actions {
        justify-content: flex-start;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.125rem;
    }
    
    .action-buttons .btn {
        padding: 0.125rem 0.25rem;
        font-size: 0.7rem;
    }
    
    /* Statistics Grid Responsive */
    .stats-cards {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .stats-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="header-info">
        <h1 class="page-title">
            <i class="fas fa-images me-2"></i>
            Slider Management
        </h1>
        <div class="stats-info">
            <span class="stat-item"><?php echo count($slides); ?> slides</span>
            <span class="stat-divider">•</span>
            <span class="stat-item"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'active'; })); ?> active</span>
        </div>
    </div>
    <div class="header-actions">
        <a href="../../public/index.php" target="_blank" class="btn btn-outline-secondary me-2">
            <i class="fas fa-eye me-1"></i>
            View Frontend
        </a>
        <a href="index.php?action=slider&method=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Add New Slide
        </a>
    </div>
</div>

<!-- Content Body -->
<div class="content-body">
    <!-- Statistics Grid -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo count($slides); ?></div>
                <div class="stat-label">Total Slides</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'active'; })); ?></div>
                <div class="stat-label">Active Slides</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo count(array_filter($slides, function($slide) { return $slide['status'] === 'inactive'; })); ?></div>
                <div class="stat-label">Inactive Slides</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo count(array_filter($slides, function($slide) { 
                    $slideDate = strtotime($slide['created_at']);
                    $currentDate = time();
                    $daysDiff = floor(($currentDate - $slideDate) / (60 * 60 * 24));
                    return $daysDiff <= 7; // Slides created in last 7 days
                })); ?></div>
                <div class="stat-label">Recent Slides (7 days)</div>
            </div>
        </div>
    </div>

    <?php if (empty($slides)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-images"></i>
            </div>
            <h3>No slides yet</h3>
            <p>Create your first slide to get started</p>
            <a href="index.php?action=slider&method=create" class="btn btn-primary">
                Add First Slide
            </a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <div class="table-header">
                <div class="table-info">
                    <h5 class="table-title">
                        <i class="fas fa-list me-2"></i>
                        Slides List
                    </h5>
                    <p class="table-description">
                        Manage your slider slides. Drag and drop to reorder, or use the action buttons to edit, toggle status, or delete slides.
                    </p>
                </div>
                <div class="table-actions">
                    <button class="btn btn-sm btn-outline-primary" id="saveOrderBtn" style="display: none;">
                        <i class="fas fa-save me-1"></i>Save Order
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="cancelOrderBtn" style="display: none;">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                </div>
            </div>
            
            <table class="table" id="slidesTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="100">Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th width="80">Order</th>
                        <th width="100">Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody id="slidesTableBody">
                    <?php foreach ($slides as $index => $slide): ?>
                        <tr data-slide-id="<?php echo $slide['_id']; ?>" class="slide-row">
                            <td>
                                <div class="drag-handle">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($slide['image'])): ?>
                                    <img src="../public/uploads/slider/<?php echo htmlspecialchars($slide['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($slide['title']); ?>" 
                                         class="slide-thumbnail">
                                <?php else: ?>
                                    <div class="slide-thumbnail-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($slide['title']); ?></strong>
                            </td>
                            <td>
                                <span class="text-muted"><?php echo htmlspecialchars(substr($slide['description'], 0, 50)); ?><?php echo strlen($slide['description']) > 50 ? '...' : ''; ?></span>
                            </td>
                            <td>
                                <span class="badge badge-light order-badge"><?php echo $slide['order']; ?></span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $slide['status']; ?>">
                                    <?php echo ucfirst($slide['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../../public/index.php" target="_blank" 
                                       class="btn btn-sm btn-outline-info" title="View Frontend">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?action=slider&method=edit&id=<?php echo $slide['_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=slider&method=toggle-status&id=<?php echo $slide['_id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary" title="Toggle Status">
                                        <i class="fas fa-toggle-<?php echo $slide['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                    </a>
                                    <a href="index.php?action=slider&method=delete&id=<?php echo $slide['_id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" title="Delete" 
                                       onclick="return confirm('Are you sure you want to delete this slide?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Slider Preview -->
        <div class="preview-section">
            <div class="preview-header">
                <h3 class="preview-title">
                    <i class="fas fa-eye me-2"></i>
                    Live Preview
                </h3>
                <div class="preview-controls">
                    <button class="btn btn-sm btn-outline-secondary" id="prevBtn" title="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="playBtn" title="Play/Pause">
                        <i class="fas fa-play"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="nextBtn" title="Next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            
            <div class="slider-preview">
                <div class="slider-container">
                    <?php foreach ($slides as $index => $slide): ?>
                        <div class="slide-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php if (!empty($slide['image'])): ?>
                                <img src="../public/uploads/slider/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                            <?php else: ?>
                                <div class="slide-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="slide-overlay">
                                <h3><?php echo htmlspecialchars($slide['title']); ?></h3>
                                <p><?php echo htmlspecialchars($slide['description']); ?></p>
                                <?php if (!empty($slide['button_text']) && !empty($slide['button_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($slide['button_url']); ?>" class="btn btn-primary" target="_blank">
                                        <?php echo htmlspecialchars($slide['button_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="slider-indicators">
                    <?php foreach ($slides as $index => $slide): ?>
                        <div class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Slider Preview JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide-item');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const playBtn = document.getElementById('playBtn');
    
    let currentSlide = 0;
    let isPlaying = false;
    let interval;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        currentSlide = index;
    }
    
    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    function prevSlide() {
        const prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }
    
    function togglePlay() {
        if (isPlaying) {
            clearInterval(interval);
            playBtn.innerHTML = '<i class="fas fa-play"></i>';
            isPlaying = false;
        } else {
            interval = setInterval(nextSlide, 3000);
            playBtn.innerHTML = '<i class="fas fa-pause"></i>';
            isPlaying = true;
        }
    }
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (playBtn) playBtn.addEventListener('click', togglePlay);
    
    // Indicator clicks
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showSlide(index));
    });
    
    // Auto-play on load
    if (slides.length > 1) {
        setTimeout(togglePlay, 2000);
    }
});

// Drag and Drop Reordering
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('slidesTableBody');
    const saveOrderBtn = document.getElementById('saveOrderBtn');
    const cancelOrderBtn = document.getElementById('cancelOrderBtn');
    
    if (!tableBody) return;
    
    let originalOrder = [];
    let isDragging = false;
    let draggedElement = null;
    
    // Initialize drag and drop
    function initDragAndDrop() {
        const rows = tableBody.querySelectorAll('.slide-row');
        
        rows.forEach(row => {
            const dragHandle = row.querySelector('.drag-handle');
            if (dragHandle) {
                dragHandle.addEventListener('mousedown', startDrag);
            }
        });
    }
    
    function startDrag(e) {
        e.preventDefault();
        isDragging = true;
        draggedElement = e.target.closest('tr');
        
        if (draggedElement) {
            draggedElement.style.opacity = '0.5';
            draggedElement.style.cursor = 'grabbing';
            
            // Store original order
            originalOrder = Array.from(tableBody.querySelectorAll('.slide-row')).map(row => 
                row.getAttribute('data-slide-id')
            );
            
            document.addEventListener('mousemove', onDrag);
            document.addEventListener('mouseup', stopDrag);
        }
    }
    
    function onDrag(e) {
        if (!isDragging || !draggedElement) return;
        
        const rows = Array.from(tableBody.querySelectorAll('.slide-row'));
        const draggedRect = draggedElement.getBoundingClientRect();
        const mouseY = e.clientY;
        
        let targetRow = null;
        
        for (let row of rows) {
            if (row === draggedElement) continue;
            
            const rect = row.getBoundingClientRect();
            const rowMiddle = rect.top + rect.height / 2;
            
            if (mouseY < rowMiddle) {
                targetRow = row;
                break;
            }
        }
        
        if (targetRow && targetRow !== draggedElement.nextSibling) {
            if (targetRow === draggedElement) return;
            
            if (draggedElement.nextSibling === targetRow) {
                tableBody.insertBefore(draggedElement, targetRow.nextSibling);
            } else {
                tableBody.insertBefore(draggedElement, targetRow);
            }
            
            updateOrderNumbers();
        }
    }
    
    function stopDrag() {
        if (draggedElement) {
            draggedElement.style.opacity = '';
            draggedElement.style.cursor = '';
            draggedElement = null;
        }
        
        isDragging = false;
        document.removeEventListener('mousemove', onDrag);
        document.removeEventListener('mouseup', stopDrag);
        
        // Show save/cancel buttons
        if (saveOrderBtn) saveOrderBtn.style.display = 'inline-block';
        if (cancelOrderBtn) cancelOrderBtn.style.display = 'inline-block';
    }
    
    function updateOrderNumbers() {
        const rows = tableBody.querySelectorAll('.slide-row');
        rows.forEach((row, index) => {
            const orderBadge = row.querySelector('.order-badge');
            if (orderBadge) {
                orderBadge.textContent = index + 1;
            }
        });
    }
    
    function saveOrder() {
        const rows = Array.from(tableBody.querySelectorAll('.slide-row'));
        const newOrder = rows.map(row => row.getAttribute('data-slide-id'));
        
        fetch('index.php?action=slider&method=reorder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order: newOrder })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide save/cancel buttons
                if (saveOrderBtn) saveOrderBtn.style.display = 'none';
                if (cancelOrderBtn) cancelOrderBtn.style.display = 'none';
                
                // Show success message
                alert('Slide order updated successfully!');
                location.reload();
            } else {
                alert('Failed to update slide order: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update slide order');
        });
    }
    
    function cancelOrder() {
        // Restore original order
        originalOrder.forEach(slideId => {
            const row = tableBody.querySelector(`[data-slide-id="${slideId}"]`);
            if (row) {
                tableBody.appendChild(row);
            }
        });
        
        updateOrderNumbers();
        
        // Hide save/cancel buttons
        if (saveOrderBtn) saveOrderBtn.style.display = 'none';
        if (cancelOrderBtn) cancelOrderBtn.style.display = 'none';
    }
    
    // Event listeners
    if (saveOrderBtn) saveOrderBtn.addEventListener('click', saveOrder);
    if (cancelOrderBtn) cancelOrderBtn.addEventListener('click', cancelOrder);
    
    // Initialize drag and drop
    initDragAndDrop();
});
</script> 