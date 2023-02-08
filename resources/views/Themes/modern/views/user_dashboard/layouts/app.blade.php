<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = settings('logo');
    $company_name = settings('name');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ meta(Route::current()->uri(), 'description') }}">
        <meta name="keywords" content="{{ meta(Route::current()->uri(), 'keywords') }}">
        <title>{{ meta(Route::current()->uri(), 'title') }}<?= isset($additionalTitle) ? ' | '.$additionalTitle : '' ?></title>
        <!--css styles-->
        @include('user_dashboard.layouts.common.style')
        <!---title logo icon-->
        <link rel="javascript" href="{{theme_asset('public/js/respond.js')}}">
        <!---favicon-->
        @if (!empty(settings('favicon')))
            <link rel="shortcut icon" href="{{theme_asset('public/images/logos/'.settings('favicon'))}}" />
        @endif

        <script type="text/javascript">
            const themeMode = localStorage.getItem('theme');
            if (themeMode === "dark") {
                document.documentElement.setAttribute('class', 'dark');
            }
            var SITE_URL = "{{url('/')}}";
            var FIATDP = "<?php echo number_format(0, preference('decimal_format_amount', 2)); ?>";
            var CRYPTODP = "<?php echo number_format(0, preference('decimal_format_amount_crypto', 8)); ?>";
        </script>
    </head>
    <body>
        <div id="scroll-top-area">
            <a href="{{url()->current()}}#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
        </div>

        <!-- Navbar section start -->
        <div>
            <nav class="navbar border-bottom py-2 nav-left navbar-toggleable-sm navbar-light bg-faded fixed-top">
                @include('user_dashboard.layouts.common.navbar')
            </nav>
        </div>
        <!-- Navbar section end -->
        <div class="container-fluid">
            <!-- Sidebar section start-->
            <div class="sidebar pl-1 min-vh-100 d-none  d-lg-block" id="sidecol">
                @include('user_dashboard.layouts.common.sidebar')
            </div>
            <!-- Sidebar section end -->

            <!-- Main content section start-->
                <div class="main-content">
                    @yield('content')
                    <!--Footer start-->
                    <footer>
                        <div class="p-3 text-center border-top">
                            <p class="copyright">{{ __('Copyright') }}&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
                        </div>
                    </footer>
                    <!--Footer end-->
                </div>
            <!-- Main content section end -->

            <!-- Delete Modal -->
            <div class="modal fade" id="delete-warning-modal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div style="display: block" class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{ __('Confirm Delete') }}</h4>
                        </div>

                        <div class="modal-body">
                            <p><strong>{{ __('Are you sure you want to delete this Data ?') }}</strong></p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-cancel" data-dismiss="modal">@lang('message.form.no')</button>
                            <a class="btn btn-danger" id="delete-modal-yes" href="javascript:void(0)">@lang('message.form.yes')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('user_dashboard.layouts.common.script')
        @yield('js')
    </body>
</html>
