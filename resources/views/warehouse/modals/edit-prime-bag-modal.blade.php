<!-- Edit Prime Bag Modal -->
<div class="modal fade" id="editPrimeBagModal" tabindex="-1" aria-labelledby="editPrimeBagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editPrimeBagModalLabel">
                    <i class="fas fa-crown me-2"></i>
                    Edit Prime Bag
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPrimeBagForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-crown me-2"></i>
                        Edit prime bag functionality will be implemented here.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-crown me-1"></i>
                        Update Prime Bag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editPrimeBag(primeBagId) {
        console.log('Edit prime bag:', primeBagId);
        const modal = new bootstrap.Modal(document.getElementById('editPrimeBagModal'));
        modal.show();
    }
</script>