<?php
session_start();
include 'db.php';

// Cart Count கணக்கிட
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item)) {
            $cart_count += $item['quantity'] ?? 1;
        } else {
            $cart_count += 1;
        }
    }
}

// Search & Filter Query
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM medicines WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
if (!empty($category)) {
    $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
}
$result = $conn->query($sql);

// Categories Array with Images
$categories_list = [
    ['name' => 'Injection', 'img' => 'https://cdn-icons-png.flaticon.com/512/3209/3209028.png'],
    ['name' => 'OTC', 'img' => 'https://cdn-icons-png.flaticon.com/512/2874/2874808.png'],
    ['name' => 'Tablet', 'img' => 'https://cdn-icons-png.flaticon.com/512/883/883407.png'],
    ['name' => 'Syrup', 'img' => 'https://cdn-icons-png.flaticon.com/512/3004/3004458.png'],
    ['name' => 'Capsules', 'img' => 'https://cdn-icons-png.flaticon.com/512/2965/2965567.png'],
    ['name' => 'Drops', 'img' => 'https://cdn-icons-png.flaticon.com/512/2864/2864380.png'],
    ['name' => 'Ointment', 'img' => 'https://cdn-icons-png.flaticon.com/512/2312/2312293.png']
];
?>

<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Store - Online Medicine</title>
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 60px;
        }
        .amazon-header {
            background-color: #131921;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1050;
            width: 100%;
        }
        .search-btn {
            background-color: #febd69;
            border: none;
            color: #111;
        }
        .search-btn:hover {
            background-color: #f3a847;
        }
        .hero-banner {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .category-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 15px 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: block;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.15);
            color: #0d6efd;
        }
        .category-card.active-cat {
            border: 2px solid #0d6efd;
            background-color: #e3f2fd;
        }
        .category-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-bottom: 8px;
        }
        .product-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12) !important;
        }
        .product-img {
            height: 180px;
            object-fit: contain;
            padding: 15px;
            background-color: #fafafa;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .badge-category {
            background-color: #e3f2fd;
            color: #0d6efd;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .price-text {
            color: #B12704;
            font-size: 1.25rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Header / Sticky Navbar -->
    <header class="amazon-header py-2 px-3 shadow">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Brand Logo -->
            <a href="index.php" class="text-white text-decoration-none fw-bold fs-4 d-flex align-items-center gap-2">
                <i class="fa-solid fa-capsules text-warning"></i> QuickPharmacy
            </a>

            <!-- Search Bar -->
            <form method="GET" action="index.php" class="d-flex flex-grow-1 max-width-600 my-1 mx-lg-4 position-relative" style="max-width: 600px;">
                <select name="category" class="form-select w-auto bg-light border-0 rounded-start text-secondary" style="border-radius: 0;">
                    <option value="">All Categories</option>
                    <?php foreach($categories_list as $cat): ?>
                        <option value="<?php echo $cat['name']; ?>" <?php if($category==$cat['name']) echo 'selected'; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="live_search" name="search" class="form-control border-0 rounded-0" placeholder="Search medicines, healthcare products..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
                <button type="submit" class="btn search-btn rounded-end px-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <!-- Right Menu Items -->
            <div class="d-flex align-items-center gap-3">
                <a href="index.php" class="text-white text-decoration-none small fw-bold">
                    <i class="fa-solid fa-house text-warning"></i> Home
                </a>
                <a href="about.php" class="text-white text-decoration-none small">
                    <i class="fa-solid fa-circle-info text-warning"></i> About Us
                </a>
                <a href="services.php" class="text-white text-decoration-none small">
                    <i class="fa-solid fa-hand-holding-medical text-warning"></i> Services
                </a>
                <a href="faq.php" class="text-white text-decoration-none small">
                    <i class="fa-solid fa-circle-question text-warning"></i> FAQ
                </a>
                <a href="track_order.php" class="text-white text-decoration-none small">
                    <i class="fa-solid fa-truck-fast text-warning"></i> Track Order
                </a>
                <a href="cart.php" class="btn btn-outline-light position-relative border-0 fw-bold">
                    <i class="fa-solid fa-cart-shopping fs-5 text-warning"></i> Cart
                    <?php if($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="admin_login.php" class="btn btn-warning btn-sm fw-bold">Admin Login</a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container my-4">

        <!-- Hero Banner -->
        <div class="hero-banner shadow text-center text-md-start d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-2">Essential Medicines Delivered Fast 🚚</h2>
                <p class="mb-0 fs-5 text-white-50">Get your health care products at best discount prices.</p>
            </div>
            <a href="#products" class="btn btn-light text-primary fw-bold px-4 py-2 shadow-sm">Shop Now</a>
        </div>

        <!-- Categories Section -->
        <div class="mb-5">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-layer-group text-primary me-2"></i>Shop By Category</h5>
            <div class="row row-cols-2 row-cols-xs-3 row-cols-sm-3 row-cols-md-4 row-cols-lg-7 g-3">
                <?php foreach($categories_list as $cat): 
                    $isActive = ($category == $cat['name']) ? 'active-cat' : '';
                ?>
                    <div class="col">
                        <a href="index.php?category=<?php echo urlencode($cat['name']); ?>#products" class="category-card <?php echo $isActive; ?>">
                            <img src="<?php echo $cat['img']; ?>" alt="<?php echo $cat['name']; ?>" class="category-img d-block mx-auto">
                            <span class="fw-bold small"><?php echo $cat['name']; ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section Title -->
        <div class="d-flex justify-content-between align-items-center mb-4" id="products">
            <h4 class="fw-bold text-dark mb-0">
                <?php echo !empty($category) ? htmlspecialchars($category) . ' Medicines' : 'Featured Healthcare Products'; ?>
            </h4>
            <?php if(!empty($search) || !empty($category)): ?>
                <a href="index.php" class="btn btn-sm btn-outline-danger fw-bold"><i class="fa-solid fa-xmark"></i> Clear Filters</a>
            <?php endif; ?>
        </div>

        <!-- Medicines Product Grid -->
        <div class="row g-4">
            <?php 
            if ($result && $result->num_rows > 0): 
                while($row = $result->fetch_assoc()):
                    $img_name = $row['image'] ?? '';
                    if (!empty($img_name)) {
                        if (filter_var($img_name, FILTER_VALIDATE_URL)) {
                            $img = $img_name; 
                        } elseif (file_exists('uploads/' . $img_name)) {
                            $img = 'uploads/' . $img_name; 
                        } else {
                            $img = 'https://via.placeholder.com/200x180/e3f2fd/0d6efd?text=Medicine';
                        }
                    } else {
                        $img = 'https://via.placeholder.com/200x180/e3f2fd/0d6efd?text=Medicine';
                    }
                    
                    $stock_count = (int)($row['stock'] ?? 0);
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 product-card shadow-sm">
                        <img src="<?php echo $img; ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        
                        <div class="card-body d-flex flex-column p-3">
                            <div>
                                <span class="badge badge-category mb-2 px-2 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($row['category'] ?? 'General'); ?>
                                </span>
                            </div>

                            <h6 class="card-title fw-bold text-dark mb-1 text-truncate" title="<?php echo htmlspecialchars($row['name']); ?>">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </h6>

                            <div class="mt-auto pt-2">
                                <div class="price-text mb-1">₹<?php echo number_format($row['price'], 2); ?></div>
                                <small class="text-muted d-block mb-3">
                                    In Stock: 
                                    <?php if($stock_count > 0): ?>
                                        <strong class="text-success"><?php echo $stock_count; ?></strong>
                                    <?php else: ?>
                                        <strong class="text-danger">Out of Stock</strong>
                                    <?php endif; ?>
                                </small>

                                <?php if($stock_count > 0): ?>
                                    <!-- Add to Cart Form -->
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="medicine_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                                        
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <small class="fw-bold text-secondary">Qty:</small>
                                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $stock_count; ?>" class="form-control form-control-sm text-center fw-bold" style="width: 75px;">
                                        </div>

                                        <button type="submit" name="add_to_cart" class="btn btn-warning w-100 fw-bold rounded-3 py-2">
                                            <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                                        </button>
                                    </form>

                                    <div class="text-center mt-2">
                                        <a href="#" class="small text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#notifyModal<?php echo $row['id']; ?>">
                                            <i class="fa-solid fa-bell text-warning"></i> Need More Stock?
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <!-- Stock இல்லாதபோது Notify Me Button -->
                                    <button type="button" class="btn btn-outline-primary w-100 fw-bold rounded-3 py-2" data-bs-toggle="modal" data-bs-target="#notifyModal<?php echo $row['id']; ?>">
                                        <i class="fa-solid fa-bell me-1"></i> Notify Me
                                    </button>
                                <?php endif; ?>

                                <!-- Notify Me Modal Popup -->
                                <div class="modal fade" id="notifyModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h6 class="modal-title fw-bold"><i class="fa-solid fa-bell me-2"></i>Stock Alert Request</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="save_notification.php" method="POST">
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-3">
                                                        <strong><?php echo htmlspecialchars($row['name']); ?></strong> - கூடுதல் Stock வந்தவுடன் உங்களுக்கு SMS பெற உங்கள் விவரங்களை பதிவு செய்யவும்.
                                                    </p>
                                                    <input type="hidden" name="medicine_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="medicine_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                                    
                                                    <div class="mb-3 text-start">
                                                        <label class="form-label small fw-bold">Your Name</label>
                                                        <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Enter your name" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required autocomplete="off">
                                                    </div>
                                                    <div class="mb-3 text-start">
                                                        <label class="form-label small fw-bold">Phone Number</label>
                                                        <input type="tel" name="phone" class="form-control form-control-sm" placeholder="10-digit phone number" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required autocomplete="off">
                                                    </div>
                                                    <div class="mb-2 text-start">
                                                        <label class="form-label small fw-bold">Required Quantity</label>
                                                        <input type="number" name="requested_qty" class="form-control form-control-sm" value="10" min="1" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="notify_me" class="btn btn-primary btn-sm fw-bold">Submit Request</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile; 
            else: 
            ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-capsules fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No medicines found in this category!</h5>
                    <p class="text-secondary small">Try selecting another category or clear filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Script Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>