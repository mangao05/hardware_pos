<ul class="nav-links">
    <li>
      <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
        <i class='bx bx-grid-alt' ></i>
        <span class="links_name">Dashboard</span>
      </a>
    </li>
    <li>
        <a href="{{ url('/user-management') }}" class="{{ request()->is('user-management') ? 'active' : '' }}">
          <i class='bx bx-user' ></i>
          <span class="links_name">User Management</span>
        </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-box' ></i>
        <span class="links_name">Rooms Category</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-list-ul' ></i>
        <span class="links_name">Rooms</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-pie-chart-alt-2' ></i>
        <span class="links_name">Package</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-coin-stack' ></i>
        <span class="links_name">Leisures/Add-ons</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-message' ></i>
        <span class="links_name">Resto Tables</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-heart' ></i>
        <span class="links_name">Agents</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class='bx bx-cog' ></i>
        <span class="links_name">GoKart Cars</span>
      </a>
    </li>
    <li>
        <a href="#">
          <i class='bx bx-cog' ></i>
          <span class="links_name">Logs</span>
        </a>
    </li>
    <li class="log_out">
      <a href="#">
        <i class='bx bx-log-out'></i>
        <span class="links_name">Log out</span>
      </a>
    </li>
  </ul>