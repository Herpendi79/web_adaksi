@props([
    'type' => 'success', // success, danger, warning, info
    'message' => '',
    'title' => '',
    'autoClose' => true,
    'duration' => 3000, // ms
])

@php
    $alertTypes = [
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    $iconTypes = [
        'success' => 'bi bi-check-circle-fill',
        'danger' => 'bi bi-x-circle-fill',
        'warning' => 'bi bi-exclamation-triangle-fill',
        'info' => 'bi bi-info-circle-fill',
    ];
    $alertClass = $alertTypes[$type] ?? $alertTypes['info'];
    $iconClass = $iconTypes[$type] ?? $iconTypes['info'];
    $modalId = 'notificationModal_' . uniqid();
@endphp

<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-body p-4">
        <div class="d-flex align-items-center">
          <i class="{{ $iconClass }} fs-2 me-3 text-{{ $type }}"></i>
          <div>
            @if($title)
              <h5 class="mb-1">{{ $title }}</h5>
            @endif
            <div class="alert {{ $alertClass }} mb-0 border-0 bg-opacity-75">
              {!! $message !!}
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('{{ $modalId }}'));
        modal.show();

        @if($autoClose)
            setTimeout(function() {
                modal.hide();
            }, {{ $duration }});
        @endif
    });
</script>
@endpush

<!-- Usage example:
<x-notifications type="danger" title="Gagal!" message="Terjadi kesalahan." :autoClose="false" />
-->