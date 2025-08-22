<?php
$page_title = "Barangay Indigency Requests";
include 'admin_header.php';
include '../include/config.php';

$sql = "SELECT i.*, u.f_name, u.m_name, u.l_name, u.address 
        FROM indigency i
        LEFT JOIN users u ON i.user_id = u.id
        ORDER BY i.created_at DESC";
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
                <th>Nature of Assistance</th>
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
                <td><?= htmlspecialchars(trim($r['f_name'].' '.$r['m_name'].' '.$r['l_name'])) ?></td>
                <td><?= htmlspecialchars($r['address'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['nature_of_assistance'] ?? '-') ?></td>
                <td>
                    <?= htmlspecialchars($r['payment_type'] ?? '-') ?>
                    <?php if (!empty($r['gcash_ref_no'])): ?>
                        <div class="small-muted">Ref#: <?= htmlspecialchars($r['gcash_ref_no']) ?></div>
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
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $r['id'] ?>" data-type="indigency">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $r['id'] ?>" data-type="indigency">Decline</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<div class="modal fade" id="declineModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5>Decline Request</h5></div>
      <div class="modal-body">
        <textarea id="declineComment" class="form-control" placeholder="Enter reason"></textarea>
        <input type="hidden" id="declineId">
        <input type="hidden" id="declineType">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmDecline" class="btn btn-danger">Submit</button>
      </div>
    </div>
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
