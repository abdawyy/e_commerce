@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ __('Success!') }}</strong> {{ session('success') }}
    </div>
@endif
