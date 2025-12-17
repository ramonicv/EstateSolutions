<?php

// Real Estate MLS - Buyers Page
// Displays all buyers and their associated information including preferences


require_once 'config.php';
$conn = getDBConnection();

// Fetch all buyers with their working agents
$buyerQuery = "
    SELECT b.id, b.name, b.phone, b.propertyType, 
           b.bedrooms, b.bathrooms, b.businessPropertyType,
           b.minimumPreferredPrice, b.maximumPreferredPrice,
           GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ') AS workingAgents
    FROM Buyer b
    LEFT JOIN Works_With ww ON b.id = ww.buyerId
    LEFT JOIN Agent a ON ww.agentId = a.agentId
    GROUP BY b.id, b.name, b.phone, b.propertyType, 
             b.bedrooms, b.bathrooms, b.businessPropertyType,
             b.minimumPreferredPrice, b.maximumPreferredPrice
    ORDER BY b.name
";
$buyerResult = $conn->query($buyerQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyers - Real Estate MLS</title>
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
            <li><a href="search_business.php">Search Business</a></li>
            <li><a href="agents.php">Agents</a></li>
            <li><a href="buyers.php" class="active">Buyers</a></li>
            <li><a href="custom_query.php">Custom Query</a></li>
        </ul>
    </nav>

    <main class="container">
        <section class="listings-section">
            <h2> Registered Buyers</h2>
            <?php if ($buyerResult && $buyerResult->num_rows > 0): ?>
                <div class="cards-grid">
                    <?php while ($row = $buyerResult->fetch_assoc()): ?>
                        <div class="buyer-card">
                            <div class="card-header">
                                <h3><?php echo ($row['name']); ?></h3>
                                <span class="buyer-id">ID: <?php echo ($row['id']); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="info-row">
                                    <span class="label"> Phone:</span>
                                    <span class="value"><?php echo ($row['phone']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label">️ Looking For:</span>
                                    <span class="value property-type-badge <?php echo $row['propertyType']; ?>">
                                        <?php echo ucfirst(($row['propertyType'])); ?>
                                    </span>
                                </div>
                                
                                <div class="preferences-section">
                                    <h4>Preferences</h4>
                                    <?php if (trim($row['propertyType']) === 'house'): ?>
                                        <div class="info-row">
                                            <span class="label">️ Bedrooms:</span>
                                            <span class="value"><?php echo $row['bedrooms'] ? $row['bedrooms'] . '+' : 'Any'; ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="label"> Bathrooms:</span>
                                            <span class="value"><?php echo $row['bathrooms'] ? $row['bathrooms'] . '+' : 'Any'; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="info-row">
                                            <span class="label"> Business Type:</span>
                                            <span class="value"><?php echo $row['businessPropertyType'] ? ucwords((trim($row['businessPropertyType']))) : 'Any'; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="info-row">
                                        <span class="label"> Price Range:</span>
                                        <span class="value">
                                            <?php echo formatPrice($row['minimumPreferredPrice']); ?> - 
                                            <?php echo formatPrice($row['maximumPreferredPrice']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="working-with">
                                    <span class="label">Working with:</span>
                                    <span class="agents-list">
                                        <?php echo $row['workingAgents'] ? ($row['workingAgents']) : 'No agent assigned'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-results">No buyers found in the system.</p>
            <?php endif; ?>
        </section>

        <!-- Buyers Table View -->
        <?php 
        $buyerResult->data_seek(0); // Reset result pointer
        ?>
        <section class="listings-section">
            <h2> Buyers Table View</h2>
            <?php if ($buyerResult && $buyerResult->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="listings-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Bed/Bath or Biz Type</th>
                                <th>Price Range</th>
                                <th>Working With</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $buyerResult->fetch_assoc()): ?>
                                <tr>
                                    <td class="centered"><?php echo ($row['id']); ?></td>
                                    <td><?php echo ($row['name']); ?></td>
                                    <td><?php echo ($row['phone']); ?></td>
                                    <td>
                                        <span class="property-type-badge <?php echo trim($row['propertyType']); ?>">
                                            <?php echo ucfirst((trim($row['propertyType']))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (trim($row['propertyType']) === 'house'): ?>
                                            <?php echo $row['bedrooms']; ?> BR / <?php echo $row['bathrooms']; ?> BA
                                        <?php else: ?>
                                            <?php echo $row['businessPropertyType'] ? ucwords((trim($row['businessPropertyType']))) : 'Any'; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo formatPrice($row['minimumPreferredPrice']); ?> - 
                                        <?php echo formatPrice($row['maximumPreferredPrice']); ?>
                                    </td>
                                    <td><?php echo $row['workingAgents'] ? ($row['workingAgents']) : '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; <?php echo date('Y'); ?> Real Estate MLS System | Database Project</p>
    </footer>
</body>
</html>
<?php closeDBConnection($conn); ?>
