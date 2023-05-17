<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
    <li class="nav-item">
        <a href="{{route('customer.dashboard')}}" class="nav-link {{isSidebarActive('customer.dashboard')}}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
                {{trans('customer.dashboard')}}
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('customer.contacts.index')}}" class="nav-link {{isSidebarActive('customer.contacts*')}}">
            <i class="nav-icon fas fa-phone-alt"></i>
            <p>
                {{trans('customer.contacts')}}
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('customer.groups.index')}}" class="nav-link {{isSidebarActive('customer.groups*')}}">
            <i class="nav-icon fas fa-users"></i>
            <p>
                {{trans('customer.groups')}}
            </p>
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{route('customer.keywords.index')}}" class="nav-link {{isSidebarActive('customer.keywords*')}}">
            <i class="nav-icon fas fa-file-word"></i>
            <p>
                {{trans('customer.keywords')}}
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('customer.smsbox.inbox')}}" class="nav-link {{isSidebarActive('customer.smsbox*')}}">
            <i class="nav-icon fas fa-envelope"></i>
            <p>
                {{trans('customer.smsbox')}}
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('customer.numbers.phone_numbers')}}" class="nav-link {{isSidebarActive('customer.numbers*')}}">
            <i class="nav-icon fas fa-mobile-alt"></i>
            <p>
                {{trans('customer.phone_number')}}
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('customer.billing.index')}}" class="nav-link {{isSidebarActive('customer.billing*')}}">
            <i class="nav-icon fas fa-file-invoice-dollar"></i>
            <p>
                {{trans('customer.billing')}}
            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('customer.settings.index')}}" class="nav-link {{isSidebarActive('customer.settings*')}}">
            <i class="nav-icon fas fa-cog"></i>
            <p>
                {{trans('customer.settings')}}
            </p>
        </a>
    </li>


</ul>
