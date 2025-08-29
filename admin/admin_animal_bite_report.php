<?php
$page_title = "Animal Bite Reports";
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
                <th>Victim Details</th>
                <th>Bite Details</th>
                <th>Animal Details</th>
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
                <td>
                    <strong>Name:</strong> <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?><br>
                    <strong>Age:</strong> <?= htmlspecialchars($r['age']) ?><br>
                    <strong>Gender:</strong> <?= htmlspecialchars($r['gender']) ?>
                </td>
                <td>
                    <strong>Location:</strong> <?= htmlspecialchars($r['bite_location']) ?><br>
                    <strong>Body Part:</strong> <?= htmlspecialchars($r['body_part']) ?><br>
                    <strong>Date:</strong> <?= htmlspecialchars($r['bite_date']) ?>
                </td>
                <td>
                    <strong>Description:</strong> <?= htmlspecialchars($r['animal_description']) ?><br>
                    <strong>Color:</strong> <?= htmlspecialchars($r['color']) ?><br>
                    <strong>Condition:</strong> <?= htmlspecialchars($r['animal_condition']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($r['payment_method'] ?? '-') ?>
                    <?php if (!empty($r['gcash_ref_no'])): ?>
                        <div>Ref#: <?= htmlspecialchars($r['gcash_ref_no']) ?></div>
                        <?php if (!empty($r['payment_proof'])): ?>
                            <a href="<?= htmlspecialchars($r['payment_proof']) ?>" target="_blank">Proof</a>
                        <?php endif; ?>
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
                    <button class="btn btn-success btn-sm approveBtn" data-id="<?= $r['id'] ?>" data-type="animal_bite">Approve</button>
                    <button class="btn btn-danger btn-sm declineBtn" data-id="<?= $r['id'] ?>" data-type="animal_bite">Decline</button>
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
                showToast('Animal bite report approved successfully');
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
            showToast('Error approving animal bite report', 'danger');
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
                showToast('Animal bite report declined successfully');
                
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
            showToast('Error declining animal bite report', 'danger');
        });
    });
});
</script>