<?php
$page_title = "Business Permit Renewals";
include 'admin_header.php';
include '../include/config.php';

$sql = "SELECT r.*, u.f_name, u.m_name, u.l_name, u.address 
        FROM business_permit_renewal r
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC";
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
                <th>Nature</th>
                <th>payment</th>
                <th>Attachments</th>
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
                <td><?= htmlspecialchars($r['name_kind_of_establishment'] ?? '-') ?></td>
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
                    <?php if (!empty($r['comment'])): ?><div class="small-muted"><?= htmlspecialchars($r['comment']) ?></div><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $r['id'] ?>" data-type="business_permit_renewal">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $r['id'] ?>" data-type="business_permit_renewal">Decline</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Reuse same decline modal as earlier -->
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

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="statusToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto">Notification</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toastMessage"></div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>

<script>
$(function(){
    // Function to show toast notification
    function showToast(message, type = 'success') {
        $('#toastMessage').text(message);
        $('#statusToast').removeClass('bg-success bg-danger').addClass('bg-' + type);
        var toast = new bootstrap.Toast(document.getElementById('statusToast'));
        toast.show();
    }

    $('.approveBtn').on('click', function(){
        var button = $(this);
        $.post('update_status.php', { 
            id: button.data('id'), 
            status:'Approved', 
            type: button.data('type') 
        }, function(response) {
            if (response === "OK") {
                showToast('Business permit renewal approved successfully');
                // Update the UI without full page reload
                var row = $('#row-' + button.data('id'));
                row.find('.badge')
                    .removeClass('bg-secondary bg-danger')
                    .addClass('bg-success')
                    .text('Approved');
                
                // Disable buttons after action
                button.prop('disabled', true);
                row.find('.declineBtn').prop('disabled', true);
            } else {
                showToast('Error: ' + response, 'danger');
            }
        }).fail(function() {
            showToast('Error approving business permit renewal', 'danger');
        });
    });

    $('.declineBtn').on('click', function(){
        $('#declineId').val($(this).data('id'));
        $('#declineType').val($(this).data('type'));
        $('#declineComment').val(''); // Clear previous comment
        $('#declineModal').modal('show');
    });

    $('#confirmDecline').on('click', function(){
        var declineId = $('#declineId').val();
        var declineType = $('#declineType').val();
        var comment = $('#declineComment').val();
        
        $.post('update_status.php', { 
            id: declineId, 
            status:'Declined', 
            comment: comment, 
            type: declineType 
        }, function(response) {
            if (response === "OK") {
                $('#declineModal').modal('hide');
                showToast('Business permit renewal declined successfully');
                
                // Update the UI without full page reload
                var row = $('#row-' + declineId);
                row.find('.badge')
                    .removeClass('bg-secondary bg-success')
                    .addClass('bg-danger')
                    .text('Declined');
                
                // Add comment if provided
                if (comment) {
                    row.find('.small-muted').remove(); // Remove existing comment if any
                    row.find('.badge').after('<div class="small-muted">' + comment + '</div>');
                }
                
                // Disable buttons after action
                row.find('.approveBtn, .declineBtn').prop('disabled', true);
            } else {
                showToast('Error: ' + response, 'danger');
            }
        }).fail(function() {
            showToast('Error declining business permit renewal', 'danger');
        });
    });
});
</script>