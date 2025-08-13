<?php
$page_title = "Animal Bite Investigation Reports";
include 'admin_header.php';
include '../include/config.php';

$sql = "SELECT a.*, u.f_name, u.m_name, u.l_name, u.address 
        FROM animal_bite_reports a
        LEFT JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card p-3">
    <div class="table-wrap">
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>Permit ID</th>
                <th>Requester</th>
                <th>Address</th>
                <th>Date Bitten</th>
                <th>Animal</th>
                <th>Pet Color / Marks</th>
                <th>Owner</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Requested On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): ?>
            <tr id="row-<?= $r['id'] ?>">
                <td><?= htmlspecialchars($r['permit_id']) ?></td>
                <td><?= htmlspecialchars(trim($r['l_name'].', '.$r['f_name'].', '.$r['m_name'])) ?></td>
                <td><?= htmlspecialchars($r['address'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['bite_date'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['animal_description'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['color'] . ' / ' . $r['marks']) ?></td>
                <td><?= htmlspecialchars($r['owner_name'] ?? '-') ?></td>
                <td>
                    <?= htmlspecialchars($r['payment_method'] ?? '-') ?>
                    <?php if (!empty($r['gcash_ref_no'])): ?>
                        <div class="small-muted">Ref#: <?= htmlspecialchars($r['gcash_ref_no']) ?></div>
                        <a href="<?= htmlspecialchars($r['payment_proof']) ?>" target="_blank">Proof</a>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-<?= $r['status']=="Approved" ? "success" : ($r['status']=="Declined" ? "danger" : "secondary") ?>">
                        <?= htmlspecialchars($r['status']) ?>
                    </span>
                    <?php if (!empty($r['comment'])): ?><div class="small-muted"><?= htmlspecialchars($r['comment']) ?></div><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $r['id'] ?>" data-type="animal_bite">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $r['id'] ?>" data-type="animal_bite">Decline</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php include 'admin_footer.php'; ?>

<script>
$(function(){
    $('.approveBtn').on('click', function(){
        $.post('update_status.php', { id: $(this).data('id'), status:'Approved', type: $(this).data('type') }, function(){ location.reload(); });
    });

    $('.declineBtn').on('click', function(){
        $('#declineId').val($(this).data('id'));
        $('#declineType').val($(this).data('type'));
        $('#declineModal').modal('show');
    });

    $('#confirmDecline').on('click', function(){
        $.post('update_status.php', { id: $('#declineId').val(), status:'Declined', comment: $('#declineComment').val(), type: $('#declineType').val() }, function(){ location.reload(); });
    });
});
</script>
