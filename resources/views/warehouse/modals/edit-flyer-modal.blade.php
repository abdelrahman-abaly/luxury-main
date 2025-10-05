<!-- Edit Flyer Modal -->
<div class="modal fade" id="editFlyerModal" tabindex="-1" aria-labelledby="editFlyerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editFlyerModalLabel">
                    <i class="fas fa-file-alt me-2"></i>
                    Edit Flyer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFlyerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-file-alt me-2"></i>
                        Edit flyer functionality will be implemented here.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-file-alt me-1"></i>
                        Update Flyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editFlyer(flyerId) {
        console.log('Edit flyer:', flyerId);
        const modal = new bootstrap.Modal(document.getElementById('editFlyerModal'));
        modal.show();
    }
</script>