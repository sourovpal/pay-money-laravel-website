
<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
?>
	<!-- navbar -->
    <div class="navigation-wrap bg-white start-header start-style">
		<div class="container-fluid px-240">
			<div class="row">
				<div class="col-12">
					<nav class="navbar navbar-expand-md navbar-light">
                        @if(!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                            <a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                                <img src="{{ url('public/images/logos/'. settings('logo')) }}" alt="brand-logo">
                            </a>
                        @else
                            <a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                                <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}">
                            </a>
                        @endif

						<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
							<svg class="mt-n4p" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path class="path-white" fill-rule="evenodd" clip-rule="evenodd" d="M22 14.75C22 14.0597 21.4124 13.5 20.6875 13.5H12.8123C12.0874 13.5 11.4998 14.0597 11.4998 14.75C11.4998 15.4404 12.0874 16 12.8123 16H20.6875C21.4124 16 22 15.4404 22 14.75Z" fill="#403E5B"/>
								<path class="path-white" fill-rule="evenodd" clip-rule="evenodd" d="M22 8.00027C22 7.17183 21.403 6.50024 20.6666 6.50024H7.33307C6.59667 6.50024 5.99971 7.17183 5.99971 8.00027C5.99971 8.82871 6.59667 9.5003 7.33307 9.5003H20.6666C21.403 9.5003 22 8.82871 22 8.00027Z" fill="#403E5B"/>
								<path class="path-white" fill-rule="evenodd" clip-rule="evenodd" d="M22 1.25002C22 0.559654 21.3984 0 20.6562 0H1.84339C1.10124 0 0.499611 0.559654 0.499611 1.25002C0.499611 1.94039 1.10124 2.50005 1.84339 2.50005H20.6562C21.3984 2.50005 22 1.94039 22 1.25002Z" fill="#403E5B"/>
							</svg>
						</button>

						<div class="collapse navbar-collapse" id="navbarSupportedContent">
							<ul class="navbar-nav ml-auto py-4 py-md-0 OpenSans-600 nav-align">
                                <li class="nav-item <?= isset( $menu ) && ( $menu == 'home' ) ? 'nav-active': '' ?>">
                                    <a href="{{url('/')}}" class="nav-link">@lang('message.home.title-bar.home')</a>
                                </li>
                                <li class="nav-item <?= isset( $menu ) && ( $menu == 'send-money' ) ? 'nav-active': '' ?>">
                                    <a href="{{url('/send-money')}}" class="nav-link">@lang('message.home.title-bar.send')</a>
                                </li>
                                <li class="nav-item <?= isset( $menu ) && ( $menu == 'request-money' ) ? 'nav-active': '' ?>">
                                    <a href="{{url('/request-money')}}" class="nav-link">@lang('message.home.title-bar.request')</a>
                                </li>
                                @if(!empty($menusHeader))
                                    @foreach($menusHeader as $top_navbar)
                                        <li class="nav-item <?= isset( $menu ) && ( $menu == $top_navbar->url ) ? 'nav-active': '' ?>"><a href="{{url($top_navbar->url)}}" class="nav-link"> {{ $top_navbar->name }}</a></li>
                                    @endforeach

                                    @if(isActive('CryptoExchange') && cryptoValidity('guest_user'))
                                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'Crypto Exchange' ) ? 'nav-active': '' ?> "><a href="{{ route('guest.crypto_exchange.home') }}" class="nav-link"> {{ __('Crypto Exchange') }}</a></li>
                                    @endif
                                @endif

                                @if (request()->is('/'))
								<li>
									<div class="color-parent mt-2p">
										<div class="switch">
											<div id="switch">
												<img src="{{theme_asset('public/images/new-images/moon.png')}}" style="width:26px" class="moon img-none" alt="">
												<img src="{{theme_asset('public/images/new-images/sun2.png')}}" style="width:26px" class="img-none sun" alt="">
											</div>
										</div>
									</div>
								</li>
                                @endif

                                @if (!Auth::check())
                                    <div class="d-flex log-reg">
                                        <a href="{{ url('login') }}" class="border d-flex align-items-center cursor-pointer justify-content-center log-btn rounded ml-60 mt-n4p">
                                            @lang('message.home.title-bar.login')
                                        </a>

                                        <a href="{{ url('register') }}" class="border d-flex align-items-center justify-content-center cursor-pointer reg-btn rounded ml-18 mt-n4p">
                                            @lang('message.home.title-bar.register')
                                        </a>
                                    </div>
                                @else
                                    <div class="d-flex">
                                        <a href="{{ url('dashboard') }}" class="border d-flex align-items-center cursor-pointer justify-content-center log-btn rounded ml-60 mt-n4p">
                                            @lang('message.home.title-bar.dashboard')
                                        </a>

                                        <a href="{{ url('logout') }}" class="border d-flex align-items-center justify-content-center cursor-pointer reg-btn rounded ml-18 mt-n4p">
                                            @lang('message.home.title-bar.logout')
                                        </a>
                                    </div>
                                @endif
							</ul>
						</div>

					</nav>
				</div>
			</div>
		</div>
	</div>
