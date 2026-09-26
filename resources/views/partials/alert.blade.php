{{-- File: resources/views/partials/alert.blade.php --}}
@if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
