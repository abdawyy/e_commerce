@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ __('Error!') }}</strong> {{ session('error') }}
    </div>
    @endif
