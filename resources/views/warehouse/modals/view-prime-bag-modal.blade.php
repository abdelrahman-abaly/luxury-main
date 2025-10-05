<!-- View Prime Bag Modal -->
<div class="modal fade" id="viewPrimeBagModal" tabindex="-1" aria-labelledby="viewPrimeBagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="viewPrimeBagModalLabel">
                    <i class="fas fa-crown me-2"></i>
                    Prime Bag Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-crown me-2"></i>
                    View prime bag details functionality will be implemented here.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewPrimeBag(primeBagId) {
        console.log('View prime bag:', primeBagId);
        const modal = new bootstrap.Modal(document.getElementById('viewPrimeBagModal'));
        modal.show();
    }
</script>