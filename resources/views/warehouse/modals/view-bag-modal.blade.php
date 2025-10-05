<!-- View Bag Modal -->
<div class="modal fade" id="viewBagModal" tabindex="-1" aria-labelledby="viewBagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewBagModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Shopping Bag Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    View bag details functionality will be implemented here.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewBag(bagId) {
        console.log('View bag:', bagId);
        const modal = new bootstrap.Modal(document.getElementById('viewBagModal'));
        modal.show();
    }
</script>