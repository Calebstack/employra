<button
    type="button"
    class="profile-photo-trigger"
    data-bs-toggle="modal"
    data-bs-target="#{{ $id }}"
    aria-label="View larger profile photo for {{ $alt }}"
>
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        width="{{ $width }}"
        height="{{ $height }}"
        class="{{ $imageClass }}"
    >
</button>

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content profile-photo-preview-modal">
            <div class="modal-header border-0">
                <h2 class="modal-title fs-6" id="{{ $id }}Title">Profile photo</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <img src="{{ $src }}" alt="{{ $alt }}" class="img-fluid profile-photo-preview-image">
            </div>
        </div>
    </div>
</div>
