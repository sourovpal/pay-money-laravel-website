@extends('user_dashboard.layouts.app')

@section('css')
    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/iCheck/square/blue.css') }}">
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/sweetalert.css')}}">
@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Settings') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="mt-5 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/profile') }}">
                        <div class="mr-4">
                            <p class="text-16 font-weight-400 text-gray-500">{{ __('Profile') }}</p>
                        </div>
                    </a>

                    @if ($two_step_verification != 'disabled')
                    <a href="{{ url('/profile/2fa') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active "> {{ __('2-FA') }} </p>
                        </div>
                    </a>
                    @endif

                    <a href="{{ url('/profile/personal-id') }}">
                        <div class="mr-4">
                            <span class="text-16 font-weight-400 text-gray-500">{{ __('Identity verification') }}</span>
                            @if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' )
                            (<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) 
                            @endif
                        </div>
                    </a>

                    <a href="{{ url('/profile/personal-address') }}">
                        <div class="mr-4">
                            <span class="text-16 font-weight-400 text-gray-500">{{ __('Address verfication') }}</span>
                            @if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' )(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) @endif
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('2-FA Verification') }}</h3>
                        <p class="text-gray-500 text-16"> {{ __('Mange your 2-FA verication') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="bg-secondary rounded mt-3 shadow p-35">
                                <!-- section_2fa_form -->
                                <section id="section_2fa_form">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="row justify-content-center">
                                                <div class="col-md-12">
                                                    <h4 class="font-weight-600 text-16">@lang('message.2sa.title-text')</h4>
                                                    <form method="POST" class="form-horizontal mt-4" id="2fa_update">
                                                        <input type="hidden" value="{{$user->id}}" name="id" id="id" />
                                                        <input type="hidden" name="gotResonponseFromSubmit" id="gotResonponseFromSubmit" />
                                                        <input type="hidden" name="is_demo" id="is_demo" value="{{ $is_demo }}" />

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <select class="form-control" name="two_step_verification_type" id="two_step_verification_type">
                                                                    @if ($two_step_verification == 'by_email')
                                                                    <option value='disabled' {{ $user->user_detail->two_step_verification_type == 'disabled' ? 'selected':"" }}>{{ __('Disabled') }}</option>
                                                                    <option value='email' {{ $user->user_detail->two_step_verification_type == 'email' ? 'selected':"" }}>{{ __('By email') }}</option>

                                                                    @elseif ($two_step_verification == 'by_phone')
                                                                    <option value='disabled' {{ $user->user_detail->two_step_verification_type == 'disabled' ? 'selected':"" }}>{{ __('Disabled') }}</option>
                                                                    <option value='phone' {{ $user->user_detail->two_step_verification_type == 'phone' ? 'selected':"" }}>{{ __('By phone') }}</option>

                                                                    @elseif ($two_step_verification == 'by_google_authenticator')
                                                                    <option value='disabled' {{ $user->user_detail->two_step_verification_type == 'disabled' ? 'selected':"" }}>{{ __('Disabled') }}</option>
                                                                    <option value='google_authenticator' {{ $user->user_detail->two_step_verification_type == 'google_authenticator' ? 'selected':"" }}>{{ __('By Google Authenticator') }}</option>

                                                                    @elseif ($two_step_verification == 'by_email_phone')
                                                                    <option value='disabled' {{ $user->user_detail->two_step_verification_type == 'disabled' ? 'selected':"" }}>{{ __('Disabled') }}</option>
                                                                    <option value='email' {{ $user->user_detail->two_step_verification_type == 'email' ? 'selected':"" }}>{{ __('By email') }}</option>
                                                                    <option value='phone' {{ $user->user_detail->two_step_verification_type == 'phone' ? 'selected':"" }}>{{ __('By phone') }}</option>
                                                                    @else
                                                                    <option value='disabled' {{ $user->user_detail->two_step_verification_type == 'disabled' ? 'selected':"" }}>{{ __('Disabled') }}</option>
                                                                    @endif
                                                                </select>
                                                                <span id="2sa-error"></span>
                                                            </div>

                                                        </div>
                                                        <div class="clearfix"></div>


                                                        <div class="row mt-1">
                                                            <div class="form-group mb-0 col-md-12">
                                                                <button type="submit" class="btn btn-primary px-4 py-2" id="2fa_submit">
                                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="2fa_submit_text">@lang('message.dashboard.button.submit')</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <!-- section_2fa_verify -->
                                <section id="section_2fa_verify" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-md-12">
                                                <div>
                                                    <form class="form-horizontal" method="POST" id="2fa_verify_form"><!--submitting via ajax-->

                                                        <input type="hidden" name="twoFaVerificationType" id="twoFaVerificationType"/>

                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                <h4 class="text-left font-weight-600 text-16">
                                                                    @lang('message.2sa.extra-step-settings-verify')
                                                                </h4>
                                                                <br>
                                                                <h4 class="text-left font-weight-600 text-16">
                                                                    @lang('message.2sa.confirm-message-verification')
                                                                    <span id="type"></span>
                                                                </h4>
                                                                <br>
                                                            </label>
                                                        </div>

                                                        <div class="form-group {{ $errors->has('two_step_verification_code') ? ' has-error' : '' }}">
                                                            <div>
                                                                <input id="two_step_verification_code" class="form-control" placeholder="Enter the 6-digit code" name="two_step_verification_code"
                                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type = "number" maxlength = "6" required autofocus/>

                                                                @if ($errors->has('two_step_verification_code'))
                                                                <span class="error">
                                                                    <strong>{{ $errors->first('two_step_verification_code') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if (empty($checkDeviceLog))
                                                            <div class="form-group">
                                                                <div class="checkbox icheck">
                                                                    <label>
                                                                        <input type="checkbox" name="remember_me" id="remember_me">
                                                                        <span style="font-size: 16px; font-weight: 600; color: #181818;">&nbsp;&nbsp;&nbsp;@lang('message.2sa.remember-me-checkbox')</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="form-group">
                                                            <div>
                                                                <button type="submit" class="btn btn-primary px-4 py-2 verify_code" id="verify_code">@lang('message.2sa.verify')</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </section>


                                <!-- section_google2fa -->
                                <section  id="section_google2fa" style="display: none;">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="card">

                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div>
                                                                    <div style="text-align: center;">
                                                                        <h4>@lang('message.google2fa.subheader-text')</h4>
                                                                        <span id="qrsecret"></span>
                                                                        <div>
                                                                            <img id="qr_image" class="img-responsive">
                                                                        </div>

                                                                        <h6>@lang('message.google2fa.setup-a')</h6>
                                                                        <br>
                                                                        <h6>@lang('message.google2fa.setup-b')</h6>
                                                                        <br>

                                                                        <button class="btn btn-primary px-4 py-2 completeVerification">@lang('message.google2fa.proceed')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>


                                <!-- section_2fa_otp -->
                                <section  id="section_2fa_otp" style="display: none;">
                                    <div class="container mt-5">
                                        <div class="row">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row justify-content-center">
                                                            <form class="form-horizontal" method="POST" id="otp_form">

                                                                <div class="form-group {{ $errors->has('one_time_password') ? ' has-error' : '' }}">
                                                                    <label for="one_time_password" class="col-md-12 control-label"><h4>@lang('message.google2fa.otp-input')</h4></label>

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

                                                                @if (empty($checkDeviceLog))
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
                                                                @endif

                                                                <div class="form-group">
                                                                    <div class="text-center">
                                                                        <button type="submit" class="btn btn-primary px-4 py-2 transfer_form" id="verify_otp">@lang('message.2sa.verify')</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="clearfix">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
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
<script src="{{ theme_asset('public/js/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/fpjs2/fpjs2.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/sweetalert.min.js') }}" type="text/javascript"></script>

<script>

    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    //check phone
    $(document).ready(function()
    {
        $("#two_step_verification_type").change(function()
        {
            if ($(this).val() == 'phone')
            {
              $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/profile/2fa/check-phone",
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
            })
              .done(function(response)
              {
                if (response.status == false)
                {
                    $('#2sa-error').addClass('error').html(response.message).css({
                        'color' : 'red !important',
                        'font-size' : '14px',
                        'font-weight' : '800',
                        'padding-top' : '5px',
                    });
                    $('form').find("button[type='submit']").prop('disabled',true);
                }
                else
                {
                    $('#2sa-error').html('');
                    $('form').find("button[type='submit']").prop('disabled',false);
                }
            });
          }
          else
          {
              $('#2sa-error').html('');
              $('form').find("button[type='submit']").prop('disabled',false);
          }
      });
    });

    //2fa verifying on submit
    $('#2fa_update').submit(function(event)
    {
        event.preventDefault();
        var is_demo = $('#is_demo').val();
        if (is_demo == true)
        {
            swal({
                    title: "{{ __("Error") }}!",
                    text: "{{ __("2-FA is disabled in demo application") }}",
                    type: "error"
                }
            );
        }
        else
        {
            var two_step_verification_type = $('#two_step_verification_type').val();
            if (two_step_verification_type == 'email' || two_step_verification_type == 'phone')
            {
                $("#2fa_submit").attr("disabled", true);
                $(".spinner").show();
                $("#2fa_submit_text").text('Please wait...');

                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/profile/2fa/ajaxTwoFa",
                    dataType: "json",
                    cache: false,
                    data: {
                        'two_step_verification_type': two_step_verification_type,
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        $('#section_2fa_form').hide();
                        $('#section_2fa_verify').show();
                        $('#twoFaVerificationType').val(response.twoFaVerificationTypeForResponse);
                        $('#type').html(response.twoFa_type);
                    }
                    else
                    {
                        if (response.two_step_verification_type == "email")
                        {
                            swal({
                                    title: "{{ __("Error") }}!",
                                    text: "{{ __("2-FA is already set to by email!") }}",
                                    type: "error"
                                }
                            );
                        }
                        else
                        {
                            swal({
                                    title: "{{ __("Error") }}!",
                                    text: "{{ __("2-FA is already set to by phone!") }}",
                                    type: "error"
                                }
                            );
                        }
                        $("#2fa_submit").attr("disabled", false);
                        $(".spinner").hide();
                        $("#2fa_submit_text").text('Submit');
                    }
                });
            }
            else if (two_step_verification_type == 'google_authenticator')
            {
                // alert('l');
                $("#2fa_submit").attr("disabled", true);
                $(".spinner").show();
                $("#2fa_submit_text").text("{{ __('Please wait...') }}");

                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/profile/2fa/google2fa",
                    dataType: "json",
                    cache: false,
                    data: {
                        'two_step_verification_type': two_step_verification_type,
                    }
                })
                .done(function(response)
                {
                    if (response.status == false)
                    {
                        swal({
                                title: "{{ __("Error") }}!",
                                text: "{{ __("2-FA is already set by google authenticator!") }}",
                                type: "error"
                            }
                        );
                        $("#2fa_submit").attr("disabled", false);
                        $(".spinner").hide();
                        $("#2fa_submit_text").text('Submit');
                    }
                    else
                    {
                        $('#section_2fa_form').hide();
                        $('#section_2fa_verify').hide();
                        $('#section_google2fa').show();

                        $("#qrsecret").html(response.secret).hide();
                        $("#qr_image").attr("src", response.QR_Image);
                        $('#twoFaVerificationType').val(response.twoFaVerificationTypeForResponse);
                    }
                });
            }
            else
            {
                // alert('disabled');
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL+"/profile/2fa/disabledTwoFa",
                    dataType: "json",
                    cache: false,
                    data: {
                        'two_step_verification_type': two_step_verification_type,
                    }
                })
                .done(function(response)
                {
                    console.log(response);
                    if (response.status == true)
                    {
                        $('#section_2fa_form').show();
                        $('#section_2fa_verify').hide();

                        $('#twoFaVerificationType').val(response.twoFaVerificationTypeForResponse);
                        swal({
                                title: "{{ __("Success") }}!",
                                text: "{{ __("2 FA Setting Updated Successfully") }}",
                                type: "success"
                            }
                        );

                    }
                });
            }
        }
    });

    //verifying 2fa on submit
    $(document).ready(function()
    {
        $('#2fa_verify_form').submit(function(event)
        {
            event.preventDefault();

            var token = '{{csrf_token()}}';
            var twoFaVerificationType = $("#twoFaVerificationType").val();

            var two_step_verification_code = $("#two_step_verification_code").val();

            var remember_me = $("#remember_me").is(':checked');

            //Fingerprint2
            new Fingerprint2().get(function(result, components)
            {
               $.ajax({
                    method: "POST",
                    url: SITE_URL + "/profile/2fa/ajaxTwoFaSettingsVerify",
                    cache: false,
                    dataType:'json',
                    data: {
                        "_token": token,
                        'two_step_verification_code': two_step_verification_code,
                        'twoFaVerificationType': twoFaVerificationType,
                        'remember_me': remember_me,
                        'browser_fingerprint': result,
                    }
                })
               .done(function(data)
               {
                    if (data.status == false || data.status == 404)
                    {
                        //failure
                        swal({
                                title: "Error!",
                                text: data.message,
                                type: "error"
                            }
                        );
                    }
                    else
                    {
                        //success
                        $('#section_2fa_form').show();
                        $('#section_2fa_verify').hide();
                        swal({
                                title: "Success!",
                                text: data.message,
                                type: "success"
                            }
                        );
                        $("#2fa_submit").attr("disabled", false);
                        $(".spinner").hide();
                        $("#2fa_submit_text").text('Submit');

                        //resetting values below
                        $('#two_step_verification_code').val('');
                        $('input').iCheck('uncheck');
                    }
                });
            });
        });
    });


    //google 2fa on submit
    $(document).on('click', '.completeVerification', function()
    {
        var google2fa_secret = $("#qrsecret").html();

        $.ajax({
            headers:
            {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: SITE_URL+"/profile/2fa/google2fa/complete-google2fa-verification",
            dataType: "json",
            cache: false,
            data: {
                'google2fa_secret': google2fa_secret,
            }
        })
        .done(function(response)
        {
            if (response.status == true)
            {
                $('#section_2fa_form').hide();
                $('#section_2fa_verify').hide();
                $('#section_google2fa').hide();
                $('#section_2fa_otp').show();
            }
        });
    });

    //google 2fa verifying OTP on submit
    $(document).ready(function()
    {
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
                    url: SITE_URL + "/profile/2fa/google2fa/otp-verify",
                    cache: false,
                    dataType:'json',
                    data: {
                        "_token": token,
                        'one_time_password': one_time_password,
                        'two_step_verification_type': two_step_verification_type,
                        'remember_otp': remember_otp,
                        'browser_fingerprint': result,
                    },
                })
                .done(function(data)
                {
                    if (data.status == true)
                    {
                        //true
                        $('#section_2fa_form').show();
                        $('#section_2fa_verify').hide();
                        $('#section_google2fa').hide();
                        $('#section_2fa_otp').hide();
                        swal({
                                title: "Success!",
                                text: data.message,
                                type: "success"
                            }
                        );
                        $("#2fa_submit").attr("disabled", false);
                        $(".spinner").hide();
                        $("#2fa_submit_text").text('Submit');

                        //resetting values below
                        $('#one_time_password').val('');
                        $('input').iCheck('uncheck');
                    }
                    else
                    {
                        swal({
                                title: "{{ __('Error') }}",
                                text: "{{ __('One time password is incorrect!') }}",
                                type: "error"
                            }
                        );
                    }
                })
                .fail(function(error)
                {
                    console.log(error);

                    swal({
                            title: "Error",
                            text: JSON.parse(error.responseText).exception,
                            type: "error"
                        }
                    );
                });
            });
        });
    });


</script>
@endsection
