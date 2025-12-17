<?php

// Real Estate MLS - Search Houses
// Search houses based on price range, bedrooms, and bathrooms


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
    $bedrooms = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' 
        ? intval($_GET['bedrooms']) : null;
    $bathrooms = isset($_GET['bathrooms']) && $_GET['bathrooms'] !== '' 
        ? intval($_GET['bathrooms']) : null;
    
    // Build query
    $query = "
        SELECT l.mlsNumber, h.address, p.ownerName, p.price, 
               h.bedrooms, h.bathrooms, h.size, l.dateListed,
               a.name AS agentName, a.phone AS agentPhone, f.name AS firmName
        FROM Listings l
        INNER JOIN House h ON l.address = h.address
        INNER JOIN Property p ON l.address = p.address
        INNER JOIN Agent a ON l.agentId = a.agentId
        INNER JOIN Firm f ON a.firmId = f.id
        WHERE p.price >= ? AND p.price <= ?
    ";
    
    $params = [$minPrice, $maxPrice];
    $types = "ii";
    
    if ($bedrooms !== null) {
        $query .= " AND h.bedrooms >= ?";
        $params[] = $bedrooms;
        $types .= "i";
    }
    
    if ($bathrooms !== null) {
        $query .= " AND h.bathrooms >= ?";
        $params[] = $bathrooms;
        $types .= "i";
    }
    
    $query .= " ORDER BY p.price DESC";
    
    // Execute prepared statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Houses - Real Estate MLS</title>
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
            <li><a href="search_houses.php" class="active">Search Houses</a></li>
            <li><a href="search_business.php">Search Business</a></li>
            <li><a href="agents.php">Agents</a></li>
            <li><a href="buyers.php">Buyers</a></li>
            <li><a href="custom_query.php">Custom Query</a></li>
        </ul>
    </nav>

    <main class="container">
        <section class="search-section">
            <h2> Search Houses</h2>
            
            <form method="GET" action="" class="search-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="min_price">Minimum Price ($)</label>
                        <input type="number" id="min_price" name="min_price" 
                               placeholder="e.g., 100000"
                               value="<?php echo isset($_GET['min_price']) ? ($_GET['min_price']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="max_price">Maximum Price ($)</label>
                        <input type="number" id="max_price" name="max_price" 
                               placeholder="e.g., 500000"
                               value="<?php echo isset($_GET['max_price']) ? ($_GET['max_price']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="bedrooms">Minimum Bedrooms</label>
                        <select id="bedrooms" name="bedrooms">
                            <option value="">Any</option>
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?php echo $i; ?>" 
                                    <?php echo (isset($_GET['bedrooms']) && $_GET['bedrooms'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>+
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Minimum Bathrooms</label>
                        <select id="bathrooms" name="bathrooms">
                            <option value="">Any</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>"
                                    <?php echo (isset($_GET['bathrooms']) && $_GET['bathrooms'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>+
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="search" value="1" class="btn btn-primary">Search Houses</button>
                    <a href="search_houses.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </section>

        <?php if ($searchPerformed): ?>
            <section class="results-section">
                <h2>Search Results</h2>
                <?php if ($results && $results->num_rows > 0): ?>
                    <p class="result-count"><?php echo $results->num_rows; ?> house(s) found</p>
                    <div class="table-responsive">
                        <table class="listings-table">
                            <thead>
                                <tr>
                                    <th>MLS #</th>
                                    <th>Address</th>
                                    <th>Price</th>
                                    <th>Bed</th>
                                    <th>Bath</th>
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
                                        <td class="centered"><?php echo ($row['bedrooms']); ?></td>
                                        <td class="centered"><?php echo ($row['bathrooms']); ?></td>
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
                    <p class="no-results">No houses found matching your criteria.</p>
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
