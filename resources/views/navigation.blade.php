<ul class="nav-links">
    @php
        $userRoles = auth()->user()->roles()->pluck('role_id')->toArray();
    @endphp

    @php
        $routes = [
            [
                'url' => '/dashboard',
                'name' => 'cms.dashboard',
                'label' => 'Dashboard',
                'icon' => 'bx bx-grid-alt',
                'roles' => [12, 2, 11],
            ],
            [
                'url' => '/user-management',
                'name' => 'user-management',
                'label' => 'User Management',
                'roles' => [12, 11],
                'icon' => 'bx bx-user',
            ],
            [
                'url' => '/room-category',
                'name' => 'room-category',
                'label' => 'Room Category',
                'icon' => 'bx bx-box',
                'roles' => [12, 11],
            ],
            [
                'url' => '/rooms',
                'name' => 'rooms',
                'label' => 'Rooms',
                'icon' => 'bx bx-list-ul',
                'roles' => [12, 11],
            ],
            [
                'url' => '/package',
                'name' => 'package',
                'label' => 'Package',
                'icon' => 'bx bx-pie-chart-alt-2',
                'roles' => [12, 11],
            ],
            [
                'url' => '/leisures-add-ons',
                'name' => 'leisures-add-ons',
                'label' => 'Leisures/Add-ons',
                'icon' => 'bx bx-coin-stack',
                'roles' => [12, 11],
            ],
            [
                'url' => '/booking',
                'name' => 'booking',
                'label' => 'Booking',
                'icon' => 'bx bx-heart',
                'roles' => [12, 2, 11],
            ],
            [
                'url' => '/food-categories',
                'name' => 'food-categories',
                'label' => 'Food Categories',
                'icon' => 'bx bx-food-menu',
                'roles' => [12, 11],
            ],
            [
                'url' => '/foods',
                'name' => 'foods',
                'label' => 'Foods',
                'icon' => 'bx bx-food-menu',
                'roles' => [12, 11],
            ],
            [
                'url' => '/pos',
                'name' => 'POS',
                'label' => 'Point of Sale',
                'icon' => 'bx bx-calculator',
                'roles' => [],
            ],
        ];
    @endphp

    @foreach ($routes as $route)
        @if (collect($userRoles)->intersect($route['roles'])->isNotEmpty() || collect($route['roles'])->isEmpty())
            <li>
                <a href="{{ url($route['url']) }}" class="{{ request()->is(ltrim($route['url'], '/')) ? 'active' : '' }}">
                    <i class='{{ $route['icon'] }}'></i>
                    <span class="links_name">{{ $route['label'] }}</span>
                </a>
            </li>
        @endif
    @endforeach


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
