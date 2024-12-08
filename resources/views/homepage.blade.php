<!DOCTYPE html>
<!-- Coding by CodingNepal | www.codingnepalweb.com -->
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Responsive Admin Dashboard | CodingLab </title>
    <link rel="stylesheet" href="{{ asset('css/homepage_view.css') }}">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
   </head>

<style>
  nav-links,ol, ul{
    padding-left: 0px !important;
  }

  .home-section .home-content {
    position: relative;
    padding-top: 85px;
  }

  .button-success{
    background: rgb(246,145,27) !important;
    color: white
  }

  .sidebar{
    background: rgb(32, 80, 153) !important;
  }

  .modal-padding{
    padding: 5px;
  }

  .btn {
    padding: 5px !important;
    font-size: 14px !important;
  }

  .modal-color{
    background:rgb(8,29,69)
  }

  .form-control{
    padding: 5px !important;
  }

  label{
    font-weight: bold;
    font-size: 13px
  }
</style>
<body>
  <div class="sidebar">
    <div class="logo-details">
      <img src="{{ asset('img/pantukan_logo.png') }}" style="width: 50px;" alt="">
      <span class="logo_name">Pantukan</span>
    </div>
          {{-- navigation  --}}
      @include('navigation')
      
      
  </div>
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class='bx bx-menu sidebarBtn'></i>
        <span class="dashboard">
          @yield('header')
        </span>
      </div>
      <div class="search-box">
        <span>Current Date and Time:December 07, 2024 6:05 PM</span>
      </div>
      <div class="profile-details">
        <img src="https://media.licdn.com/dms/image/v2/C5603AQF1WA6mvPPN7g/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1655827862331?e=2147483647&v=beta&t=A0HGyBn7tNazpYnwQoiEMf4K_-fa9AZXAOLuQ-wXg0A" alt="">
        <span class="admin_name">Khenneth Alaiza</span>
        <i class='bx bx-chevron-down' ></i>
      </div>
    </nav>

    <div class="home-content">
      <div class="main-content">
        @yield('content')
      </div>
      
      
    </div>
  </section>
  
  <script>
   let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  if(sidebar.classList.contains("active")){
  sidebarBtn.classList.replace("bx-menu" ,"bx-menu-alt-right");
}else
  sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
}
 </script>

</body>
</html>