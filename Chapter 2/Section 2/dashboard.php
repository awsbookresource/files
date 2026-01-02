<?php
$bucket = 'supply-chain-inventory-bucket';
$file = 'inventory_data.csv';

$url = "https://$bucket.s3.amazonaws.com/$file";
$data = array_map('str_getcsv', file($url));
$headers = array_shift($data);

// Prepare data arrays for visualization
$parts = [];
$quantities = [];
$locations = [];
$daysOfSupply = [];
$reorderLevels = [];

foreach ($data as $row) {
    $index = array_flip($headers);
    $parts[] = $row[$index['product.partNumber']];
    $quantities[] = (int)$row[$index['quantity']];
    $locations[] = $row[$index['location.locationIdentifier']];
    $daysOfSupply[] = (int)$row[$index['daysOfSupply']];
    $reorderLevels[] = (int)$row[$index['reorderLevel']];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Supply Chain Inventory Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            position: sticky;
            top: 0;
            background: white;
        }
    </style>
</head>
<body>
    <h1>Supply Chain Inventory Dashboard</h1>

    <div class="chart-container">
        <h2>Inventory Levels by Part Number</h2>
        <canvas id="quantityChart"></canvas>
    </div>

    <div class="chart-container">
        <h2>Days of Supply vs Reorder Levels</h2>
        <canvas id="supplyChart"></canvas>
    </div>

    <div class="table-container">
        <h2>Detailed Inventory Report</h2>
        <table>
            <tr>
                <th>Part Number</th>
                <th>Location</th>
                <th>Quantity</th>
                <th>Days of Supply</th>
                <th>Reorder Level</th>
                <th>Value</th>
                <th>Lead Time</th>
            </tr>
            <?php foreach ($data as $row) { ?>
                <tr>
                    <td><?php echo $row[$index['product.partNumber']]; ?></td>
                    <td><?php echo $row[$index['location.locationIdentifier']]; ?></td>
                    <td><?php echo $row[$index['quantity']] . ' ' . $row[$index['quantityUnits']]; ?></td>
                    <td><?php echo $row[$index['daysOfSupply']]; ?></td>
                    <td><?php echo $row[$index['reorderLevel']]; ?></td>
                    <td><?php echo $row[$index['value']] . ' ' . $row[$index['valueCurrency']]; ?></td>
                    <td><?php echo $row[$index['expectedLeadTime']]; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script>
        // Quantity Chart
        var quantityCtx = document.getElementById('quantityChart').getContext('2d');
        new Chart(quantityCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($parts); ?>,
                datasets: [{
                    label: 'Current Quantity',
                    data: <?php echo json_encode($quantities); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity'
                        }
                    }
                }
            }
        });

        // Supply vs Reorder Chart
        var supplyCtx = document.getElementById('supplyChart').getContext('2d');
        new Chart(supplyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($parts); ?>,
                datasets: [{
                    label: 'Days of Supply',
                    data: <?php echo json_encode($daysOfSupply); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                },
                {
                    label: 'Reorder Level',
                    data: <?php echo json_encode($reorderLevels); ?>,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Days'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

