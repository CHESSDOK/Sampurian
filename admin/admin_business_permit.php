<?php
$page_title = "Business Permit Requests";
include 'admin_header.php';
include '../include/config.php';

// Select all columns from business_permit plus user name & address
$sql = "SELECT bp.*, u.f_name, u.m_name, u.l_name, u.address 
        FROM business_permit bp
        LEFT JOIN users u ON bp.user_id = u.id
        ORDER BY bp.created_at DESC";
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
                <th>Establishment</th>
                <th>Nature of Business</th>
                <th>payment</th>
                <th>attachment</th>
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
                <td><?= htmlspecialchars($r['kind_of_establishment'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['nature_of_business'] ?? '-') ?></td>
                <td>
                    <?= htmlspecialchars($r['payment_type'] ?? '-') ?>
                    <?php if (!empty($r['gcash_ref_no'])): ?>
                        <div class="small-muted">Ref#: <?= htmlspecialchars($r['gcash_ref_no']) ?></div>
                        <a href="<?= htmlspecialchars($r['payment_proof']) ?>" target="_blank">Proof</a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($r['business_registration'])): ?>
                        <a href="<?= htmlspecialchars($r['business_registration']) ?>" target="_blank">Business Reg</a><br>
                    <?php endif; ?>
                    <?php if (!empty($r['cedula'])): ?>
                        <a href="<?= htmlspecialchars($r['cedula']) ?>" target="_blank">Cedula</a><br>
                    <?php endif; ?>
                    <?php if (!empty($r['barangay_requirements'])): ?>
                        <a href="<?= htmlspecialchars($r['barangay_requirements']) ?>" target="_blank">Barangay Req</a>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-<?= $r['status']=="Approved" ? "success" : ($r['status']=="Declined" ? "danger" : "secondary") ?>">
                        <?= htmlspecialchars($r['status']) ?>
                    </span>
                    <?php if (!empty($r['comment'])): ?>
                        <div class="small-muted"><?= htmlspecialchars($r['comment']) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $r['id'] ?>" data-type="business_permit">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $r['id'] ?>" data-type="business_permit">Decline</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Decline Modal -->
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
        const id = $(this).data('id'), type = $(this).data('type');
        $.post('update_status.php', { id:id, status:'Approved', type:type }, function(){ location.reload(); });
    });

    $('.declineBtn').on('click', function(){
        $('#declineId').val($(this).data('id'));
        $('#declineType').val($(this).data('type'));
        $('#declineModal').modal('show');
    });

    $('#confirmDecline').on('click', function(){
        const id = $('#declineId').val(), type = $('#declineType').val(), comment = $('#declineComment').val();
        $.post('update_status.php', { id:id, status:'Declined', comment:comment, type:type }, function(){ location.reload(); });
    });
});
</script>
