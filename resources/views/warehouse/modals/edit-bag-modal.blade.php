<!-- Edit Bag Modal -->
<div class="modal fade" id="editBagModal" tabindex="-1" aria-labelledby="editBagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBagModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Edit Shopping Bag
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBagForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Similar form fields as add bag modal -->
                    <div class="alert alert-info">Edit bag functionality will be implemented here.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Update Bag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editBag(bagId) {
        console.log('Edit bag:', bagId);
        const modal = new bootstrap.Modal(document.getElementById('editBagModal'));
        modal.show();
    }
</script>