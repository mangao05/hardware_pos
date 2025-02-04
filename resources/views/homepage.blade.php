<!DOCTYPE html>
<!-- Coding by CodingNepal | www.codingnepalweb.com -->
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title> Pantukan </title>
    <link rel="stylesheet" href="{{ asset('css/homepage_view.css') }}">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.css') }}" />
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
    <!-- Bootstrap Select JS -->

    @yield('custom_css')
</head>

<style>
    nav-links,
    ol,
    ul {
        padding-left: 0px !important;
    }

    .home-section .home-content {
        position: relative;
        padding-top: 85px;
    }

    .button-success {
        background: rgb(246, 145, 27) !important;
        color: white
    }

    .sidebar {
        background: rgb(32, 80, 153) !important;
    }

    .modal-padding {
        padding: 5px;
    }

    .btn {
        padding: 5px !important;
        font-size: 14px !important;
    }

    .modal-color {
        background: rgb(8, 29, 69)
    }

    .form-control {
        padding: 5px !important;
    }

    label {
        font-weight: bold;
        font-size: 13px
    }

    .logo_name {
        margin-left: 11px;
    }
</style>

<body>
    <div class="sidebar {{ request()->is('booking') ? 'active' : '' }}">
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
                <span>Current Date and Time: {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}</span>
            </div>
            <div class="profile-details">
                <img src="https://media.licdn.com/dms/image/v2/C5603AQF1WA6mvPPN7g/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1655827862331?e=2147483647&v=beta&t=A0HGyBn7tNazpYnwQoiEMf4K_-fa9AZXAOLuQ-wXg0A"
                    alt="">
                <span
                    class="admin_name">{{ auth()->check() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : '' }}</span>
                <i class='bx bx-chevron-down'></i>
            </div>
        </nav>

        <div class="home-content">
            {{-- <div class="main-content"> --}}
            <div class="container-fluid">
                @yield('content')
            </div>


        </div>
    </section>

    <div class="modal fade d-block @if (\Hash::check('Pantukan@2025', auth()->user()->password)) show @endif" id="changePasswordModal"
        tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="changePasswordModalLabel">Change Password</h5>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> <!-- CSRF Token -->
                        <input type="hidden" name="userId" id="userId" value="{{ auth()->user()->id }}">
                        @method('PUT')
                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="newPassword"
                                    placeholder="Enter new password" required minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="newPassword">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                    placeholder="Confirm new password" required minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="confirmPassword">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        sidebarBtn.onclick = function() {
            sidebar.classList.toggle("active");
            if (sidebar.classList.contains("active")) {
                sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else
                sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('js')
    <script>
        $(document).ready(function() {

            $(".toggle-password").click(function() {
                let input = $("#" + $(this).data("target"));
                let icon = $(this).find("i");

                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                    icon.removeClass("bx-show").addClass("bx-hide");
                } else {
                    input.attr("type", "password");
                    icon.removeClass("bx-hide").addClass("bx-show");
                }
            });

            $("#changePasswordForm").submit(function(e) {
                e.preventDefault();

                let userId = $("#userId").val(); // Get the user ID dynamically
                let formData = $(this).serialize() +
                    "&_method=PUT";

                $.ajax({
                    url: `/users/${userId}`,
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.code === 200) {
                            toaster(response.message, "success");
                            setTimeout(() => $("#changePasswordModal").removeClass('show'),
                                1500);
                        } else {
                            toaster(response.message, "error");
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.message ||
                            "Error updating user.";
                        toaster(errorMsg, "error");
                    }
                });
            });

        });
    </script>
</body>

</html>
