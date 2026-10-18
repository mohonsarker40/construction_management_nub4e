@if ($records->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
        <p class="small text-muted mb-2 mb-md-0">
            Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} results
        </p>
        {{ $records->links('pagination::bootstrap-4') }}
    </div>
@endif
