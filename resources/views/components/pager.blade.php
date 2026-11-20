{{-- Demo pager. Replace with {{ $paginator->links('components.pager') }} once you use real pagination. Expects: $total, $shown --}}
<div class="table-foot">
    <span>Showing 1–{{ $shown }} of {{ $total }}</span>
    <nav class="pager" aria-label="Pagination"><span class="off"><i class="bi bi-chevron-left"></i></span><span class="on">1</span><a href="#">2</a><a href="#">3</a><a href="#" aria-label="Next page"><i class="bi bi-chevron-right"></i></a></nav>
</div>
