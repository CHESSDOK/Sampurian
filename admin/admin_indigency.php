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

$mark = $pdo->prepare("UPDATE notification SET is_read_admin=1 
                       WHERE recipient_type='admin' AND module='indigency' AND is_read_admin=0");
$mark->execute();

?>
<div class="card p-3">
    <div class="table-wrap">
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>tracking</th>
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
                <td>
                    <?php
                    $created = new DateTime($r['created_at']);
                    $now = new DateTime();
                    $diff = $created->diff($now);

                    if ($diff->y > 0) {
                        echo $diff->y . " year(s) ago";
                    } elseif ($diff->m > 0) {
                        echo $diff->m . " month(s) ago";
                    } elseif ($diff->d > 0) {
                        echo $diff->d . " day(s) ago";
                    } elseif ($diff->h > 0) {
                        echo $diff->h . " hour(s) ago";
                    } elseif ($diff->i > 0) {
                        echo $diff->i . " minute(s) ago";
                    } else {
                        echo "just now";
                    }
                    ?>
                </td>
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
                showToast('Request approved successfully');
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
            showToast('Error approving request', 'danger');
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
                showToast('Request declined successfully');
                
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
            showToast('Error declining request', 'danger');
        });
    });
});
</script>