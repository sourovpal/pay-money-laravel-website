<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = settings('logo');
?>

<header id="js-header-old">
    <nav class="navbar navbar-expand-lg pb-3">
        <div class="container">
            @if (isset($logo))
                <a style="width: 100px;overflow: hidden;"  class="navbar-brand" href="{{url('/')}}">
                    <img src="{{theme_asset('public/images/logos/'.$logo)}}" alt="logo" class="img-fluid">
                </a>
            @else
                <a style="height: 45px;width: 157px;overflow: hidden;"  class="navbar-brand" href="{{url('/')}}">
                    <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="img-responsive" width="80" height="50">
                </a>
            @endif

            <button aria-label="navbar" class="navbar-toggler" data-toggle="modal" data-target="#left_modal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="d-none d-lg-block">
                <div class="row justify-content-end p-2">
                    <div>
                        @if(Auth::user()->picture)
                            <img src="{{url('public/user_dashboard/profile/' . Auth::user()->picture)}}" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                        @else
                            <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                        @endif
                        @php
                            $fullName = strlen($user->first_name . ' ' . $user->last_name) > 20 ? substr($user->first_name . ' ' . $user->last_name, 0, 20) . "..." : $user->first_name . ' ' . $user->last_name;
                        @endphp

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="subStringUserName" title="{{$user->first_name.' '.$user->last_name}}">{{$fullName}}</span></a>

                        <ul class="dropdown-menu" style="left: auto!important;">
                            @if(Common::has_permission(auth()->id(),'manage_setting'))
                                <li class="" style="padding: 5px;text-align: center;border-bottom: 1px solid #dae1e9">
                                    <i class="fa fa-cog"></i><a style="line-height: 0;color:#7d95b6" href="{{url('/profile')}}" class="btn btn-default btn-flat">@lang('message.dashboard.nav-menu.settings')</a>
                                </li>
                            @endif
                            <li class="" style="padding: 5px;text-align: center">
                                <i class="fa fa-sign-out"></i><a style="line-height: 0;color:#7d95b6" href="{{url('/logout')}}" class="btn btn-default btn-flat">@lang('message.dashboard.nav-menu.logout')</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal Window -->
    <div class="modal left fade" id="left_modal" tabindex="-1" role="dialog" aria-labelledby="left_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 m-nav-bg">
                    @if(Auth::check())
                        <div class="row justify-content-center">
                            <div>
                                @if(Auth::user()->picture)
                                    <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}"
                                        class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                                @else
                                    <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                                @endif

                            </div>

                            <div>
                                <p  class="text-white ml-1 mt-2"> {{ $fullName }}</p>
                            </div>
                        </div>
                    @endif

                    <button type="button" class="close text-28" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <ul class="mobile-side">
                        <li><a href="{{url('/dashboard')}}">@lang('message.dashboard.nav-menu.dashboard')</a></li>

                        @if(Common::has_permission(auth()->id(),'manage_transaction'))
                            <li><a href="{{url('/transactions')}}">@lang('message.dashboard.nav-menu.transactions')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_deposit'))
                            <li><a href="{{url('/deposit')}}">@lang('message.dashboard.button.deposit')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_transfer'))
                            <li><a href="{{url('/moneytransfer')}}">@lang('message.dashboard.nav-menu.send-req')</a></li>
                        @elseif(Common::has_permission(auth()->id(),'manage_request_payment'))
                            <li><a href="{{url('/request_payment/add')}}">@lang('message.dashboard.nav-menu.send-req')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_exchange'))
                            <li><a href="{{url('/exchange')}}">@lang('message.dashboard.nav-menu.exchange')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_merchant'))
                            <li><a href="{{url('/merchants')}}">@lang('message.dashboard.nav-menu.merchants')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_withdrawal'))
                            <li><a href="{{url('/payouts')}}">@lang('message.dashboard.nav-menu.payout')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_dispute'))
                            <li><a href="{{url('/disputes')}}">@lang('message.dashboard.nav-menu.disputes')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_ticket'))
                            <li><a href="{{url('/tickets')}}">@lang('message.dashboard.nav-menu.tickets')</a></li>
                        @endif

                        @if(Common::has_permission(auth()->id(),'manage_setting'))
                            <li><a href="{{url('/profile')}}">@lang('message.dashboard.nav-menu.settings')</a></li>
                        @endif

                        <li><a href="{{url('/logout')}}">@lang('message.dashboard.nav-menu.logout')</a></li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<!--Start Section-->
<section class="section-06 menu-bgcolor marginTopMinnus d-none d-lg-block">
    <div class="container">
        <div class="menu-list">
            <ul class="ml-0">
                <li class="pl-0 <?= isset($menu) && ($menu == 'dashboard') ? 'active' : '' ?>"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i>@lang('message.dashboard.nav-menu.dashboard')</a></li>

                @if(Common::has_permission(auth()->id(),'manage_transaction'))
                    <li class="<?= isset($menu) && ($menu == 'transactions') ? 'active' : '' ?>"><a href="{{url('/transactions')}}"><i class="fa fa-list"></i>@lang('message.dashboard.nav-menu.transactions')</a></li>
                @endif

                @if(Common::has_permission(auth()->id(),'manage_transfer'))
                    <li class="<?= isset($menu) && ($menu == 'send_receive') ? 'active' : '' ?>"><a href="{{url('/moneytransfer')}}"><i class="fa fa-exchange"></i>@lang('message.dashboard.nav-menu.send-req')</a></li>
                @elseif(Common::has_permission(auth()->id(),'manage_request_payment'))
                    <li class="<?= isset($menu) && ($menu == 'request_payment') ? 'active' : '' ?>">
                        <a href="{{url('/request_payment/add')}}"><i class="fa fa-exchange"></i>@lang('message.dashboard.nav-menu.send-req')</a>
                    </li>
                @endif

                @if(Common::has_permission(auth()->id(),'manage_merchant'))
                    <li class="<?= isset($menu) && ($menu == 'merchant') ? 'active' : '' ?>"><a
                                href="{{url('/merchants')}}"><i
                                    class="fa fa-user"></i>@lang('message.dashboard.nav-menu.merchants')</a></li>
                @endif
                @if(Common::has_permission(auth()->id(),'manage_dispute'))
                    <li class="<?= isset($menu) && ($menu == 'dispute') ? 'active' : '' ?>"><a
                                href="{{url('/disputes')}}"><i class="fa fa-ticket"></i>@lang('message.dashboard.nav-menu.disputes')</a></li>
                @endif
                @if(Common::has_permission(auth()->id(),'manage_ticket'))
                    <li class="<?= isset($menu) && ($menu == 'ticket') ? 'active' : '' ?>"><a
                                href="{{url('/tickets')}}"><i class="fa fa-spinner"></i>@lang('message.dashboard.nav-menu.tickets')</a></li>
                @endif
            </ul>
        </div>
    </div>
</section>
