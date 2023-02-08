@extends('admin.layouts.master')
@section('title', __('API Credentials'))

@section('page_content')

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.settings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('API Credentials') }}</h3>
                </div>

                <form action="{{ url(\Config::get('adminPrefix').'/settings/api_informations') }}" method="post" class="form-horizontal" id="api-credentials" >
                    {!! csrf_field() !!}

                    <!-- box-body -->
                    <div class="box-body">

                        <!-- Google Captcha Site key -->
                        <div class="form-group row">
                            <label class="col-sm-4 control-label mt-11 f-14 fw-bold text-end" for="inputEmail3">{{ __('Google ReCaptcha Site key') }}</label>
                            <div class="col-sm-6">
                                <input type="text" name="captcha_site_key" class="form-control f-14" value="{{ $recaptcha['site_key'] ?? '' }}" placeholder="{{ __('captcha site key') }}">

                                @if($errors->has('captcha_site_key'))
                                    <span class="help-block">
                                    <strong class="text-danger">{{ $errors->first('captcha_site_key') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Google Captcha Secret key -->
                        <div class="form-group row">
                            <label class="col-sm-4 control-label mt-11 f-14 fw-bold text-end" for="inputEmail3">{{ __('Google ReCaptcha Secret key') }}</label>
                            <div class="col-sm-6">
                                <input type="text" name="captcha_secret_key" class="form-control f-14" value="{{ $recaptcha['secret_key'] ?? '' }}" placeholder="{{ __('captcha secret key') }}">

                                @if($errors->has('captcha_secret_key'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('captcha_secret_key') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <!-- box-footer -->
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_api_credentials'))
                        <div class="box-footer">
                            <button class="btn btn-theme pull-right f-14" type="submit">{{ __('Submit') }}</button>
                        </div>
                    @endif
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>

@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

$.validator.setDefaults({
    highlight: function(element) {
        $(element).parent('div').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).parent('div').removeClass('has-error');
    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    }
});

$('#api-credentials').validate({
    rules: {
        captcha_secret_key: {
            required: true,
        },
        captcha_site_key: {
            required: true,
        },
    },
});

</script>

@endpush
