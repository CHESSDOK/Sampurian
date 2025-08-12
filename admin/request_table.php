<?php
function renderRequestTable($pdo, $table, $columns, $join_user = true) {
    $sql = "SELECT r.id, r.status, r.comment, r.payment_type, r.gcash_ref_no, r.payment_proof, r.created_at";
    foreach ($columns as $col) {
        $sql .= ", r.$col";
    }
    if ($join_user) {
        $sql .= ", u.f_name, u.m_name, u.l_name";
        $sql .= " FROM $table r JOIN users u ON r.user_id = u.id";
    } else {
        $sql .= " FROM $table r";
    }
    $sql .= " ORDER BY r.created_at DESC";

    $stmt = $pdo->query($sql);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>Full Name</th>
                <?php foreach ($columns as $col): ?>
                    <th><?= ucfirst(str_replace("_", " ", $col)) ?></th>
                <?php endforeach; ?>
                <th>Payment</th>
                <th>Status</th>
                <th>Requested On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $row): ?>
            <tr id="row-<?= $row['id'] ?>">
                <td><?= $join_user ? htmlspecialchars($row['f_name']." ".$row['m_name']." ".$row['l_name']) : "N/A" ?></td>
                <?php foreach ($columns as $col): ?>
                    <td><?= htmlspecialchars($row[$col]) ?></td>
                <?php endforeach; ?>
                <td>
                    <?= htmlspecialchars($row['payment_type']) ?>
                    <?php if ($row['payment_type'] === "gcash"): ?>
                        <br>Ref#: <?= htmlspecialchars($row['gcash_ref_no']) ?>
                        <br><a href="uploads/<?= htmlspecialchars($row['payment_proof']) ?>" target="_blank">View Proof</a>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-<?= ($row['status']=="Approved" ? "success" : ($row['status']=="Declined" ? "danger" : "secondary")) ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $row['id'] ?>" data-table="<?= $table ?>">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $row['id'] ?>" data-table="<?= $table ?>">Decline</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}
?>
<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5>Decline Request</h5></div>
      <div class="modal-body">
        <textarea id="declineComment" class="form-control" placeholder="Reason for decline"></textarea>
        <input type="hidden" id="declineId">
        <input type="hidden" id="declineTable">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDecline">Submit</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).on('click', '.approveBtn', function(){
    $.post('update_status.php', {
        id: $(this).data('id'),
        table: $(this).data('table'),
        status: 'Approved'
    }, function(){ location.reload(); });
});

$(document).on('click', '.declineBtn', function(){
    $('#declineId').val($(this).data('id'));
    $('#declineTable').val($(this).data('table'));
    $('#declineModal').modal('show');
});

$('#confirmDecline').click(function(){
    $.post('update_status.php', {
        id: $('#declineId').val(),
        table: $('#declineTable').val(),
        status: 'Declined',
        comment: $('#declineComment').val()
    }, function(){ location.reload(); });
});
</script>
