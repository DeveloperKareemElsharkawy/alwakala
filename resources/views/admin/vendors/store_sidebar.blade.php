<ul class="navbar-nav">
    <li class="nav-item {{ Request::is('admin_panel/vendors/'.$store['id']) && request()->type != 'branches' ? 'active' : ''}}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/vendors', $store['id']) }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">معلومات رئيسية</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin_panel/vendors/'.$store['id']) && request()->type != 'branches' ? 'active' : ''}}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/vendors', $store['id']) }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">معلومات المتجر</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('admin_panel/products/'.$store['id']) ? 'active' : ''}}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/products', $store['id']) }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">المنتجات</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('admin_panel/shipping_addresses/'.$store['id']) ? 'active' : ''}}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/shipping_addresses', $store['id']) }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">مدن الشحن</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('admin_panel/delivery_addresses/'.$store['id']) ? 'active' : ''}}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/delivery_addresses', $store['id']) }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">اماكن التوصيل</span>
        </a>
    </li>

    <li class="nav-item {{ Request::is('admin_panel/purchases/'.$store['id']) ? 'active' : ''}}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/purchases', $store['id']) }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">المبيعات</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('admin_panel/orders/'.$store['id']) || Request::is('admin_panel/order/*') ? 'active' : ''}}">
        <a href="#purchase" class="nav-link menu-link {{ Request::is('admin_panel/orders/'.$store['id']) || Request::is('admin_panel/order/*') ? '' : 'collapsed'}}"
           data-bs-toggle="collapse"
           role="button" aria-expanded="{{ Request::is('admin_panel/orders/'.$store['id']) || Request::is('admin_panel/order/*') ? 'true' : 'false'}}" aria-controls="purchase">
            <i class="ph-record-fill"></i> <span data-key="t-layouts">المشتريات</span>
            <i class="ri-arrow-left-s-fill fs-5"></i>
        </a>
        <div class="collapse menu-dropdown {{ Request::is('admin_panel/orders/'.$store['id']) || Request::is('admin_panel/order/*') ? 'show' : ''}}" id="purchase">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ url('admin_panel/orders' , $store['id']) }}" class="nav-link" data-key="t-products">
                        <i class="bx bx-minus"></i>
                        المشتريات
                    </a>
                </li>
            </ul>
        </div>
    </li>
    @if(request()->type != 'branch')
    <li class="nav-item {{ Request::is('admin_panel/vendors/'.$store['id']) && request()->type == 'branches' ? 'active' : '' }}">
        <a class="nav-link menu-link" href="{{ url('admin_panel/vendors/'.$store['id'].'?type=branches') }}">
            <i class="ph-record-fill"></i> <span
                data-key="t-dashboards">الفروع </span>
        </a>
    </li>
    @endif
</ul>
