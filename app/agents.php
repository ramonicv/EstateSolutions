<?php

// Real Estate MLS - Agents Page
// Displays all agents and their associated information


require_once 'config.php';
$conn = getDBConnection();

// Fetch all agents with firm info and listing/buyer counts
$agentQuery = "
    SELECT a.agentId, a.name, a.phone, a.dateStarted,
           f.name AS firmName, f.address AS firmAddress,
           (SELECT COUNT(*) FROM Listings l WHERE l.agentId = a.agentId) AS listingCount,
           (SELECT COUNT(*) FROM Works_With ww WHERE ww.agentId = a.agentId) AS buyerCount
    FROM Agent a
    INNER JOIN Firm f ON a.firmId = f.id
    ORDER BY a.name
";
$agentResult = $conn->query($agentQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agents - Real Estate MLS</title>
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
            <li><a href="agents.php" class="active">Agents</a></li>
            <li><a href="buyers.php">Buyers</a></li>
            <li><a href="custom_query.php">Custom Query</a></li>
        </ul>
    </nav>

    <main class="container">
        <section class="listings-section">
            <h2> Real Estate Agents</h2>
            <?php if ($agentResult && $agentResult->num_rows > 0): ?>
                <div class="cards-grid">
                    <?php while ($row = $agentResult->fetch_assoc()): ?>
                        <div class="agent-card">
                            <div class="card-header">
                                <h3><?php echo ($row['name']); ?></h3>
                                <span class="agent-id">ID: <?php echo ($row['agentId']); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="info-row">
                                    <span class="label"> Phone:</span>
                                    <span class="value"><?php echo ($row['phone']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"> Firm:</span>
                                    <span class="value"><?php echo ($row['firmName']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"> Firm Address:</span>
                                    <span class="value"><?php echo ($row['firmAddress']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"> Started:</span>
                                    <span class="value"><?php echo ($row['dateStarted']); ?></span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="stat">
                                    <span class="stat-value"><?php echo $row['listingCount']; ?></span>
                                    <span class="stat-label">Active Listings</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value"><?php echo $row['buyerCount']; ?></span>
                                    <span class="stat-label">Buyers</span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-results">No agents found in the system.</p>
            <?php endif; ?>
        </section>

        <!-- Agents Table View -->
        <?php 
        $agentResult->data_seek(0); // Reset result pointer
        ?>
        <section class="listings-section">
            <h2> Agents Table View</h2>
            <?php if ($agentResult && $agentResult->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="listings-table">
                        <thead>
                            <tr>
                                <th>Agent ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Firm</th>
                                <th>Date Started</th>
                                <th>Listings</th>
                                <th>Buyers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $agentResult->fetch_assoc()): ?>
                                <tr>
                                    <td class="centered"><?php echo ($row['agentId']); ?></td>
                                    <td><?php echo ($row['name']); ?></td>
                                    <td><?php echo ($row['phone']); ?></td>
                                    <td><?php echo ($row['firmName']); ?></td>
                                    <td><?php echo ($row['dateStarted']); ?></td>
                                    <td class="centered"><?php echo $row['listingCount']; ?></td>
                                    <td class="centered"><?php echo $row['buyerCount']; ?></td>
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
