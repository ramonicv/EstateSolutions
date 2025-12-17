<?php

// Real Estate MLS - Main Listings Page
// Displays all house and business property listings


require_once 'config.php';
$conn = getDBConnection();

// Fetch house listings
$houseQuery = "
    SELECT l.mlsNumber, h.address, p.ownerName, p.price, 
           h.bedrooms, h.bathrooms, h.size, l.dateListed,
           a.name AS agentName, a.phone AS agentPhone, f.name AS firmName
    FROM Listings l
    INNER JOIN House h ON l.address = h.address
    INNER JOIN Property p ON l.address = p.address
    INNER JOIN Agent a ON l.agentId = a.agentId
    INNER JOIN Firm f ON a.firmId = f.id
    ORDER BY l.dateListed DESC
";
$houseResult = $conn->query($houseQuery);

// Fetch business property listings
$businessQuery = "
    SELECT l.mlsNumber, bp.address, p.ownerName, p.price, 
           bp.type, bp.size, l.dateListed,
           a.name AS agentName, a.phone AS agentPhone, f.name AS firmName
    FROM Listings l
    INNER JOIN BusinessProperty bp ON l.address = bp.address
    INNER JOIN Property p ON l.address = p.address
    INNER JOIN Agent a ON l.agentId = a.agentId
    INNER JOIN Firm f ON a.firmId = f.id
    ORDER BY l.dateListed DESC
";
$businessResult = $conn->query($businessQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate MLS - Property Listings</title>
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
            <li><a href="index.php" class="active">All Listings</a></li>
            <li><a href="search_houses.php">Search Houses</a></li>
            <li><a href="search_business.php">Search Business</a></li>
            <li><a href="agents.php">Agents</a></li>
            <li><a href="buyers.php">Buyers</a></li>
            <li><a href="custom_query.php">Custom Query</a></li>
        </ul>
    </nav>

    <main class="container">
        <!-- House Listings Section -->
        <section class="listings-section">
            <h2>House Listings</h2>
            <?php if ($houseResult && $houseResult->num_rows > 0): ?>
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
                            <?php while ($row = $houseResult->fetch_assoc()): ?>
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
                <p class="no-results">No house listings found.</p>
            <?php endif; ?>
        </section>

        <!-- Business Property Listings Section -->
        <section class="listings-section">
            <h2> Business Property Listings</h2>
            <?php if ($businessResult && $businessResult->num_rows > 0): ?>
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
                            <?php while ($row = $businessResult->fetch_assoc()): ?>
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
                <p class="no-results">No business property listings found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; <?php echo date('Y'); ?> Real Estate MLS System | Database Project</p>
    </footer>
</body>
</html>
<?php closeDBConnection($conn); ?>
