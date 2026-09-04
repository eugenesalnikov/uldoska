@if (session('success'))
  <div class="flash" role="status">{{ session('success') }}</div>
@endif

@if (session('error'))
  <div class="flash flash-error" role="alert">{{ session('error') }}</div>
@endif

@if ($errors->any())
  <div class="flash flash-error" role="alert">
    {{ $errors->first() }}
  </div>
@endif
