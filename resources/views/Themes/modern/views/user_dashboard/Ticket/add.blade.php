@extends('user_dashboard.layouts.app')

@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Tickets') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="row mt-4">
                <div class="col-md-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('New ticket') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Open a new ticket.') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="bg-secondary rounded m-0 mt-4 p-35 shadow">
                                <form action="{{url('ticket/store')}}" method="post" enctype="multipart/form-data" accept-charset="utf-8" id="ticket">
                                    <div>
                                        <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

                                        <div class="form-group">
                                            <label for="subject">@lang('message.dashboard.ticket.add.name')<span class="text-danger">*</span></label>
                                            <input class="form-control" name="subject" id="subject" type="text"
                                                    value="{{old('subject')}}">
                                            @if($errors->has('subject'))
                                                <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('subject') }}</strong>
                                        </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="description">@lang('message.dashboard.ticket.add.message')<span class="text-danger">*</span></label>
                                            <textarea name="description" class="form-control"
                                                        id="description">{{old('description')}}</textarea>
                                            @if($errors->has('description'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{ $errors->first('description') }}</strong>
                                                </span>
                                            @endif
                                            <p id="description-error" class="text-danger"></p>
                                        </div>

                                        <div class="form-group">
                                            <label>@lang('message.dashboard.ticket.add.priority')</label>
                                            <select class="form-control" name="priority" id="priority">
                                                <option value="Low">{{ __('Low') }}</option>
                                                <option value="Normal">{{ __('Normal') }}</option>
                                                <option value="High">{{ __('High') }}</option>
                                            </select>
                                        </div>

                                        <div class="mt-1">
                                            <a href="{{ url('/tickets') }}" class="btn btn-danger px-4 py-2" style="color: white !important;">Cancel</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="submit" class="btn btn-primary px-4 py-2" id="ticket_create">
                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="ticket_create_text">@lang('message.dashboard.button.submit')</span>
                                            </button>
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

<script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script>

jQuery.extend(jQuery.validator.messages, {
    required: "{{ __('This field is required.') }}",
})

$('#ticket').validate({
    rules: {
        subject: {
            required: true
        },
        description: {
            required: true
        }
    },
    submitHandler: function(form)
    {
        $("#ticket_create").attr("disabled", true);
        $(".spinner").show();
        $("#ticket_create_text").text("{{ __('Submitting...') }}");
        form.submit();
    }
});

</script>

@endsection
