<!DOCTYPE html>
<html lang="en">
<head>
   @include('layouts.head')
</head>
<body>
    <div class="page-wrapper">
        @include('layouts.nav')
        @yield('content')
        @include('layouts.footer')
    </div>

    <!-- scroll-top -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

    @include('layouts.mobile-menu')
    @include('layouts.scripts')
</body>
</html> 