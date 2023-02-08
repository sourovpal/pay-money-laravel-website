
@php
 $extensions = json_encode(getFileExtensions(1));
@endphp

@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid" id="ticket-reply">
            <!-- Page title start -->
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="page-title">{{ __('Tickets') }}</h3>
                </div>

                <div>
                    <a href="{{ url('/ticket/add') }}" class="btn btn-primary px-4 py-2">
                        <i class="fa fa-plus"></i> {{ __('New ticket') }}
                    </a>
                </div>
            </div>

            <!-- Page title end-->

            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/tickets') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">@lang('message.dashboard.ticket.details.form.title')</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 text-16">{{ __('See details conversation') }}</p>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 col-xl-3">
                    <!-- Sub title start -->
                    <div class="mt-4 p-4 bg-secondary rounded shadow">
                        <div class="mb-4">
                            <div class="ticket-line">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.ticket-id')</div>
                                <div class="generalcolor-txt">{{ $ticket->code }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.subject')</div>
                                <div class="generalcolor-txt">{{ $ticket->subject }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.date')</div>
                                <div class="generalcolor-txt">{{ dateFormat($ticket->created_at) }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.priority')</div>
                                <div class="generalcolor-txt">{{ $ticket->priority }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.status')</div>
                                <div class="generalcolor-txt">
                                    @php
                                        echo getStatusBadge($ticket->ticket_status->name);
                                    @endphp
                                </div>
                            </div>

                            <div class="ticket-btn ticket-line mt-3 d-none">
                                <select class="form-control" name="status" id="status">
                                    @foreach($ticket_status as $val)
                                        <option value="{{$val->id}}" <?= ($ticket->ticket_status->id == $val->id) ? 'selected' : ''  ?> >{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="ticket_id" value="{{ $ticket->id }}">
                            </div>
                        </div>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-md-8 col-xl-9">
                    <div class="bg-secondary rounded m-0 mt-4 shadow">

                            <div class="p-35">
                                @include('user_dashboard.layouts.common.alert')
                                @if($ticket->ticket_status->name != 'Closed')
                                    <form action="{{url('ticket/reply_store')}}" id="reply" method="post"
                                        enctype="multipart/form-data">
                                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label>@lang('message.dashboard.ticket.details.form.message')
                                                <span class="text-danger">*</span>
                                            </label>

                                            <textarea name="description" id="description" class="form-control" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"></textarea>
                                            @if($errors->has('description'))
                                                <span class="error">
                                                {{ $errors->first('description') }}
                                            </span>
                                            @endif
                                            <p id="description-error" class="text-danger"></p>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="file" name="file" id="file" class="upload-text border rounded p-1"/>
                                                    <span class="file-error" id="fileSpan"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group d-none">
                                                    <label class="control-label" for="exampleInputFile">@lang('message.dashboard.ticket.details.sidebar.status')</label>
                                                    <select class="form-control" name="status_id" id="status_id">
                                                        @foreach($ticket_status as $val)
                                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="text-right">
                                                    <button class="btn btn-primary px-4 py-2" id="ticket-reply">
                                                        <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                                        <span id="ticket-reply-text" class="font-weight-bolder">
                                                            @lang('message.dashboard.button.submit')
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                            <hr class="mt-0">

                        <div class="px-4">
                            <div class="d-flex">
                                <div class="pr-2">
                                    @if(!empty($ticket->user->picture) && file_exists(public_path('user_dashboard/profile/' . $ticket->user->picture)))
                                        <img src="{{ url('public/user_dashboard/profile/' . $ticket->user->picture) }}" class="rounded-circle" style="width:50px;">
                                    @else
                                        <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" alt="User Image" class="rounded-circle" style="width:50px;">
                                    @endif
                                </div>

                                <div>
                                    <p class="font-weight-600">{{$ticket->user->first_name.' '.$ticket->user->last_name}}</p>
                                    <p class="text-12 text-gray-500">{{ dateFormat($ticket->created_at) }}</p>
                                </div>
                            </div>

                            <div class="pl-5 mt-3 ml-2">
                                <p>{{ $ticket->message }}</p>
                            </div>
                        </div>
                        <br>
                        @if( $ticket_replies->count() > 0 )
                            @foreach($ticket_replies as $result)
                                @if($result->user_type == 'user' )
                                    <div class="px-4">
                                        <div class="d-flex">
                                            <div class="pr-2">
                                                @if(!empty($result->user->picture) && file_exists(public_path('user_dashboard/profile/' . $result->user->picture)))
                                                    <img src="{{ url('public/user_dashboard/profile/' . $result->user->picture) }}" class="rounded-circle" style="width:50px;">
                                                @else
                                                    <img src="{{ url('public/user_dashboard/images/avatar.jpg') }}" alt="" class="rounded-circle" style="width:50px;">
                                                @endif
                                            </div>

                                            <div>
                                                <p class="font-weight-600">{{ $result->user->first_name.' '.$result->user->last_name }}</p>
                                                <p class="text-12 text-gray-500">{{ dateFormat($result->created_at) }}</p>
                                            </div>
                                        </div>

                                        <div class="pl-5 mt-3 ml-2">
                                            <p>{!! $result->message !!}</p>
                                                @if($result->file)
                                                    <div class="mt-3">
                                                        <a class="text-info rounded-lg p-1 bg-light" href="{{ url('/ticket/download',$result->file->filename)}}"><i class="fa fa-download text-active"></i> {{ $result->file->originalname }}</a>
                                                    </div>
                                                @endif
                                        </div>
                                    </div>
                                    <br>
                                @else

                                    <div class="px-4">
                                        <div class="d-flex">
                                            <div class="pr-2">
                                                @if(!empty($result->admin->picture) && file_exists(public_path('uploads/userPic/' . $result->admin->picture)))
                                                    <img src="{{ url('public/uploads/userPic/' . $result->admin->picture) }}" class="rounded-circle" style="width:50px;">
                                                @else
                                                    <img src="{{ url('public/user_dashboard/images/avatar.jpg') }}" alt="Admin Image" class="rounded-circle" style="width:50px;">
                                                @endif
                                            </div>

                                            <div>
                                                <p class="font-weight-600">{{ $result->admin->first_name.' '.$result->admin->last_name }}</p>
                                                <p class="text-12 text-gray-500">{{ dateFormat($result->created_at) }}</p>
                                            </div>
                                        </div>

                                        <div class="pl-5 mt-3 ml-2">
                                            <p>{!! $result->message !!}</p>
                                            @if($result->file)
                                                <div class="mt-3">
                                                    <a class="text-info rounded-lg p-1 bg-light" href="{{ url('/ticket/download',$result->file->filename)}}"><i class="fa fa-download text-active"></i> {{ $result->file->originalname }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <br>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script src="{{ asset('public/plugins/html-validation-1.0.0/validation.min.js') }}" type="text/javascript"></script>
<script>
    'use strict';
    var extensions = JSON.parse(@json($extensions));
	var extensionsValidation = extensions.join(', ');
	var errorMessage = '{{ __("Please select (:x) file.") }}';
	var extensionsValidationMessage = errorMessage.replace(':x', extensionsValidation);
    var submittingText = '{{ __("Submitting...") }}';
    var requirdFieldText = '{{ __("This field is required.") }}';
    var statusChangeText = '{{ __("Ticket reply status :x successfully done.") }}';
</script>
<script src="{{ asset('public/user_dashboard/js/ticket/ticket_reply.min.js')}}" type="text/javascript"></script>
@endsection
