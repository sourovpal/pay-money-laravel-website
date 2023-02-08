@extends('frontend.layouts.app')
@section('content')

<div class="min-vh-100 mt-93">

          <!--Start banner Section-->
          <section class="bg-image">
            <div class="bg-dark">
                <div class="container">
                    <div class="row py-5">
                        <div class="col-md-12">
                            <h2 class="text-white font-weight-bold text-28">@lang('message.form.reset-password')</h2>
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
                        <div class="col-md-5 mt-5 pt-3">
                            <!-- form card login -->
                            <div class="card card p-4 rounded-0">
                                <div>
                                    <div>
                                        <h3 class="mb-0 text-left font-weight-bold">
                                            <span>@lang('message.form.reset-password')</span>
                                        </h3>
                                    </div>

                                    @include('frontend.layouts.common.alert')
                                    <br>

                                    <form action="{{ url('confirm-password') }}" method="post" id="resetForm">
                                            {{ csrf_field() }}
                                            <input type="hidden" value="{{@$token}}" name="token">

                                        <div class="form-group">
                                            <label for="password">@lang('message.form.new_password')<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" id="password">
                                            @if($errors->has('password'))
                                            <span class="error">
                                                {{ $errors->first('password') }}
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="inputPassword4">@lang('message.form.confirm_password')<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-grad mt-4 float-right" id="set-password-submit-btn">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                    <span id="set-password-submit-btn-text" style="font-weight: bolder;">
                                                        @lang('message.form.submit')
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!--/card-block-->
                            </div>
                            <!-- /form card login -->
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

    <script>

        jQuery.extend(jQuery.validator.messages, {
            required: "{{ __('This field is required.') }}",
            minlength: $.validator.format( "{{ __("Please enter at least") }}"+" {0} "+"{{ __("characters.") }}" ),
            equalTo: "{{ __("Please enter the same value again.") }}",
            password_confirmation: {
                equalTo: "{{ __("Please enter same value as the password field!") }}",
            },
        })

        $('#resetForm').validate({
            rules: {
                password: {
                    required: true,
                    minlength: 6,
                },
                password_confirmation: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            },
            messages: {
                password_confirmation: {
                    equalTo: "{{ __("Please enter same value as the password field!") }}",
                }
            },
            submitHandler: function(form)
            {
                $("#set-password-submit-btn").attr("disabled", true).click(function (e)
                {
                    e.preventDefault();
                });
                $(".spinner").show();
                $("#set-password-submit-btn-text").text("{{ __('Submitting...') }}");
                form.submit();
            }
        });
    </script>

@endsection
