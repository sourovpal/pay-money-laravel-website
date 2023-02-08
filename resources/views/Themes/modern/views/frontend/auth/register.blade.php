@extends('frontend.layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/plugins/intl-tel-input-17.0.19/css/intlTelInput.min.css') }}">
@endsection

@section('content')

<!--Start banner Section-->
<section class="bg-image mt-93">
    <div class="bg-dark">
        <div class="container">
            <div class="row py-5">
                <div class="col-md-12">
                    <h2 class="text-white font-weight-bold text-28">@lang('message.registration.title')</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!--End banner Section-->

<!--Start Section-->
<section class="sign-up padding-30 pb-44">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-8 mt-5">
                        <!-- form card login -->
                        <div class="card p-4 rounded-0">
                            <div>
                                <h3 class="mb-0 font-weight-bold">@lang('message.registration.form-title')</h3>
                                <p class="mt-2 text-14">
                                    <span>@lang('message.registration.new-account-question') &nbsp;</span>
                                    <a href="{{url('login')}}" class="text-active">@lang('message.registration.sign-here')</a>.
                                </p>
                            </div>

                            <div class="mt-4 mb-3">
                                @include('frontend.layouts.common.alert')

                                <form action="{{ url('register/store') }}" class="form-horizontal" id="register_form" method="POST">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="has_captcha" value="{{ isset($enabledCaptcha) && ($enabledCaptcha == 'registration' || $enabledCaptcha == 'login_and_registration') ? 'registration' : 'Disabled' }}">

                                    <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                                    <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                                    <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">

                                    <!-- FirstName -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.first-name')<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                                required data-value-missing="{{ __('This field is required.') }}"
                                                maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('First name'), 'y' => __('30')]) }}">
                                                @if($errors->has('first_name'))
                                                <span class="error">
                                                    {{ $errors->first('first_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- LastName -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.last-name')<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                                required data-value-missing="{{ __('This field is required.') }}"
                                                maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Last name'), 'y' => __('30')]) }}">

                                                @if($errors->has('last_name'))
                                                <span class="error">
                                                    {{ $errors->first('last_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">@lang('message.registration.email')<span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email')}}" 
                                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" 
                                                data-type-mismatch="{{ __('Enter a valid :x.', [ 'x' => strtolower(__('email'))]) }}">
                                                @if($errors->has('email'))
                                                    <span class="error">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span id="email_error"></span>
                                                <span id="email_ok" class="text-success"></span>
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.phone')</span></label>
                                                <br>
                                                <input type="tel" class="form-control" id="phone" name="phone">
                                                <span id="duplicate-phone-error"></span>
                                                <span id="tel-error"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="password">@lang('message.registration.password')<span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" name="password" id="password" 
                                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => '6']) }}">
                                                @if($errors->has('password'))
                                                    <span class="error">
                                                        {{ $errors->first('password') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Passsword Confirmation -->
                                        <div class=" col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="password_confirmation">@lang('message.registration.confirm-password')<span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                                                required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => '6']) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- UserType -->
                                    <div class="form-group">
                                        <label for="type">@lang('message.registration.type-title')<span class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control" name="type" id="type" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                          <option value=''>@lang('message.registration.select-user-type')</option>
                                          @if (!empty($checkUserRole))
                                            <option value='user'>@lang('message.registration.type-user')</option>
                                          @endif
                                          @if (!empty($checkMerchantRole))
                                            <option value='merchant'>@lang('message.registration.type-merchant')</option>
                                          @endif
                                        </select>
                                    </div>

                                    <!-- reCaptcha -->
                                    @if (isset($enabledCaptcha) && ($enabledCaptcha == 'registration' || $enabledCaptcha == 'login_and_registration'))
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! app('captcha')->display() !!}

                                                @if ($errors->has('g-recaptcha-response'))
                                                    <span class="error">
                                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                                    </span>
                                                    <br>
                                                @endif
                                                <br>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="checkbox">
                                              <p class="text-14">@lang('message.registration.terms')</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-sm-12 mt20">
                                            <button type="submit" class="btn btn-grad mt-4" id="users_create"><i class="spinner fa fa-spinner fa-spin d-none" "></i> <span id="users_create_text">@lang('message.form.button.sign-up')</span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="{{ asset('public/plugins/html-validation-1.0.0/validation.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/plugins/intl-tel-input-17.0.19/js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/isValidPhoneNumber.min.js') }}" type="text/javascript"></script>

<script>
    'use strict';
    let requiredText = '{{ __("This field is required.") }}';
    let validEmailText = '{{ __("Please enter a valid email address.") }}';
    let samePasswordText = '{{ __("Please enter the same value again.") }}'
    let confirmSamePasswordText = '{{ __("Please enter same value as the password field.") }}';
    let alphabetSpaceText = '{{ __("Please enter only alphabet and spaces.") }}';
    let signingUpText = '{{ __("Signing Up...") }}';
    let countryShortCode = '{{ getDefaultCountry() }}';
    let validPhoneNumberText = '{{ __("Please enter a valid international phone number.") }}'
    let utilsJsScript = '{{ asset("public/plugins/intl-tel-input-17.0.19/js/utils.min.js") }}';
</script>
<script src="{{ asset('public/frontend/js/register/register.min.js') }}" type="text/javascript"></script>
@endsection
