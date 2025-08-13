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

// requests per year (last 5 years) aggregated across tables (approx)
$years = [];
$currentYear = (int)date('Y');
for ($i=4;$i>=0;$i--) $years[] = $currentYear - $i;

$yearly = [];
foreach ($years as $y) $yearly[$y] = 0;

foreach ($tables as $label => $t) {
    // try to use created_at if exists
    try {
        $stmt = $pdo->query("SELECT YEAR(created_at) as y, COUNT(*) as c FROM $t WHERE created_at IS NOT NULL GROUP BY YEAR(created_at)");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $yr = (int)$r['y'];
            if (isset($yearly[$yr])) $yearly[$yr] += (int)$r['c'];
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
                    <div class="small-muted"><?= htmlspecialchars($label) ?></div>
                    <h4 class="mt-2"><?= intval($c) ?></h4>
                </div>
                <div><i class="fa fa-file-invoice fa-2x"></i></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card p-3 mb-4">
    <h5>Requests in last 5 years</h5>
    <canvas id="yearChart" style="height:220px"></canvas>
</div>

<?php include 'admin_footer.php'; ?>



