<?php
$page_title = "Admin Dashboard";
include 'admin_header.php';
include '../include/config.php';

// table map for display
$tables = [
    'Business Permits' => 'business_permit',
    'Permit Renewals' => 'business_permit_renewal',
    'Barangay Clearances' => 'barangay_clearance',
    'Indigencies' => 'indigency',
    'Animal Bite Reports' => 'animal_bite_reports'
];

// counts
$counts = [];
foreach ($tables as $label => $t) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM $t");
    $counts[$label] = (int)$stmt->fetchColumn();
}

// =============================
// Requests per month (current year)
// =============================
$currentYear = (int)date('Y');
$monthly = array_fill(1, 12, 0); // months 1–12 initialized to 0

foreach ($tables as $label => $t) {
    try {
        $stmt = $pdo->query("
            SELECT MONTH(created_at) as m, COUNT(*) as c 
            FROM $t 
            WHERE created_at IS NOT NULL AND YEAR(created_at) = $currentYear 
            GROUP BY MONTH(created_at)
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $m = (int)$r['m'];
            $monthly[$m] += (int)$r['c'];
        }
    } catch (Exception $e) {
        // ignore tables without created_at
    }
}
?>
<div class="row mb-4">
    <?php foreach ($counts as $label => $c): ?>
    <div class="col-md-2 col-sm-4 mb-3">
        <div class="card card-stat p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small-muted" style="color: black;"><?= htmlspecialchars($label) ?></div>
                    <h4 class="mt-2 "><?= intval($c) ?></h4>
                </div>
                <div><i class="fa fa-file-invoice fa-2x"></i></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Graph Container -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Requests in <?= $currentYear ?> (per month)</h5>
    </div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; height:400px; width:100%">
            <canvas id="monthChart"></canvas>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('monthChart').getContext('2d');

    // Destroy old chart instance if it exists
    if (window.monthChartInstance) {
        window.monthChartInstance.destroy();
    }

    window.monthChartInstance = new Chart(ctx, {
        type: 'line', // change to 'bar' if you prefer bars
        data: {
            labels: [
                "Jan","Feb","Mar","Apr","May","Jun",
                "Jul","Aug","Sep","Oct","Nov","Dec"
            ],
            datasets: [{
                label: 'Requests in <?= $currentYear ?>',
                data: <?= json_encode(array_values($monthly)) ?>,
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.3)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: "#333"
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Requests'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            }
        }
    });
});
</script>
