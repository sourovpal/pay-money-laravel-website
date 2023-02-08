<?php
/**
 * Created By: TechVillage.net
 * Start Date: 22-Jan-2018
 */
$logo = settings('logo');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="paymoney">
    <title>{{ __('Admin') }}</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap-css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css')}}">

    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/AdminLTE.min.css') }}">

    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/iCheck/square/blue.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/styles.css') }}">

    <!---favicon-->
    @if (!empty(settings('favicon')))
        <link rel="shortcut icon" href="{{asset('public/images/logos/'.settings('favicon'))}}" />
    @endif


</head>

<body class="hold-transition login-page bg-ec">
<div class="login-box">
    <div class="login-logo">
        <a href="{{ url(\Config::get('adminPrefix').'/') }}">{!! getSystemLogo('img-responsive log-img') !!}</a>
    </div>

    <div class="login-box-body login-design">

        @if(Session::has('message'))
            <div class="alert {{ Session::get('alert-class') }} text-center">
                <strong>{{ Session::get('message') }}</strong>
                <a class="cursor-pointer close h5 ms-3" data-dismiss="alert" aria-hidden="true">&times;</a>
            </div>
        @endif

        <form action="{{ url(\Config::get('adminPrefix').'/adminlog') }}" method="POST" id="admin_login_form">
            {{ csrf_field() }}

            <div class="form-group has-feedback position-relative {{ $errors->has('email') ? 'has-error' : '' }}">
                <label class="control-label sr-only" for="inputSuccess2">{{ __('Email') }}</label>
                <input type="email" class="form-control f-14" placeholder="{{ __('Email') }}" name="email">
                <span class="fa fa-envelope form-control-feedback position-absolute mail-log"></span>

                @if ($errors->has('email'))
                    <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                @endif
            </div>

            <div class="form-group has-feedback position-relative {{ $errors->has('password') ? 'has-error' : '' }}">
                <label class="control-label sr-only" for="inputSuccess2">{{ __('Password') }}</label>
                <input type="password" class="form-control f-14" placeholder="{{ __('Password') }}" name="password" id="password">
                <span class="fa fa-lock f-24 form-control-feedback position-absolute mail-log"></span>

                @if ($errors->has('password'))
                    <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label class="f-14">
                            <input type="checkbox"> {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-theme f-14 btn-block">{{ __('Sign In') }}</button>
                </div>
            </div>
        </form>
        <!-- /.social-auth-links -->
        <a href="{{ url(\Config::get('adminPrefix').'/forget-password') }}" class="f-14">{{ __('I forgot my password') }}</a><br>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<!-- Bootstrap 3.3.5 -->
<script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>

<!-- iCheck -->
<script src="{{ asset('public/backend/iCheck/icheck.min.js') }}" type="text/javascript"></script>

<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
    });

    $('#admin_login_form').validate({
        errorClass: "has-error",
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true
            }
        }
    });
</script>
</body>
