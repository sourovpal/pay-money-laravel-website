<?php
	$user = Auth::user();
	$socialList = getSocialLink();
	$menusHeader = getMenuContent('Header');
	$logo = session('company_logo');
	$company_name = settings('name');
	$socialList = getSocialLink();
	$menusFooter = getMenuContent('Footer');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<meta name="csrf-token" content="{{ csrf_token() }}"><!-- for ajax -->

		<meta name="description" content="{{ meta(Route::current()->uri(), 'description') }}">
        <meta name="keywords" content="{{ meta(Route::current()->uri(), 'keywords') }}">
        <title>{{ meta(Route::current()->uri(), 'title') }}<?= isset($additionalTitle) ? ' | '.$additionalTitle : '' ?></title>

		<!--css styles-->
		<link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/bootstrap.min.css')}}">
		<link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/themify-icons.css')}}">
		<link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/prereset.css')}}">
		<link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/customstyle.css')}}">
		<link rel="stylesheet" type="text/css"  href="{{theme_asset('public/css/responsive.css')}}">
		<link rel="stylesheet" type="text/css"  href="{{theme_asset('public/css/animate.min.css')}}">
		<link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/fontawesome/css/all.min.css')}}">
		<link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/sweetalert/sweetalert.css')}}">

		<!-- iCheck -->
		<link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/iCheck/square/blue.css') }}">

		<!---title logo icon-->
		<link rel="javascript" href="{{theme_asset('public/js/respond.js')}}">

		<!---favicon-->
		@if (!empty(settings('favicon')))
			<link rel="shortcut icon" href="{{theme_asset('public/images/logos/'.settings('favicon'))}}" />
		@endif

		<!-- must include below if using auth middleware, or, creates problem in ajax token -->
		<script>
			window.Laravel = {!! json_encode([
				'csrfToken' => csrf_token(),
			]) !!};
		</script>
		<!--/-->

		<script type="text/javascript">
			var SITE_URL = "{{url('/')}}";
		</script>
	</head>
    <body>
		<div id="scroll-top-area">
			<a href="{{url()->current()}}#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
		</div>

        <!-- Start Preloader -->
        <div class="preloader">
            <div class="preloader-img"></div>
        </div>
        <!-- End Preloader -->

		<header id="js-header-old">
			<nav class="navbar navbar-expand-lg">
				<div class="container">
					@if($logo)
						<a style="width: 205px;overflow: hidden;"  class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
							<img src="{{theme_asset('public/images/logos/'.$logo)}}" alt="logo" class="img-fluid">
						</a>
					@else
						<a style="width: 157px;overflow: hidden;"  class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
							<img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="img-responsive" width="80" height="50">
						</a>
					@endif
		
					@if (request()->path() != 'merchant/payment')
						<button aria-label="navbar" class="navbar-toggler bg-white text-28" data-toggle="modal" data-target="#left_modal">
							<i class="fas fa-bars"></i>
						</button>
		
						<div class="collapse navbar-collapse navbar-toggler-right" id="navbarSupportedContent">
							<ul class="navbar-nav  my-navbar">
								<li class="nav-item <?= isset( $menu ) && ( $menu == 'home' ) ? 'nav_active': '' ?>"><a href="{{url('/')}}" class="nav-link">@lang('message.home.title-bar.home')</a></li>
								<li class="nav-item <?= isset( $menu ) && ( $menu == 'send-money' ) ? 'nav_active': '' ?>"><a href="{{url('/send-money')}}" class="nav-link">@lang('message.home.title-bar.send')</a></li>
								<li class="nav-item <?= isset( $menu ) && ( $menu == 'request-money' ) ? 'nav_active': '' ?>"><a href="{{url('/request-money')}}" class="nav-link">@lang('message.home.title-bar.request')</a></li>
							 @if(!empty($menusHeader))
								@foreach($menusHeader as $top_navbar)
									<li class="nav-item <?= isset( $menu ) && ( $menu == $top_navbar->url ) ? 'nav_active': '' ?>"><a href="{{url($top_navbar->url)}}" class="nav-link"> {{ $top_navbar->name }}</a></li>
								@endforeach
							@endif
								@if( !Auth::check() )
									<li class="nav-item auth-menu"> <a href="{{url('/login')}}" class="nav-link">@lang('message.home.title-bar.login')</a></li>
									<li class="nav-item auth-menu"> <a href="{{url('/register')}}" class="nav-link">@lang('message.home.title-bar.register')</a></li>
								@else
									<li class="nav-item auth-menu"> <a href="{{url('/dashboard')}}" class="nav-link">@lang('message.home.title-bar.dashboard')</a> </li>
									<li class="nav-item auth-menu"> <a href="{{url('/logout')}}" class="nav-link">@lang('message.home.title-bar.logout')</a> </li>
								@endif
							</ul>
						</div>
		
					@endif
		
					<div id="quick-contact" class="collapse navbar-collapse">
						<ul class="ml-auto">
							@if( !Auth::check())
								@if (request()->path() == 'merchant/payment')
									{{-- @php
										$grandId = $_GET['grant_id'];
										$urlToken = $_GET['token'];
									@endphp
									<li> <a href="{{ url("merchant/payment?grant_id=$grandId&token=$urlToken") }}">@lang('message.home.title-bar.login')</a> </li> --}}
								@else
									<li> <a href="{{url('/login')}}">@lang('message.home.title-bar.login')</a> </li>
									<li> <a href="{{url('/register')}}">@lang('message.home.title-bar.register')</a> </li>
								@endif
							@else
								<li><a href="{{url('/dashboard')}}">@lang('message.home.title-bar.dashboard')</a> </li>
								<li><a href="{{url('/logout')}}">@lang('message.home.title-bar.logout')</a> </li>
							@endif
						</ul>
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
										@php
											$fullName = strlen($user->first_name.' '.$user->last_name) > 20 ? substr($user->first_name.' '.$user->last_name,0,20)."..." : $user->first_name.' '.$user->last_name; //change in pm_v2.1
										@endphp
										<p  class="text-white ml-1 mt-2"> {{ $fullName }}</p>
									</div>
								</div>
							@else
								<div>
									@if($logo)
									<a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
										<img src="{{theme_asset('public/images/logos/'.$logo)}}" alt="logo" class="logo">
									</a>
									@else
										<a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
											<img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="logo">
										</a>
									@endif
		
								</div>
							@endif
		
							<button type="button" class="close text-28" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
		
						<div class="modal-body">
							<ul class="mobile-side">
								<li><a href="{{url('/')}}">@lang('message.home.title-bar.home')</a></li>
								<li><a href="{{url('/send-money')}}">@lang('message.home.title-bar.send')</a></li>
								<li><a href="{{url('/request-money')}}">@lang('message.home.title-bar.request')</a></li>
							 @if(!empty($menusHeader))
								@foreach($menusHeader as $top_navbar)
									<li><a href="{{url($top_navbar->url)}}"> {{ $top_navbar->name }}</a></li>
								@endforeach
							@endif
								@if( !Auth::check() )
									<li> <a href="{{url('/login')}}">@lang('message.home.title-bar.login')</a></li>
									<li> <a href="{{url('/register')}}">@lang('message.home.title-bar.register')</a></li>
								@else
									<li> <a href="{{url('/dashboard')}}">@lang('message.home.title-bar.dashboard')</a> </li>
									<li> <a href="{{url('/logout')}}">@lang('message.home.title-bar.logout')</a> </li>
								@endif
							</ul>
						</div>
					</div>
				</div>
			</div>
		</header>

        <!--section_google2fa-->
		<section class="section-01 sign-up padding-30" id="section_google2fa">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-7 mx-auto">
								<div class="card rounded-0">
									<div class="card-header">
										<h4 class="mb-0">@lang('message.google2fa.title-text')</h4>
									</div>

									<div class="card-body">
										<div style="text-align: center;">
											<h4>@lang('message.google2fa.subheader-text')</h4>
											@php
												$data = Session::get('data');
											@endphp
											<div>
												<img src="{{ $data['QR_Image'] }}" class="qr-image">
											</div>

											<h5>@lang('message.google2fa.setup-a')</h5>
											<br>
											<h5>@lang('message.google2fa.setup-b')</h5>
											<br>
											<button class="btn btn-primary completeVerification">@lang('message.google2fa.proceed')</button>
										</div>
									</div>
									<!--/card-block-->
								</div>
							</div>
						</div>
						<!--/row-->
					</div>
					<!--/col-->
				</div>
				<!--/row-->
			</div>
		</section>


		<!--section_2fa_otp-->
		<section class="section-01 sign-up padding-30" id="section_2fa_otp" style="display: none;">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-7 mx-auto">
								<div class="card rounded-0">
									<div class="card-header">
										<h3 class="mb-0">@lang('message.google2fa.otp-title-text')</h3>
									</div>

									<div class="card-body">
										<div class="row">
											<form class="form-horizontal" method="POST" id="otp_form">

												<input type="hidden" name="two_step_verification_type" id="two_step_verification_type" value="{{ auth()->user()->user_detail->two_step_verification_type }}">

												<div class="form-group {{ $errors->has('one_time_password') ? ' has-error' : '' }}">
													<label for="one_time_password" class="col-md-12 control-label"><h3>@lang('message.google2fa.otp-input')</h3></label>

													<div class="col-md-12">
														<input id="one_time_password" type="number" maxlength="6" class="form-control" name="one_time_password"
														oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required autofocus>
														@if ($errors->has('one_time_password'))
															<span class="error">
																<strong>{{ $errors->first('one_time_password') }}</strong>
															</span>
														@endif
													</div>
												</div>

												<div class="form-group">
													<div class="checkbox icheck" style="margin-left: 15px;">
														<label>
															<input type="checkbox" name="remember_otp" id="remember_otp">
															<span style="font-size: 16px; font-weight: 600; color: #181818;">&nbsp;&nbsp;&nbsp;@lang('message.2sa.remember-me-checkbox')</span>
														</label>
													</div>
												</div>
												<br>
												<br>

												<div class="form-group">
													<div class="col-md-6 col-md-offset-6">
														<button type="submit" class="btn btn-primary" id="verify_otp">@lang('message.2sa.verify')</button>
													</div>
												</div>
											</form>
										</div>
										<div class="clearfix"></div>
									</div>
									<!--/card-block-->
								</div>
							</div>
						</div>
						<!--/row-->
					</div>
					<!--/col-->
				</div>
				<!--/row-->
			</div>
		</section>

		
		<section class="bg-image footer text-white mt-5">
			<div class="bg-dark">
				<div class="container pt-60 pb-3">
					<div class="row  justify-content-between">
						<div class="col-xl-5">
							<div>
								<img src="{{theme_asset('public/images/logos/'.$logo)}}" class="mw-200" alt="logo">
							</div>
		
							<p class="mt-3">{{ __('Paymoney, a secured online payment gateway that allows payment in multiple currencies easily, safely and securely.') }}</p>
						</div>
		
						<div class="col-xl-7 pt-3">
							<div class="d-flex flex-wrap justify-content-between">
								<div>
									<h4 style="color: white;">{{ __('Quick Links') }}</h4>
									<ul class="link mt-3">
										<li class="mt-2"><a href="{{ url('/') }}" class="text-white">@lang('message.home.title-bar.home')</a></li>
										<li class="mt-2"><a href="{{ url('/send-money') }}" class="text-white">@lang('message.home.title-bar.send')</a></li>
										<li class="mt-2"><a href="{{ url('/request-money') }}" class="text-white">@lang('message.home.title-bar.request')</a></li>
										<li class="mt-2"><a href="{{ url('/developer') }}" class="text-white">@lang('message.home.title-bar.developer')</a></li>
									</ul>
								</div>
		
								<div>
									<ul class="link mt-4 pt-1">
										@if(!empty($menusFooter))
											@foreach($menusFooter as $footer_navbar)
												<li class="mt-2"><a href="{{url($footer_navbar->url)}}" class="text-white">{{ $footer_navbar->name }}</a></li>
											@endforeach
										@endif
									</ul>
								</div>
							</div>
						</div>
					</div>
		
					<div class="d-flex flex-wrap justify-content-between">
						<div>
							<div class="d-flex justify-content-center">
								<div class="d-flex flex-wrap social-icons mt-5">
									@foreach($socialList as $social)
										@if (!empty($social->url))
											<div class="p-2">
												<a href="{{ $social->url }}">{!! $social->icon !!}</a>
											</div>
										@endif
									@endforeach
								</div>
							</div>
						</div>
		
						<div>
							<div class="d-flex justify-content-center pt-4">
		
								@foreach(getAppStoreLinkFrontEnd() as $app)
									@if (!empty($app->logo))
										<div class="p-2 pl-4 pr-4">
											<a href="{{$app->link}}"><img src="{{url('public/uploads/app-store-logos/'.$app->logo)}}" class="img-responsive" width="125" height="50"/></a>
										</div>
									@else
										<div class="p-2 pl-4 pr-4">
											<a href="#"><img src='{{ url('public/uploads/app-store-logos/default-logo.jpg') }}' class="img-responsive" width="120" height="90"/></a>
										</div>
									@endif
								@endforeach
							</div>
						</div>
					</div>
		
					<hr class="mb-2">
					<div class="d-flex justify-content-between">
						<div>
							<?php
								$company_name = settings('name');
							?>
							<p class="copyright mt-0">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
						</div>
		
						<div>
							<div class="container-select d-flex">
								<div>
									<i class="fa fa-globe"></i>
								</div>
		
								<div>
									<select class="select-custom mt-0" id="lang">
										@foreach (getLanguagesListAtFooterFrontEnd() as $lang)
										<option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }} value='{{ $lang->short_name }}'> {{ $lang->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
		
		
				</div>
			</div>
		</section>

    <!--javascript-->
	<script src="{{theme_asset('public/js/jquery.min.js')}}" type="text/javascript"></script>

    <!--bootstrap-->
	<script src="{{theme_asset('public/js/bootstrap.min.js')}}" type="text/javascript"></script>

    <!--jquery waypoints-->
	<script src="{{theme_asset('public/js/jquery.waypoints.min.js')}}" type="text/javascript"></script>

    <!--main-->
	<script src="{{theme_asset('public/js/main.js')}}" type="text/javascript"></script>

	<!-- iCheck -->
	<script src="{{ theme_asset('public/css/iCheck/icheck.min.js') }}" type="text/javascript"></script>

	<!-- sweetalert -->
	<script src="{{ theme_asset('public/css/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

	<!-- fingerprint2 -->
	<script src="{{ theme_asset('public/js/fpjs2/fpjs2.js') }}" type="text/javascript"></script>


	<script type="text/javascript">

		//extra
		    $(function () {
		        $('[data-toggle="tooltip"]').tooltip();
		    });

		    function resizeHeaderOnScroll() {
		        const distanceY = window.pageYOffset || document.documentElement.scrollTop,
		            shrinkOn = 100,
		            headerEl = document.getElementById('js-header');
		        if (headerEl) {
		            if (distanceY > shrinkOn) {
		                headerEl.classList.add("smaller-header");
		                $("#logo_container").attr('src', SITE_URL + '/public/frontend/images/logo_sm.png');
		            } else {
		                headerEl.classList.remove("smaller-header");
		                $("#logo_container").attr('src', SITE_URL + '/public/frontend/images/logo.png');
		            }
		        }
		    }
		    window.addEventListener('scroll', resizeHeaderOnScroll);

		    //Language script
		    $('#lang').on('change', function (e) {
		        e.preventDefault();
		        lang = $(this).val();
		        url = '{{url('change-lang')}}';
		        $.ajax({
		            type: 'get',
		            url: url,
		            async: false,
		            data: {lang: lang},
		            success: function (msg)
		            {
		                if (msg == 1)
		                {
		                    location.reload();
		                }
		            }
		        });
		    });
	    //extra

    	//verifying script - start
	    $(function () {
		    $('input').iCheck({
		        checkboxClass: 'icheckbox_square-blue',
		        radioClass: 'iradio_square-blue',
		        increaseArea: '20%' // optional
		    });
		});

		//verifying on submit
		$('#2sa_form').submit(function(event)
		{
		    event.preventDefault();

		    var token = '{{csrf_token()}}';
		    var two_step_verification_code = $("#two_step_verification_code").val();
		    var remember_me = $("#remember_me").is(':checked');

			//Fingerprint2
			new Fingerprint2().get(function(result, components)
			{
			   $.ajax({
		        method: "POST",
		        url: SITE_URL + "/2fa/verify",
		        cache: false,
		        dataType:'json',
		        data: {
			            "_token": token,
			     		'two_step_verification_code': two_step_verification_code,
			            'remember_me': remember_me,
			            'browser_fingerprint': result,
		        	}
			    })
			    .done(function(data)
			    {
			       	if (data.status == false || data.status == 404) {
			        	//failure
			            $('#message').css('display', 'block');
			            $('#message').html(data.message);
			            $('#message').addClass(data.error);
			        } else {
			        	console.log('verified');
			        	//success
			            $('#message').removeClass('alert-danger');
			            $('#message').hide();
			            window.location.href="{{ url('dashboard') }}";
			        }
			    });
			});
		});

		//google 2fa on submit
	    $(document).on('click', '.completeVerification', function(e)
	    {
	        e.preventDefault();
	        var google2fa_secret = '{{ $data['secret'] }}';

	        $.ajax({
	            headers:
	            {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            method: "POST",
	            url: SITE_URL+"/google2fa/verify",
	            dataType: "json",
	            cache: false,
	            data: {
	                'google2fa_secret': google2fa_secret,
	            }
	        })
	        .done(function(response)
	        {
	            if (response.status == true) {
	                $('#section_google2fa').hide();
	                $('#section_2fa_otp').show();
	            }
	        });
	    });

		//verifying OTP on submit
	    $('#otp_form').submit(function(event)
	    {
	        event.preventDefault();

	        var token = '{{csrf_token()}}';
	        var one_time_password = $("#one_time_password").val();
	        var two_step_verification_type = $('#two_step_verification_type').val();
	        var remember_otp = $("#remember_otp").is(':checked');

	        new Fingerprint2().get(function(result, components)
	        {
	            $.ajax({
	                method: "POST",
	                url: SITE_URL + "/google2fa/verifyGoogle2faOtp",
	                cache: false,
	                dataType:'json',
	                data: {
	                    "_token": token,
	                    'one_time_password': one_time_password,
	                    'two_step_verification_type': two_step_verification_type,
	                    'remember_otp': remember_otp,
	                    'browser_fingerprint': result,
	                },
	                error:function(msg)
	                {
	                	console.log(msg);
	                    if (msg.status!=200) {
		                    swal({
		                            title: "Error",
		                            text: JSON.parse(msg.responseText).message,
		                            type: "error"
		                        }
		                    );
	                   }
	                }
	            })
	            .done(function(data)
	            {
	            	if (data.status == false) {
						swal({
							title: "Error",
							text: 'Verification is code is invalid.',
							type: "error"
						});
					} else {
	                	window.location.href="{{ url('dashboard') }}";
					}
	            });
	        });
	    });




		

// custom dropdown

function create_custom_dropdowns() {
$('.select-custom').each(function(i, select) {
	if (!$(this).next().hasClass('dropdown')) {
	$(this).after('<div class="dropdown ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
	var dropdown = $(this).next();
	var options = $(select).find('option');
	var selected = $(this).find('option:selected');
	dropdown.find('.current').html(selected.data('display-text') || selected.text());
	options.each(function(j, o) {
		var display = $(o).data('display-text') || '';
		dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
	});
	}
});
}

// Event listeners

// Open/close
$(document).on('click', '.dropdown', function(event) {
$('.dropdown').not($(this)).removeClass('open');
$(this).toggleClass('open');
if ($(this).hasClass('open')) {
	$(this).find('.option').attr('tabindex', 0);
	$(this).find('.selected').focus();
} else {
	$(this).find('.option').removeAttr('tabindex');
	$(this).focus();
}
});
// Close when clicking outside
$(document).on('click', function(event) {
    if ($(event.target).closest('.dropdown').length === 0) {
        $('.dropdown').removeClass('open');
        $('.dropdown .option').removeAttr('tabindex');
    }
    event.stopPropagation();
});
// Option click
$(document).on('click', '.dropdown .option', function(event) {
    $(this).closest('.list').find('.selected').removeClass('selected');
    $(this).addClass('selected');
    var text = $(this).data('display-text') || $(this).text();
    $(this).closest('.dropdown').find('.current').text(text);
    $(this).closest('.dropdown').prev('select').val($(this).data('value')).trigger('change');
});

// Keyboard events
$(document).on('keydown', '.dropdown', function(event) {
var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
// Space or Enter
if (event.keyCode == 32 || event.keyCode == 13) {
	if ($(this).hasClass('open')) {
	    focused_option.trigger('click');
	} else {
	    $(this).trigger('click');
	}
	return false;
	// Down
} else if (event.keyCode == 40) {
	if (!$(this).hasClass('open')) {
	    $(this).trigger('click');
	} else {
	    focused_option.next().focus();
	}
	return false;
	// Up
} else if (event.keyCode == 38) {
	if (!$(this).hasClass('open')) {
	    $(this).trigger('click');
	} else {
	    var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
	    focused_option.prev().focus();
	}
	return false;
// Esc
} else if (event.keyCode == 27) {
	if ($(this).hasClass('open')) {
	    $(this).trigger('click');
	}
	return false;
}
});

$(document).ready(function() {
    create_custom_dropdowns();
});

	</script>



</body>
</html>



