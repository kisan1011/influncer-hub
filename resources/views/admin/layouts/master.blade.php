<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Head start --}}
    @include('admin.layouts.head')
    {{-- Head end --}}
    <style>
        /* Hide CKEditor licensing notifications */
        .cke_notifications_area,
        #cke_notifications_area_content,
        .cke_notification_warning {
            display: none !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- Header start --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            @include('admin.layouts.header')
        </nav>
        {{-- Header end --}}

        {{-- Sidebar start --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            @include('admin.layouts.sidebar')
        </aside>
        {{-- Sidebar end --}}

        {{-- Content start --}}
        <div class="content-wrapper">


            @yield('content')

        </div>
        {{-- Content end --}}

    </div>
    {{-- Footer start --}}
    @include('admin.layouts.footer')
    {{-- Footer end  --}}


</body>

</html>
