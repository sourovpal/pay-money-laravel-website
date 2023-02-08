<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="top-bar-title padding-bottom">
                    {{ __('Users') }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box">

    <!-- form start -->
    <form action="{{ url('register') }}" class="form-horizontal" id="users_form" method="POST">
        {{ csrf_field() }}
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <input id="user_type" name="user_type" type="hidden" value="">
                            <h4 class="text-info text-center">
                                {{ __('Users Information') }}
                            </h4>
                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('Username') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="username" type="text" value="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('First Name') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="first_name" type="text" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('Last Name') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="last_name" type="text" value="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('Phone') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="phone" type="text" value="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('Email') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="email" type="email" value="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('Password') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="password" type="password" value="" id="password">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label require" for="inputEmail3">
                                    {{ __('Confirm Password') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="password_confirmation" type="password" id="password_confirmation">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="inputEmail3">
                                    {{ __('Phrase') }}
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="phrase" type="text" value="">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <!-- box-footer -->
        <div class="box-footer">
            <a class="btn btn-danger btn-flat" href="{{ url('/') }}">
                {{ __('Cancel') }}
            </a>
            <button class="btn btn-primary pull-right btn-flat" type="submit">
                {{ __('Submit') }}
            </button>
        </div>
        <!-- /.box-footer -->
    </form>
</div>
<script type="text/javascript">

  $('#password-form').validate({
        rules: {
            username: {
                required: true,
            },
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            phone: {
                required: true,
            },
            email: {
                required: true,
            },
            password: {
                required: true,
                minlength: 5
            },
            password_confirmation: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            }
        }
    });

</script>
