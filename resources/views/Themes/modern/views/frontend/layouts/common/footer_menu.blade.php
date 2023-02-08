<?php
    $socialList = getSocialLink();
    $menusFooter = getMenuContent('Footer');
?>

	<!-- Footer section -->
	<div class="footer-sec">
		<div class="px-240">
			<div class="row">
				<div class="col-md-5 pb-54 align-center">
                    @if(!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                        <a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                            <img class="mt-54 footer-logo" src="{{ url('public/images/logos/'. settings('logo')) }}" alt="brand-logo">
                        </a>
                    @else
                        <a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                            <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="mt-54 footer-logo object-contain">
                        </a>
                    @endif

					<p class="pb-0 mt-26 w-358 txt-center text-white OpenSans-400 leading-29">
                        {{ __(':x, a secured online payment gateway that allows payment in multiple currencies easily, safely and securely.', ['x' => settings('name')]) }}
					</p>
					<p class="mb-0 mt-20 mt-r24 OpenSans-700 f-20 text-white txt-center">
						{{ __('Download Our App') }}
					</p>

                    <div class="mt-21 direction">
                        @foreach(getAppStoreLinkFrontEnd() as $app)
                            @if (!empty($app->logo))
                                <a href="{{ $app->link }}" target="_blank">
                                    <img class="{{ $app->company == 'Apple' ?  'ml-3 ml-r12' : '' }} app-imgs" src="{{ url('public/uploads/app-store-logos/thumb/'.$app->logo) }}" alt="playstore">
                                </a>
                            @else
                                <a href="#"><img src='{{ url('public/uploads/app-store-logos/default-logo.jpg') }}' class="img-responsive" width="120" height="90"/></a>
                            @endif
                        @endforeach
					</div>
				</div>
				<div class="col-md-3 align-center">
					<p class="pb-0 mt-58 OpenSans-700 text-white f-20 txt-center quick-res">
						{{ __('Quick Links') }}
					</p>

					<div class="mt-18">
						<ul class="links OpenSans-400">
                            <li><a href="{{ url('/') }}">@lang('message.home.title-bar.home')</a></li>
                                @if(!empty($menusFooter))
                                    @foreach($menusFooter as $footer_navbar)
                                        <li><a href="{{url($footer_navbar->url)}}">{{ $footer_navbar->name }}</a></li>
                                    @endforeach
                                @endif
                            <li><a href="{{ url('/developer') }}">@lang('message.home.title-bar.developer')</a></li>
						</ul>
					</div>
				</div>
				<div class="col-md-4 align-center">
					<div class="custom-postion">
						<p class="pb-0 mt-58 OpenSans-700 text-white f-20 txt-center sign-res">
							{{ __('More Links') }}
						</p>
						<div class="mt-18 more-link">
							<ul class="links OpenSans-400">
                                <li><a href="{{ url('/send-money') }}">@lang('message.home.title-bar.send')</a></li>
                                <li><a href="{{ url('/request-money') }}">@lang('message.home.title-bar.request')</a></li>
							</ul>
						</div>

						<p class="mb-0 mt-10 OpenSans-700 f-20 text-white txt-center socials-res">
							{{ __('Social Links') }}
						</p>
						<div class="d-flex col-gap-12 mt-21">
                            @foreach($socialList as $social)
                                @if (!empty($social->url))
                                    <a href="{{ $social->url }}">{!! $social->icon !!}</a>
                                @endif
                            @endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="bottom-footer">
		<div class="px-240 d-flex justify-content-between btn-footer align-items-center">
            <div>
                <?php
                    $company_name = settings('name');
                ?>
                <p class="mb-0 OpenSans-600 text-white">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
            </div>
			<div>
				<div class="d-flex justify-center-res align-items-center">
					<span class="text-white OpenSans-600">{{ __('Language') }} : </span>
					<div class="form-group OpenSans-600 selectParent footer-font-16 poppins4 mb-0">
						<select class="custom-select form-control footer-font-16 poppins4 mb-2n" data-minimum-results-for-search="Infinity" id="lang">
                            @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                            <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }} value='{{ $lang->short_name }}'> {{ $lang->name }}</option>
                            @endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

