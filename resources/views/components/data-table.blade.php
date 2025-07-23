@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

<div class="container {{ $isArabic ? 'text-end' : '' }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    {{-- Validation Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Language Switch --}}
    <div class="d-flex justify-content-{{ $isArabic ? 'start' : 'end' }} mb-3">
        <a href="{{ url('/lang/en') }}" class="mx-2 {{ !$isArabic ? 'fw-bold text-primary' : 'text-dark' }}">EN</a> |
        <a href="{{ url('/lang/ar') }}" class="mx-2 {{ $isArabic ? 'fw-bold text-primary' : 'text-dark' }}">العربية</a>
    </div>

    {{-- Search Form --}}
    <form action="{{ url()->current() }}" method="GET"
          class="mb-3 d-flex justify-content-{{ $isArabic ? 'start' : 'end' }}">
        <div class="input-group w-25">
            <input type="text" name="search" class="form-control mx-2"
                   placeholder="{{ __('table.search_placeholder') }}" value="{{ request('search') }}">
            <button class="btn btn-dark" type="submit">{{ __('table.search_button') }}</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col">{{ __('table.headers.' . strtolower($header)) ?? $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if($rows->isEmpty())
                    <tr>
                        <td colspan="{{ count($headers) }}" class="text-center">
                            {{ __('table.no_results') }}
                        </td>
                    </tr>
                @else
                    @foreach ($rows as $row)
                        <tr>
                            @foreach ($headers as $header)
                                <td>
                                    @if($header === 'Action')
                                        <a class="btn btn-primary" href="{{ $url }}/edit/{{ $row['ID'] }}">
                                            {{ __('table.view') }}
                                        </a>

                                        @if (isset($row['is_active']))
                                            <a class="btn {{ $row['is_active'] == 1 ? 'btn-success' : 'btn-warning' }}"
                                               href="{{ $url }}/status/{{ $row['ID'] }}">
                                                {{ $row['is_active'] == 1 ? __('table.status_active') : __('table.status_inactive') }}
                                            </a>
                                        @endif
                                    @else
                                        {{ $row[$header] ?? '' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
