<?php

// Real Estate MLS - Custom Query Page
// Allows users to manually enter any database query


require_once 'config.php';
$conn = getDBConnection();

$results = null;
$error = null;
$queryExecuted = false;
$query = '';
$executionTime = 0;

// Process custom query
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = trim($_POST['query']);
    
    if (!empty($query)) {
        $queryExecuted = true;
        
        // Security check - only allow SELECT queries
        $queryUpper = strtoupper(trim($query));
        if (strpos($queryUpper, 'SELECT') !== 0) {
            $error = "For security reasons, only SELECT queries are allowed through this interface.";
        } else {
            // Execute query and measure time
            $startTime = microtime(true);
            $result = $conn->query($query);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($result === false) {
                $error = "Query Error: " . $conn->error;
            } else {
                $results = $result;
            }
        }
    } else {
        $error = "Please enter a query.";
    }
}

// Sample queries for quick testing
$sampleQueries = [
    "All Houses" => "SELECT * FROM House",
    "All Properties" => "SELECT * FROM Property",
    "All Agents with Firms" => "SELECT a.agentId, a.name, a.phone, f.name AS firmName FROM Agent a JOIN Firm f ON a.firmId = f.id",
    "Houses Under $300K" => "SELECT h.address, p.price, h.bedrooms, h.bathrooms FROM House h JOIN Property p ON h.address = p.address WHERE p.price < 300000",
    "Office Spaces" => "SELECT bp.address, p.price, bp.size FROM BusinessProperty bp JOIN Property p ON bp.address = p.address WHERE TRIM(bp.type) = 'office space'",
    "Buyer-Agent Pairs" => "SELECT b.name AS buyer, a.name AS agent FROM Buyer b JOIN Works_With ww ON b.id = ww.buyerId JOIN Agent a ON ww.agentId = a.agentId",
    "Listing Count by Agent" => "SELECT a.name, COUNT(l.mlsNumber) AS listings FROM Agent a LEFT JOIN Listings l ON a.agentId = l.agentId GROUP BY a.agentId, a.name"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Query - Real Estate MLS</title>
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
            <li><a href="buyers.php">Buyers</a></li>
            <li><a href="custom_query.php" class="active">Custom Query</a></li>
        </ul>
    </nav>

    <main class="container">
        <section class="query-section">
            <h2> Custom SQL Query</h2>
            <p class="section-description">Enter any SELECT query to retrieve data from the database.</p>
            
            <form method="POST" action="" class="query-form">
                <div class="form-group">
                    <label for="query">SQL Query:</label>
                    <textarea id="query" name="query" rows="6" 
                              placeholder="Enter your SQL query here...&#10;Example: SELECT * FROM Property"><?php echo ($query); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Execute Query</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('query').value = '';">Clear</button>
                </div>
            </form>
            
            <!-- Sample Queries -->
            <div class="sample-queries">
                <h3>Quick Sample Queries:</h3>
                <div class="sample-buttons">
                    <?php foreach ($sampleQueries as $name => $sampleQuery): ?>
                        <button type="button" class="btn btn-sample" 
                                onclick="document.getElementById('query').value = '<?php echo addslashes($sampleQuery); ?>';">
                            <?php echo ($name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Database Schema Reference -->
            <div class="schema-reference">
                <h3> Database Schema Reference:</h3>
                <div class="schema-tables">
                    <div class="schema-table">
                        <strong>Property</strong>
                        <code>address (PK), ownerName, price</code>
                    </div>
                    <div class="schema-table">
                        <strong>House</strong>
                        <code>address (PK/FK), bedrooms, bathrooms, size</code>
                    </div>
                    <div class="schema-table">
                        <strong>BusinessProperty</strong>
                        <code>address (PK/FK), type, size</code>
                    </div>
                    <div class="schema-table">
                        <strong>Agent</strong>
                        <code>agentId (PK), name, phone, firmId (FK), dateStarted</code>
                    </div>
                    <div class="schema-table">
                        <strong>Firm</strong>
                        <code>id (PK), name, address</code>
                    </div>
                    <div class="schema-table">
                        <strong>Listings</strong>
                        <code>mlsNumber (PK), address (FK), agentId (FK), dateListed</code>
                    </div>
                    <div class="schema-table">
                        <strong>Buyer</strong>
                        <code>id (PK), name, phone, propertyType, bedrooms, bathrooms, businessPropertyType, minPrice, maxPrice</code>
                    </div>
                    <div class="schema-table">
                        <strong>Works_With</strong>
                        <code>buyerId (PK/FK), agentId (PK/FK)</code>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($queryExecuted): ?>
            <section class="results-section">
                <h2>Query Results</h2>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <strong>Error:</strong> <?php echo ($error); ?>
                    </div>
                <?php elseif ($results): ?>
                    <div class="query-info">
                        <p><strong>Query executed successfully!</strong></p>
                        <p>Rows returned: <?php echo $results->num_rows; ?> | Execution time: <?php echo $executionTime; ?> ms</p>
                    </div>
                    
                    <?php if ($results->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="listings-table results-table">
                                <thead>
                                    <tr>
                                        <?php 
                                        // Get field names from result
                                        $fields = $results->fetch_fields();
                                        foreach ($fields as $field): 
                                        ?>
                                            <th><?php echo ($field->name); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $results->fetch_assoc()): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?php echo $value !== null ? ($value) : '<em class="null-value">NULL</em>'; ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="no-results">Query returned no results.</p>
                    <?php endif; ?>
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
