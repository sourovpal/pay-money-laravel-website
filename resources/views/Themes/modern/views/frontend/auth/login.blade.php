@extends('frontend.layouts.app')
@section('content')
<div class="min-vh-100 mt-93">
      <!--Start banner Section-->
      <section class="bg-image">
        <div class="bg-dark">
            <div class="container">
                <div class="row py-5">
                    <div class="col-md-12">
                        <h2 class="text-white font-weight-bold text-28">@lang('message.login.title') </h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End banner Section-->

    <!--Start Section-->
    <section class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row flex-column-reverse flex-md-row">
                        <div class="col-md-7 mt-3">
                            <div>
                                <img src="{{ theme_asset('public/images/banner/bannerone.png') }}" alt="Phone Image" class="img-responsive img-fluid" />
                            </div>
                        </div>

                        <div class="col-md-5 mt-5">
                            <!-- form card login -->
                            @include('frontend.layouts.common.alert')
                            <div class="card p-4 rounded-0">
                                <div>
                                    <h3 class="mb-0 text-left font-weight-bold">@lang('message.login.form-title')</h3>
                                    <p class="mt-2 text-14">
                                        <span>@lang('message.login.no-account') &nbsp; </span>
                                        <a href="{{url('register')}}" class="text-active">@lang('message.login.sign-up-here')</a>.
                                    </p>
                                </div>
                                <br>
                                <div>
                                    <form action="{{ url('authenticate') }}" method="post" id="login_form">
                                            {{ csrf_field() }}
                                        <input type="hidden" name="has_captcha" value="{{ isset($setting['has_captcha']) && ($setting['has_captcha'] == 'login' || $setting['has_captcha'] == 'login_and_registration') ? 'login' : 'Disabled' }}">

                                        <input type="hidden" name="login_via" value="
                                        @if (isset($setting['login_via']) && ($setting['login_via'] == 'email_only'))
                                            {{ "email_only" }}
                                        @elseif(isset($setting['login_via']) && ($setting['login_via'] == 'phone_only'))
                                            {{ "phone_only" }}
                                        @else
                                            {{ "email_or_phone" }}
                                        @endif
                                        ">

                                        <input type="hidden" name="browser_fingerprint" id="browser_fingerprint" value="test">

                                        @if (isset($setting['login_via']) && $setting['login_via'] == 'email_only')
                                            <div class="form-group mt-4">
                                                <label for="email_only">@lang('message.login.email')</label>
                                                <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="@lang('message.login.email')" name="email_only" id="email_only">

                                                @if($errors->has('email_only'))
                                                    <span class="error">
                                                     {{ $errors->first('email_only') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif(isset($setting['login_via']) && $setting['login_via'] == 'phone_only')
                                            <div class="form-group">
                                                <label for="phone_only">@lang('message.login.phone')</label>
                                                <input type="text" class="form-control" aria-describedby="phoneHelp" placeholder="@lang('message.login.phone')" name="phone_only" id="phone_only"
                                                ">

                                                @if($errors->has('phone_only'))
                                                    <span class="error">
                                                     {{ $errors->first('phone_only') }}
                                                    </span>
                                                @endif
                                            </div>

                                        @elseif(isset($setting['login_via']) && $setting['login_via'] == 'email_or_phone')
                                            <div class="form-group">
                                                <label for="email_or_phone">@lang('message.login.email_or_phone')</label>
                                                <input type="text" class="form-control" aria-describedby="emailorPhoneHelp" placeholder="@lang('message.login.email_or_phone')" name="email_or_phone" id="email_or_phone">

                                                @if($errors->has('email_or_phone'))
                                                    <span class="error">
                                                     {{ $errors->first('email_or_phone') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="password">@lang('message.login.password')</label>
                                            <input type="password" class="form-control" id="password" placeholder="@lang('message.login.password')" name="password">

                                            @if ($errors->has('password'))
                                                <span class="error">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        @if (isset($setting['has_captcha']) && ($setting['has_captcha'] == 'login' || $setting['has_captcha'] == 'login_and_registration'))
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
                                          <input class="form-check-input" type="hidden" value="" id="remember_me" name="remember_me">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-grad float-left" id="login-btn">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                    <span id="login-btn-text" style="font-weight: bolder;">
                                                        @lang('message.form.button.login')
                                                    </span>
                                                </button>
                                            </div>
                                          </div>
                                        <div class="row">
                                            <div class="col-md-12 get-color" style="margin: -2px 0 6px 0px;">
                                                <br>
                                                <a href="{{url('forget-password')}}">@lang('message.login.forget-password')</a>
                                            </div>
                                        </div>
                                    </form>
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
</div>
@endsection
@section('js')

<script src="{{ theme_asset('public/js/fpjs2/fpjs2.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {
        required: "{{__('This field is required.')}}",
    })
</script>

<script>
    $('#login_form').validate({
        rules:
        {
            email_only: {
                required: true,
            },
            phone_only: {
                required: true,
            },
            email_or_phone: {
                required: true,
            },
            password: {
                required: true
            },
        },
        submitHandler: function(form)
        {
            $("#login-btn").attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });
            $(".spinner").show();
            $("#login-btn-text").text("{{ __('Signing In...') }}");
            form.submit();
        }
    });

    $(document).ready(function()
    {
        new Fingerprint2().get(function(result, components)
        {
            $('#browser_fingerprint').val(result);
        });
    });
</script>

@endsection

