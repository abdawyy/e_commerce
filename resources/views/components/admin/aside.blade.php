@php
    $isDashboard = Route::is('admin.dashboard');
    $isProducts = Route::is('products.*');
    $isCategories = Route::is('categories.*');
    $isAdmins = Route::is('admin.list') || Route::is('admin.register');
    $isCities = Route::is('cities.*');
    $isTypes = Route::is('type.*');
    $isDiscounts = Route::is('discountCodes.*');
    $isOrders = Route::is('order.*');
    $isUsers = Route::is('users.*');
    $isContacts = Route::is('admin.contact.*');
    $isGuests = Route::is('admin.guest.*');
@endphp

<!-- ======= Sidebar ======= -->
<aside id="sidebar"
    class="sidebar pt-5"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    style="width: 280px; height: 100vh; background: #fff; overflow-y: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); z-index: 999;">

    <ul class="sidebar-nav {{ app()->getLocale() === 'ar' ? 'text-end pe-3' : 'text-start ps-3' }}" id="sidebar-nav">

        <!-- Language Switch -->
        <li class="nav-item">
            <div class="d-flex justify-content-center px-4 pt-3">
                <a class="text-decoration-none mx-2 {{ app()->getLocale() == 'en' ? 'fw-bold text-primary' : 'text-black' }}"
                    href="{{ url('/lang/en') }}">EN</a>
                |
                <a class="text-decoration-none mx-2 {{ app()->getLocale() == 'ar' ? 'fw-bold text-primary' : 'text-black' }}"
                    href="{{ url('/lang/ar') }}">العربية</a>
            </div>
        </li>

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link {{ $isDashboard ? 'active' : 'collapsed' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid mx-2"></i>
                <span>{{ __('admin_sidebar.dashboard') }}</span>
            </a>
        </li>

        <!-- Products -->
        <li class="nav-item">
            <a class="nav-link {{ $isProducts ? '' : 'collapsed' }}" data-bs-target="#Products-nav" data-bs-toggle="collapse" href="#">
                <i class="bx bx-package mx-2"></i>
                <span>{{ __('admin_sidebar.products') }}</span>
                <i
                    class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <ul id="Products-nav" class="nav-content collapse {{ $isProducts ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li><a class="{{ Route::is('products.list') ? 'active' : '' }}" href="{{ route('products.list') }}">{{ __('admin_sidebar.view_products') }}</a></li>
                <li><a class="{{ Route::is('products.edit') ? 'active' : '' }}" href="{{ route('products.edit') }}">{{ __('admin_sidebar.add_product') }}</a></li>
            </ul>
        </li>

        <!-- Categories -->
        <li class="nav-item">
            <a class="nav-link {{ $isCategories ? '' : 'collapsed' }}" data-bs-target="#Categories-nav" data-bs-toggle="collapse" href="#">
                <i class="bx bx-category mx-2"></i>
                <span>{{ __('admin_sidebar.categories') }}</span>
                <i
                    class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <ul id="Categories-nav" class="nav-content collapse {{ $isCategories ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li><a class="{{ Route::is('categories.list') ? 'active' : '' }}" href="{{ route('categories.list') }}">{{ __('admin_sidebar.view_categories') }}</a></li>
                <li><a class="{{ Route::is('categories.edit') ? 'active' : '' }}" href="{{ route('categories.edit') }}">{{ __('admin_sidebar.add_category') }}</a></li>
            </ul>
        </li>

        <!-- Admins -->
        <li class="nav-item">
            <a class="nav-link {{ $isAdmins ? '' : 'collapsed' }}" data-bs-target="#Admins-nav" data-bs-toggle="collapse" href="#">
                <i class="fa-regular fa-chess-king mx-2"></i>
                <span>{{ __('admin_sidebar.admins') }}</span>
                <i
                    class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <ul id="Admins-nav" class="nav-content collapse {{ $isAdmins ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li><a class="{{ Route::is('admin.list') ? 'active' : '' }}" href="{{ route('admin.list') }}">{{ __('admin_sidebar.view_admins') }}</a></li>
                <li><a class="{{ Route::is('admin.register') ? 'active' : '' }}" href="{{ route('admin.register') }}">{{ __('admin_sidebar.add_admin') }}</a></li>
            </ul>
        </li>

        <!-- Cities -->
        <li class="nav-item">
            <a class="nav-link {{ $isCities ? '' : 'collapsed' }}" data-bs-target="#cities-nav" data-bs-toggle="collapse" href="#">
                <i class="fa-solid fa-city mx-2"></i>
                <span>{{ __('admin_sidebar.cities') }}</span>
                <i
                    class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <ul id="cities-nav" class="nav-content collapse {{ $isCities ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li><a class="{{ Route::is('cities.list') ? 'active' : '' }}" href="{{ route('cities.list') }}">{{ __('admin_sidebar.view_cities') }}</a></li>
                <li><a class="{{ Route::is('cities.edit') ? 'active' : '' }}" href="{{ route('cities.edit') }}">{{ __('admin_sidebar.add_city') }}</a></li>
            </ul>
        </li>

        <!-- Types -->
        <li class="nav-item">
            <a class="nav-link {{ $isTypes ? '' : 'collapsed' }}" data-bs-target="#Types-nav" data-bs-toggle="collapse" href="#">
                <i class="fa-solid fa-layer-group mx-2"></i>
                <span>{{ __('admin_sidebar.types') }}</span>
                <i
                    class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <ul id="Types-nav" class="nav-content collapse {{ $isTypes ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li><a class="{{ Route::is('type.list') ? 'active' : '' }}" href="{{ route('type.list') }}">{{ __('admin_sidebar.view_types') }}</a></li>
                <li><a class="{{ Route::is('type.edit') ? 'active' : '' }}" href="{{ route('type.edit') }}">{{ __('admin_sidebar.add_type') }}</a></li>
            </ul>
        </li>

        <!-- Discount Codes -->
        <li class="nav-item">
            <a class="nav-link {{ $isDiscounts ? '' : 'collapsed' }}" data-bs-target="#Discount-nav" data-bs-toggle="collapse" href="#">
                <i class="fa-solid fa-tags mx-2"></i>
                <span>{{ __('admin_sidebar.discount_codes') }}</span>
                <i
                    class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <ul id="Discount-nav" class="nav-content collapse {{ $isDiscounts ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li><a class="{{ Route::is('discountCodes.list') ? 'active' : '' }}" href="{{ route('discountCodes.list') }}">{{ __('admin_sidebar.view_discount_codes') }}</a></li>
                <li><a class="{{ Route::is('discountCodes.edit') ? 'active' : '' }}" href="{{ route('discountCodes.edit') }}">{{ __('admin_sidebar.add_discount_code') }}</a></li>
            </ul>
        </li>

        <!-- Pages -->
        <li class="nav-heading">{{ __('admin_sidebar.pages') }}</li>

        <li class="nav-item">
            <a class="nav-link {{ $isOrders ? 'active' : 'collapsed' }}" href="{{ route('order.list') }}">
                <i class="bx bx-file mx-2"></i>
                <span>{{ __('admin_sidebar.order_list') }}</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ $isUsers ? 'active' : 'collapsed' }}" href="{{ route('users.list') }}">
                <i class="bi bi-person mx-2"></i>
                <span>{{ __('admin_sidebar.user_list') }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $isContacts ? 'active' : 'collapsed' }}" href="{{ route('admin.contact.list') }}">
                <i class="bi bi-envelope-fill mx-2"></i>
                <span>{{ __('admin_sidebar.messages_list') }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $isGuests ? 'active' : 'collapsed' }}" href="{{ route('admin.guest.list') }}">
                <i class="bi bi-person mx-2"></i>
                <span>{{ __('admin_sidebar.guest_list') }}</span>
            </a>
        </li>

        <!-- Logout -->
        <li class="nav-item">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-dark w-100 mt-3">
                    {{ __('admin_sidebar.logout') }}
                </button>
            </form>
        </li>
    </ul>
</aside>