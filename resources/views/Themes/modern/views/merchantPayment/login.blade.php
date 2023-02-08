@extends('frontend.layouts.app')
@section('content')
<div class="min-vh-100">
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
                                    <form action="{{ request()->fullUrl() }}" method="post" id="login_form">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="email">@lang('message.login.email')</label>
                                            <input type="email" class="form-control" aria-describedby="emailHelp" placeholder="@lang('message.login.email')" name="email" id="email">
                                        </div>

                                        <div class="form-group">
                                            <label for="password">@lang('message.login.password')</label>
                                            <input type="password" class="form-control" id="password" placeholder="@lang('message.login.password')" name="password">
                                        </div>

                                        @if (isset($setting['has_captcha']) && $setting['has_captcha'] == 'Enabled')
                                            <div class="row">
                                                <div class="col-md-12">
                                                    {!! app('captcha')->display() !!}
                                                    <br>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <input class="form-check-input" type="hidden" value="" id="remember_me" name="remember_me">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-grad mt-4 float-left">@lang('message.form.button.login')</button>
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
    <script src="{{theme_asset('public/frontend/js/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery.extend(jQuery.validator.messages, {
            required: "{{ __('This field is required.') }}",
            email: "{{ __('Please enter a valid email address.') }}",
        })
    </script>
    <script>
        $('#login_form').validate({
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
@endsection