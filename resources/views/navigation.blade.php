<ul class="nav-links">
    @php
        $userRoles = auth()->user()->roles()->pluck('role_id')->toArray();
    @endphp
    @if (in_array(12, $userRoles) || in_array(11, $userRoles) || in_array(2, $userRoles))
        <li>
            <a href="{{ route('cms.dashboard') }}" class="{{ request()->routeIs('cms.dashboard') ? 'active' : '' }}">
                <i class='bx bx-grid-alt'></i>
                <span class="links_name">Dashboard</span>
            </a>
        </li>
    @endif

    @if (in_array(12, $userRoles) || in_array(11, $userRoles))
        <li>
            <a href="{{ url('/user-management') }}" class="{{ request()->is('user-management') ? 'active' : '' }}">
                <i class='bx bx-user'></i>
                <span class="links_name">User Management</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/room-category') }}" class="{{ request()->is('room-category') ? 'active' : '' }}">
                <i class='bx bx-box'></i>
                <span class="links_name">Room Category</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/rooms') }}" class="{{ request()->is('rooms') ? 'active' : '' }}">
                <i class='bx bx-list-ul'></i>
                <span class="links_name">Rooms</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/package') }}" class="{{ request()->is('package') ? 'active' : '' }}">
                <i class='bx bx-pie-chart-alt-2'></i>
                <span class="links_name">Package</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/leisures-add-ons') }}" class="{{ request()->is('leisures-add-ons') ? 'active' : '' }}">
                <i class='bx bx-coin-stack'></i>
                <span class="links_name">Leisures/Add-ons</span>
            </a>
        </li>
    @endif

    @if (in_array(12, $userRoles) || in_array(2, $userRoles) || in_array(11, $userRoles))
        <li>
            <a href="{{ url('/booking') }}" class="{{ request()->is('booking') ? 'active' : '' }}">
                <i class='bx bx-heart'></i>
                <span class="links_name">Booking</span>
            </a>
        </li>
    @endif

    {{-- @if (in_array(12, $userRoles) || in_array(2, $userRoles) || in_array(11, $userRoles)) --}}
    <li>
        <a href="{{ url('/booking') }}" class="{{ request()->is('POS') ? 'active' : '' }}">
            <i class='bx bx-heart'></i>
            <span class="links_name">Point of Sale</span>
        </a>
    </li>
    {{-- @endif --}}

    <li class="log_out">
        <!-- Logout Form -->
        <form action="{{ route('auth.logout') }}" method="POST" id="logoutForm">
            @csrf
            @method('POST')
            <button type="submit" style="background: none; border: none; padding: 0;">
                <a href="#">
                    <i class='bx bx-log-out'></i>
                    <span class="links_name">Log out</span>
                </a>
            </button>
        </form>
    </li>
</ul>
