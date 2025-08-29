<?php
$page_title = "Barangay Clearance Requests";
include 'admin_header.php';
include '../include/config.php';

$sql = "SELECT b.*, u.f_name, u.m_name, u.l_name, u.address 
        FROM barangay_clearance b
        LEFT JOIN users u ON b.user_id = u.id
        ORDER BY b.created_at DESC";
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
                <th>Years in Barangay</th>
                <th>Purpose</th>
                <th>Attachment</th>
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
                <td><?= htmlspecialchars($r['years_stay_in_barangay'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['purpose'] ?? '-') ?></td>
                <td>
                    <?php if (!empty($r['attachment'])): ?>
                        <a href="<?= htmlspecialchars($r['attachment']) ?>" target="_blank">View Attachment</a>
                    <?php endif; ?>
                </td>
                <td>
                    <?= htmlspecialchars($r['payment_type'] ?? '-') ?>
                    <?php if (!empty($r['gcash_ref_no'])): ?>
                        <div>GCash Ref: <?= htmlspecialchars($r['gcash_ref_no']) ?></div>
                        <a href="<?= htmlspecialchars($r['payment_proof']) ?>" target="_blank">Proof</a>
                    <?php else: ?>
                        -
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
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $r['id'] ?>" data-type="barangay_clearance">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $r['id'] ?>" data-type="barangay_clearance">Decline</button>
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
                showToast('Barangay clearance request approved successfully');
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
            showToast('Error approving barangay clearance request', 'danger');
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
                showToast('Barangay clearance request declined successfully');
                
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
            showToast('Error declining barangay clearance request', 'danger');
        });
    });
});
</script>