<!-- View Box Modal -->
<div class="modal fade" id="viewBoxModal" tabindex="-1" aria-labelledby="viewBoxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewBoxModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Box Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    View box details functionality will be implemented here.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewBox(boxId) {
        console.log('View box:', boxId);
        const modal = new bootstrap.Modal(document.getElementById('viewBoxModal'));
        modal.show();
    }
</script>