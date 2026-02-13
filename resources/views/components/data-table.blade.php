@php
    $isArabic = app()->getLocale() === 'ar';
    $languageLabel = __('table.language');
    if ($languageLabel === 'table.language') {
        $languageLabel = 'Language';
    }
@endphp

<div class="container-fluid px-0 {{ $isArabic ? 'text-end' : '' }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        {{-- Language Switch --}}
        <div class="d-flex align-items-center gap-2 small text-muted">
            <span class="text-uppercase">{{ $languageLabel }}:</span>
            <a href="{{ url('/lang/en') }}" class="{{ !$isArabic ? 'fw-bold text-primary' : 'text-dark' }}">EN</a>
            <span class="text-muted">|</span>
            <a href="{{ url('/lang/ar') }}" class="{{ $isArabic ? 'fw-bold text-primary' : 'text-dark' }}">العربية</a>
        </div>

        {{-- Search Form --}}
        <form action="{{ url()->current() }}" method="GET" class="w-100 w-md-auto">
            <div class="input-group search-group">
                <input type="text" name="search" class="form-control"
                       placeholder="{{ __('table.search_placeholder') }}" value="{{ request('search') }}">
                <button class="btn btn-dark" type="submit">{{ __('table.search_button') }}</button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive shadow-sm rounded-4">
        <table class="table table-modern align-middle mb-0">
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
                                <td data-label="{{ __('table.headers.' . strtolower($header)) ?? $header }}">
                                    @if($header === 'Action')
                                        <div class="table-actions {{ $isArabic ? 'justify-content-start' : 'justify-content-end' }}">
                                            <a class="btn btn-primary btn-sm" href="{{ $url }}/edit/{{ $row['ID'] }}">
                                                {{ __('table.view') }}
                                            </a>

                                            @if (isset($row['is_active']))
                                                <a class="btn btn-sm {{ $row['is_active'] == 1 ? 'btn-success' : 'btn-warning' }}"
                                                   href="{{ $url }}/status/{{ $row['ID'] }}">
                                                    {{ $row['is_active'] == 1 ? __('table.status_active') : __('table.status_inactive') }}
                                                </a>
                                            @endif

                                            @if (isset($row['is_highest']))
                                                <a class="btn btn-sm {{ $row['is_highest'] == 1 ? 'btn-primary' : 'btn-secondary' }}"
                                                   href="{{ route('admin.products.toggleHighestStatus', $row['ID']) }}">
                                                    {{ $row['is_highest'] == 1 ? __('table.status_highest') : __('table.status_normal') }}
                                                </a>
                                            @endif
                                        </div>
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
