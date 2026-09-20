<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-theme="light"
    data-menu-color="dark" data-topbar-color="light" data-layout-mode="fluid" data-layout-position="fixed"
    data-sidenav-size="default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#288479">
    <title>Dream Travel and Tours</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/dream-logo.png') }}">

    <!-- Vector Map css -->
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}">

    <!-- Datatables css -->
    <link href="{{ asset('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/daterangepicker/daterangepicker.css') }}">

    <!-- Bootstrap Datepicker css -->
    <link href="{{ asset('assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Select2 css -->
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/hyper-config.js') }}"></script>

    <!-- App css -->
    <link href="{{ asset('assets/css/app-saas.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Quill css -->
    <link href="{{ asset('assets/vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />

    <!-- Hightchart js -->
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <!-- optional -->
    <script src="https://code.highcharts.com/modules/offline-exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Ubuntu" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    @livewireStyles

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <!-- Scripts -->
    
    <link href="{{ asset('assets/css/brand.css') }}" rel="stylesheet" type="text/css" />

</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .toast-success {
        background-color: #51a351 !important;
    }

    .toast-error {
        background-color: #bd362f !important;
    }

    /* seat styles */
    .seat {
        margin: 4px;
        background-color: var(--brand-accent-deep);
        border-radius: 4px;
        border: 1px solid #fff;
        overflow: hidden;
        float: left;
    }

    #seat-label {
        float: left;
        line-height: 2.1em;
        width: 5.0em;
        height: 2.5em;
    }

    .seat-label-new {
        float: left;
        line-height: 2.1em;
        width: 5.0em;
        height: 2.5em;
    }

    .seat label span {
        text-align: center;
        padding: 3px 0;
        display: block;
    }

    .seat label input {
        position: absolute;
        display: none;
        color: #fff !important;
    }

    .seat label input+span {
        color: #fff;
    }

    .seat input:checked+span {
        color: #ffffff;
        text-shadow: 0 0 6px rgba(0, 0, 0, 0.8);
    }

    .seat input:disabled+span {
        color: #ffffff;
        text-shadow: 0 0 6px rgba(0, 0, 0, 0.8);
    }

    .seat input:checked+span {
        background-color: var(--brand-secondary);
        height: 37px;
    }

    .seat input:disabled+span {
        background-color: var(--brand-secondary);
        height: 37px;
    }

    /* .seat input:disabled+span {
        background-color: var(--brand-secondary);
        height: 37px;
    } */

    .seat > .booked_seat_class+span {
        background-color: #047a39;
        height: 37px;
    }

    .seat > .reserved_seat_class+span {
        background-color: var(--brand-secondary);
        height: 37px;
    }

    /* seat styles */
</style>

<body>

    <!-- Pre-loader -->
    <div id="preloader">
        <div id="status">
            <div class="bouncing-loader">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <!-- End Preloader-->

    <div id="app">
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Topbar Start ========== -->
            <x-system.top-nav-bar />
            <!-- ========== Topbar End ========== -->

            <!-- ========== Left Sidebar Start ========== -->
            <x-system.side-bar-menu />
            <!-- ========== Left Sidebar End ========== -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                    <!-- Start Content-->
                    <div class="container-fluid">
                        @yield('content')
                        <!-- end row -->
                    </div>
                    <!-- container -->

                </div>
                <!-- content -->

                <!-- Footer Start -->
                <x-system.page-footer />
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    @yield('script')
    @livewireScripts

    <script>
        @if (Session::has('message'))
            //Toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ Session::get('message') }}");
        @endif
    </script>

    <script type="text/javascript">
        // PAGE LOADING
        $(window).on("load", function(e) {
            $("#global-loader").fadeOut("slow");
        })

        //LOGOUT LOGIC
        $('document').ready(function() {
            $('#logoutButton').click(function(e) {
                e.preventDefault();

                $('#logoutButton').html("Please wait...");
                $('#logoutButton').prop("disabled", true);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('logout') }}",
                    method: 'POST',
                    data: {},
                    success: function(result) {
                        if (result.status === true) {
                            //redirect user to login page
                            window.location.href = "/";
                        } else {
                            $('#logoutButton').html("Logout");
                            $('#logoutButton').prop("disabled", false);
                        }
                    },
                    error: function(error) {

                    }
                });
            });
        });
    </script>

    <!-- Datatables js -->
    <script src="{{ asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-select/js/dataTables.select.min.js') }}"></script>



    <!-- Datatable Demo Aapp js -->
    <script src="{{ asset('assets/js/pages/demo.datatable-init.js') }}"></script>

    <!--  Select2 Js -->
    <script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>

    <!--  Toastr Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Daterangepicker js -->
    <script src="{{ asset('assets/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Bootstrap Datepicker js -->
    <script src="{{ asset('assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- quill js -->
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <!-- quill Init js-->
    <script src="{{ asset('assets/js/pages/demo.quilljs.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    @yield('script')

    @include('scripts.zanzi')

    <script>
        document.addEventListener('click', function(event) {
            const toggle = event.target.closest('[data-password-toggle]');
            if (!toggle) {
                return;
            }

            const input = toggle.closest('.input-group')?.querySelector('input') ||
                document.getElementById(toggle.getAttribute('data-password-toggle'));
            const icon = toggle.querySelector('i');
            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            icon.classList.toggle('mdi-eye-outline', !isHidden);
            icon.classList.toggle('mdi-eye-off-outline', isHidden);
        });
    </script>

    <script>
        @if (Session::has('success'))
            toastr.options = {
                "closeButton": true,
                "progressBar": false,
                "positionClass": "toast-top-right",
            }
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
            }
            toastr.error("{{ Session::get('error') }}");
        @endif
    </script>

</body>

</html>
