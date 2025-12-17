<?php

// Real Estate MLS - Search Business Properties
// Search business properties based on price range and size


require_once 'config.php';
$conn = getDBConnection();

$results = null;
$searchPerformed = false;

// Process search form
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchPerformed = true;
    
    // Get and sanitize inputs
    $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' 
        ? intval($_GET['min_price']) : 0;
    $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' 
        ? intval($_GET['max_price']) : 999999999;
    $minSize = isset($_GET['min_size']) && $_GET['min_size'] !== '' 
        ? intval($_GET['min_size']) : 0;
    $maxSize = isset($_GET['max_size']) && $_GET['max_size'] !== '' 
        ? intval($_GET['max_size']) : 999999999;
    $propertyType = isset($_GET['property_type']) && $_GET['property_type'] !== '' 
        ? sanitizeInput($conn, $_GET['property_type']) : null;
    
    // Build query
    $query = "
        SELECT l.mlsNumber, bp.address, p.ownerName, p.price, 
               bp.type, bp.size, l.dateListed,
               a.name AS agentName, a.phone AS agentPhone, f.name AS firmName
        FROM Listings l
        INNER JOIN BusinessProperty bp ON l.address = bp.address
        INNER JOIN Property p ON l.address = p.address
        INNER JOIN Agent a ON l.agentId = a.agentId
        INNER JOIN Firm f ON a.firmId = f.id
        WHERE p.price >= ? AND p.price <= ?
          AND bp.size >= ? AND bp.size <= ?
    ";
    
    $params = [$minPrice, $maxPrice, $minSize, $maxSize];
    $types = "iiii";
    
    if ($propertyType !== null) {
        $query .= " AND TRIM(bp.type) = ?";
        $params[] = $propertyType;
        $types .= "s";
    }
    
    $query .= " ORDER BY p.price DESC";
    
    // Execute
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $results = $stmt->get_result();
}

// Get available property types for dropdown
$typesQuery = "SELECT DISTINCT TRIM(type) AS type FROM BusinessProperty ORDER BY type";
$typesResult = $conn->query($typesQuery);
$propertyTypes = [];
while ($row = $typesResult->fetch_assoc()) {
    $propertyTypes[] = $row['type'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Business Properties - Real Estate MLS</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <h1> Real Estate MLS</h1>
        </div>
    </header>

    <nav class="main-nav">
        <ul>
            <li><a href="index.php">All Listings</a></li>
            <li><a href="search_houses.php">Search Houses</a></li>
            <li><a href="search_business.php" class="active">Search Business</a></li>
            <li><a href="agents.php">Agents</a></li>
            <li><a href="buyers.php">Buyers</a></li>
            <li><a href="custom_query.php">Custom Query</a></li>
        </ul>
    </nav>

    <main class="container">
        <section class="search-section">
            <h2> Search Business Properties</h2>
            
            <form method="GET" action="" class="search-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="min_price">Minimum Price ($)</label>
                        <input type="number" id="min_price" name="min_price" 
                               placeholder="e.g., 500000"
                               value="<?php echo isset($_GET['min_price']) ? ($_GET['min_price']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="max_price">Maximum Price ($)</label>
                        <input type="number" id="max_price" name="max_price" 
                               placeholder="e.g., 2000000"
                               value="<?php echo isset($_GET['max_price']) ? ($_GET['max_price']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="min_size">Minimum Size (sq ft)</label>
                        <input type="number" id="min_size" name="min_size" 
                               placeholder="e.g., 1000"
                               value="<?php echo isset($_GET['min_size']) ? ($_GET['min_size']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="max_size">Maximum Size (sq ft)</label>
                        <input type="number" id="max_size" name="max_size" 
                               placeholder="e.g., 50000"
                               value="<?php echo isset($_GET['max_size']) ? ($_GET['max_size']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="property_type">Property Type</label>
                        <select id="property_type" name="property_type">
                            <option value="">Any Type</option>
                            <?php foreach ($propertyTypes as $type): ?>
                                <option value="<?php echo ($type); ?>"
                                    <?php echo (isset($_GET['property_type']) && $_GET['property_type'] === $type) ? 'selected' : ''; ?>>
                                    <?php echo (ucwords($type)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="search" value="1" class="btn btn-primary">Search Properties</button>
                    <a href="search_business.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </section>

        <?php if ($searchPerformed): ?>
            <section class="results-section">
                <h2>Search Results</h2>
                <?php if ($results && $results->num_rows > 0): ?>
                    <p class="result-count"><?php echo $results->num_rows; ?> property(ies) found</p>
                    <div class="table-responsive">
                        <table class="listings-table">
                            <thead>
                                <tr>
                                    <th>MLS #</th>
                                    <th>Address</th>
                                    <th>Price</th>
                                    <th>Type</th>
                                    <th>Sq Ft</th>
                                    <th>Listed</th>
                                    <th>Agent</th>
                                    <th>Firm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $results->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="mls-number"><?php echo ($row['mlsNumber']); ?></span></td>
                                        <td><?php echo ($row['address']); ?></td>
                                        <td class="price"><?php echo formatPrice($row['price']); ?></td>
                                        <td><span class="property-type"><?php echo (trim($row['type'])); ?></span></td>
                                        <td class="centered"><?php echo number_format($row['size']); ?></td>
                                        <td><?php echo ($row['dateListed']); ?></td>
                                        <td>
                                            <div class="agent-info">
                                                <?php echo ($row['agentName']); ?>
                                                <small><?php echo ($row['agentPhone']); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo ($row['firmName']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-results">No business properties found matching your criteria.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="main-footer">
        <p>&copy; <?php echo date('Y'); ?> Real Estate MLS System | Database Project</p>
    </footer>
</body>
</html>
<?php closeDBConnection($conn); ?>
