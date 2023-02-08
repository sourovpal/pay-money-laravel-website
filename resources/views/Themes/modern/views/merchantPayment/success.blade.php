<!DOCTYPE html>
<html lang="en">
  <head>
    <title>@lang('message.express-payment-form.merchant-payment')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5.0.2 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap-css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css')}}">
    <!-- jQuery 3 -->
    <script src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/popper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
    var SITE_URL = "{{URL::to('/')}}";
    </script>
  </head>
  <body>
    <div class="container text-center">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="panel panel-default box-shadow mt-5">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-success">
                    <strong> @lang('message.express-payment-form.success')</strong> @lang('message.express-payment-form.payment-successfull')
                  </div>
                  <a href="{{url('/dashboard')}}" class="btn btn-sm btn-info text-white">@lang('message.express-payment-form.back-home')</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </body>
  </html>
