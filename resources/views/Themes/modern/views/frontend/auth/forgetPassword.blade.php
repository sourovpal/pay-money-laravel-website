@extends('frontend.layouts.app')
@section('content')
<div class="min-vh-100 mt-93">
    <!--Start banner Section-->
    <section class="bg-image">
      <div class="bg-dark">
          <div class="container">
              <div class="row py-5">
                  <div class="col-md-12">
                      <h2 class="text-white font-weight-bold text-28">@lang('message.form.forget-password-form')</h2>
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
                        <div class="col-md-6 mt-3">
                            <div>
                                <img src="{{ theme_asset('public/images/banner/bannerone.png') }}" alt="Phone Image" class="img-responsive img-fluid" />
                            </div>
                        </div>

                        <div class="col-md-6 mt-5 mx-auto">
                            @include('frontend.layouts.common.alert')
                            <div class="card p-4 rounded-0">
                                <div>
                                    <h3 class="mb-0 text-left font-weight-bold">@lang('message.form.forget-password-form')</h3>
                                </div>

                                <div class="mt-4">
                                    <form action="{{ url('forget-password') }}" method="post" id="forget-password-form">
                                        {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="email">@lang('message.form.email')</label>
                                        <input type="email" class="form-control" aria-describedby="emailHelp" placeholder="@lang('message.form.email')" name="email" id="email">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-grad mt-4" id="forget-password-submit-btn">
                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                <span id="forget-password-submit-btn-text" style="font-weight: bolder;">
                                                    @lang('message.form.submit')
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                </div>
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
    <script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script>

        jQuery.extend(jQuery.validator.messages, {
            required: "{{ __('This field is required.') }}",
            email: "{{ __("Please enter a valid email address.") }}",
        });

        $('#forget-password-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                }
            },
            submitHandler: function(form)
            {
                $("#forget-password-submit-btn").attr("disabled", true).click(function (e)
                {
                    e.preventDefault();
                });
                $(".spinner").show();
                $("#forget-password-submit-btn-text").text("{{ __('Submitting...') }}");
                form.submit();
            }
        });
    </script>

@endsection
