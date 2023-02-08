<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ isset($exceptionMeta) ? $exceptionMeta->description : meta(Route::current()->uri(), 'description') }}">
    <meta name="keywords" content="{{ isset($exceptionMeta) ? $exceptionMeta->keywords : meta(Route::current()->uri(), 'keywords') }}">
    <title>{{ isset($exceptionMeta) ? $exceptionMeta->title : meta(Route::current()->uri(), 'title') }}<?= isset($additionalTitle) ? ' | '.$additionalTitle : '' ?></title>

    @include('frontend.layouts.common.style')

    <!---title logo icon-->
    <link rel="javascript" href="{{theme_asset('public/frontend/js/respond.js')}}">

    <!---favicon-->
    @if (!empty(settings('favicon')))
        <link rel="shortcut icon" href="{{theme_asset('public/images/logos/'.settings('favicon'))}}" />
    @endif

    <script type="text/javascript">
        var SITE_URL = "{{url('/')}}";
    </script>
</head>

<body class="send-money request-page">
    <!-- Start scroll-top button -->
    <div id="scroll-top-area">
        <a href="{{url()->current()}}#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
    </div>
    <!-- End scroll-top button -->
    
    <!--Start Header-->
    @include('frontend.layouts.common.header')
    <!--End Header-->

    @yield('content')

    <!--Start Footer-->
    @include('frontend.layouts.common.footer_menu')
    <!--End Footer-->
    @include('frontend.layouts.common.script')

    @yield('js')
</body>