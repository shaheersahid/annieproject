<meta charset="utf-8" />
<title>{{ View::yieldContent('page-title') . ' | ' . config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Signature By RaiMal's Administration Dashboard" name="description" />
<meta content="Signature By RaiMal's" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- App favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/icons/favicon-16x16.png') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/icons/favicon.ico') }}">

<!-- Bootstrap Css -->
<link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

<!-- Icons Css -->
<link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Simplebar Css -->
<link href="{{ asset('admin/assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet" />

<!-- App Css -->
<link href="{{ asset('admin/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<!-- Custom Css -->
<link href="{{ asset('admin/assets/css/custom.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<!-- Brand Theme CSS - Purple & Black -->
<link href="{{ asset('admin/assets/css/brand-theme.css') }}" rel="stylesheet" type="text/css" />

<!-- Additional Styles -->
@stack('admin-styles')

<!-- Sweet Alert-->
<link rel="stylesheet" href="{{ asset('admin/assets/libs/sweetalert2/sweetalert2.min.css') }}">

<!-- Toastr CSS -->
<link rel="stylesheet" href="{{ asset('admin/assets/libs/toastr/build/toastr.min.css') }}">

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

