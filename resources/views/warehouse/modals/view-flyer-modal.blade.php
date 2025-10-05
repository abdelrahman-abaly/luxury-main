<!-- View Flyer Modal -->
<div class="modal fade" id="viewFlyerModal" tabindex="-1" aria-labelledby="viewFlyerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewFlyerModalLabel">
                    <i class="fas fa-file-alt me-2"></i>
                    Flyer Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-file-alt me-2"></i>
                    View flyer details functionality will be implemented here.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewFlyer(flyerId) {
        console.log('View flyer:', flyerId);
        const modal = new bootstrap.Modal(document.getElementById('viewFlyerModal'));
        modal.show();
    }
</script>