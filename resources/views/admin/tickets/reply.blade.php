@php
    $extensions = json_encode(getFileExtensions(1));
@endphp

@extends('admin.layouts.master')

@section('title', __('Ticket Reply'))

@section('head_style')
  <!-- wysihtml5 -->
  <link rel="stylesheet" type="text/css" href="{{  asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
@endsection

@section('page_content')
<div id="ticket-reply">
<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="col-md-9">
                <div class="top-bar-title padding-bottom">{{ __('Ticket Reply') }}</div>
            </div>
            <div class="col-md-3">
             <h4 class="pull-right f-18">{{ __('Ticket Status') }}: {!! getStatusLabel($ticket->ticket_status->name) !!}</h4>

            </div>
        </div>
    </div>
</div>

<!-- Reply Form -->
<div class="box">
    <div class="box-header with-border"><h4 class="f-18"> <strong>{{ __('Subject') }}  : </strong> {{ $ticket->subject }}</h4></div>

    <div class="row px-4 py-3 align-items-center">
        <div class="col-md-10">
            <span class="label label-default px-2 py-1 f-14 rounded fw-bold">{{ __('Priority') }} : {{ $ticket->priority }}</span>
            @if(isset($ticket->admin_id))
                <span class="label label-warning px-2 py-1 f-14 rounded fw-bold">{{ __('Assignee') }} : {{ $ticket->admin->first_name.' '.$ticket->admin->last_name }}</span>
            @endif
        </div>

        <div class="col-md-2">
            <span>
                <select id="status_ticket" class="form-control f-14 select2">
                    @foreach($ticket_status as $status)
                        <option {{ $status->id == $ticket->ticket_status_id ? 'selected':'' }}  value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </span>
        </div>
    </div>


    <div class="box-body">

        <form class="form-horizontal" id="reply_form" action="{{url(\Config::get('adminPrefix').'/tickets/reply/store')}}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}

            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

            <input type="hidden" name="user_id" value="{{ $ticket->user_id }}">

            <input type="hidden" name="name" value="{{ $ticket->user->first_name.' '.$ticket->user->last_name }}">

            <input type="hidden" name="email" value="{{ $ticket->user->email }}">


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-sm-1 control-label f-14 fw-bold text-end require">{{ __('Reply') }}</label>
                        <div class="col-sm-11">
                            <textarea name="message" id="message" class="message form-control f-14" cols="30" rows="1"></textarea>
                            @if($errors->has('message'))
                                <span class="help-block">
                                  <strong class="text-danger">{{ $errors->first('message') }}</strong>
                                </span>
                            @endif
                            <div id="error-message"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                 <div class="row">
                  <div class="col-md-4">
                      <div class="form-group row align-items-center">
                          <label class="col-sm-3 control-label f-14 fw-bold text-end" >{{ __('Status') }}</label>
                          <div class="col-sm-6">
                              <select name="status_id" class="form-control f-14 select2">
                                @foreach($ticket_status as $status)
                                    <option {{ $status->id == $ticket->ticket_status_id ? 'selected':'' }}  value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group row align-items-center">
                          <label class="col-sm-3 control-label f-14 fw-bold text-end" >{{ __('File') }}</label>
                          <div class="col-sm-9">
                            <input type="file" name="file" class="form-control f-14 input-file-field">
                            @if($errors->has('file'))
                                <span class="help-block">
                                  <strong class="text-danger">{{ $errors->first('file') }}</strong>
                                </span>
                            @endif
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <button type="submit" class="btn btn-primary pull-right btn-flat f-14 rounded" id="reply"><i class="fa fa-spinner fa-spin d-none"></i> <span id="reply_text">{{ __('Reply') }}</span></button>
                  </div>
                </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Show Customer Query -->
@if($ticket->admin_id != NULL)
    <div class="box">
        <div class="row m-0 p-3 bg-E6">
            <div class="col-sm-1">
              <h5 class="f-14"><a href="{{ url(\Config::get('adminPrefix').'/users/edit/'. $ticket->user->id)}}">{{ ($ticket->user->first_name.' '.$ticket->user->last_name) }}</a></h5>

              @if (!empty($ticket->user->picture))
                <img alt="User profile picture" src="{{ url('public/user_dashboard/profile/'.$ticket->user->picture) }}" class="img-responsive img-circle asa img-fluid">
              @else
                <img alt="Default picture" src='{{url("public/uploads/userPic/default-image.png")}}' class="img-responsive img-circle asa img-fluid">
              @endif

            </div>
            <div class="col-sm-11">
                <p class="f-14 text-justify">{!! ucfirst($ticket->message) !!}</p>
                <hr class="hr-dotted">
            </div>
        </div>
        <div class="box-footer">
            {{-- <span><i class="fa fa-fw fa-clock-o"></i><small><i>{{date('d-m-Y h:i A', strtotime($ticket->created_at))}}</i></small></span> --}}
            <span class="f-14"><i class="fa fa-fw fa-clock-o"></i><small><i>{{ dateFormat($ticket->created_at) }}</i></small></span>
        </div>
    </div>
@else
  <!-- Show Admin Query -->
   <div class="box">
      <div class="row m-0 p-3 bg-F4">
        <div class="col-sm-11">
           <p class="f-14 mt-2 text-justify">{!! ucfirst($ticket->message) !!}</p>
           <hr class="hr-dotted">
        </div>
        <div class="col-sm-1 text-center">

          <span><a href="{{ url(\Config::get('adminPrefix').'/admin-user/edit/'. $ticket->admin->id)}}">{{ ($ticket->admin->first_name.' '.$ticket->admin->last_name) }}</a></span>

          @if (!empty($ticket->admin->picture))
            <img alt="Admin profile picture" src="{{ url('public/uploads/userPic/'.$ticket->admin->picture) }}" class=" img-responsive img-circle asa img-fluid">
          @else
            <img alt="Default picture" src='{{url("public/uploads/userPic/default-image.png")}}' class="img-responsive img-circle asa img-fluid">
          @endif

        </div>
      </div>
       <div class="box-footer">
          <span class="pull-right f-14"><i class="fa fa-fw fa-clock-o"></i><small><i>{{ dateFormat($ticket->created_at) }}</i></small></span>
      </div>
   </div>
@endif


@foreach($ticket_replies as $ticket_reply)
    <!-- Show Customer Reply -->
    @if($ticket_reply->user_type == 'user')
        <div class="box">
            <div class="row m-0 p-3 bg-E6">
              <div class="col-sm-1 text-center">

                  <h5 class="f-14"><a href="{{ url(\Config::get('adminPrefix').'/users/edit/'. $ticket_reply->user->id)}}">{{ ($ticket_reply->user->first_name.' '.$ticket_reply->user->last_name) }}</a></h5>

                  @if (!empty($ticket_reply->user->picture))
                    <img alt="User profile picture" src="{{ url('public/user_dashboard/profile/'.$ticket_reply->user->picture) }}" class="img-responsive img-circle asa img-fluid">
                  @else
                    <img alt="Default picture" src='{{url("public/uploads/userPic/default-image.png")}}' class="img-responsive img-circle asa img-fluid">
                  @endif

                  <hr class="hr-margin">
                    <form class="d-inline-block" action="{{ url(\Config::get('adminPrefix').'/tickets/reply/delete/') }}" accept-charset="UTF-8" method="POST">{{-- modal is in message_boxes.blade.php --}}
                        {{ csrf_field() }}

                        <input type="hidden" name="id" value="{{ $ticket_reply->id }}">
                        <input type="hidden" name="ticket_id" value="{{ $ticket_reply->ticket_id }}">

                        <button class="btn btn-xs btn-danger btn-flat" data-message="Are you sure you want to delete this reply?" data-bs-target="#confirmDelete" data-title="Delete Reply" data-bs-toggle="modal" title="Delete" type="button" id="customer_reply_button">Delete</button>
                    </form>
              </div>
              <div class="col-sm-10">
                 <p class="f-14 mt-2 text-justify">{!! ucfirst($ticket_reply->message) !!}</p>

                 <hr class="hr-dotted">

                 @if(optional($ticket_reply->file)->filename)
                     <a href="{{ url(\Config::get('adminPrefix').'/ticket/download', $ticket_reply->file->filename) }}" class="pull-right"><i class="fa fa-fw fa-download"></i>{{ optional($ticket_reply->file)->originalname }}</a>
                 @endif
              </div>
              <div class="col-sm-1">
                <span class="btn btn-xs btn-primary pull-right edit-btn" data-id="{{$ticket_reply->id}}" data-message="{{$ticket_reply->message}}" data-bs-toggle="modal" data-bs-target="#modal-default"><i class="fa fa-edit"></i></span>
              </div>
            </div>
            <div class="box-footer">
                <span class="f-14" ><i class="fa fa-fw fa-clock-o"></i><small><i>{{ dateFormat($ticket_reply->created_at) }}</i></small></span>
            </div>
        </div>
    @else
      <!--  Show Admin Reply -->
      <div class="box">
          <div class="row m-0 p-3 bg-F4">

              <div class="col-sm-1">
                <span class="btn btn-xs btn-primary btn-flat edit-btn mt-3" data-bs-toggle="modal" data-id="{{ $ticket_reply->id }}" data-message="{{ $ticket_reply->message }}" data-bs-target="#modal-default" ><i class="fa fa-edit"></i></span>
              </div>

              <div class="col-sm-10">
                  <p class="f-14 mt-2 text-justify"> {!! ucfirst($ticket_reply->message) !!} </p>
                  <hr class="hr-dotted">

                  @if(optional($ticket_reply->file)->filename)
                      <a href="{{ url(\Config::get('adminPrefix').'/ticket/download', $ticket_reply->file->filename) }}" class="pull-right"><i class="fa fa-fw fa-download"></i>{{ optional($ticket_reply->file)->originalname }}</a>
                  @endif
              </div>


              <div class="col-sm-1 text-center">
                  <h5 class="f-14"><a href="{{ url(\Config::get('adminPrefix').'/admin-user/edit/'. $ticket_reply->admin->id)}}">{{ ($ticket_reply->admin->first_name.' '.$ticket_reply->admin->last_name) }}</a></h5>

                  @if (!empty($ticket_reply->admin->picture))
                    <img alt="Admin profile picture" src="{{ url('public/uploads/userPic/'.$ticket_reply->admin->picture) }}" class=" img-responsive img-circle asa img-fluid">
                  @else
                    <img alt="Default picture" src='{{url("public/uploads/userPic/default-image.png")}}' class="img-responsive img-circle asa img-fluid">
                  @endif

                  <hr class="hr-margin">

                      <form class="d-inline-block" action="{{ url(\Config::get('adminPrefix').'/tickets/reply/delete') }}" accept-charset="UTF-8" method="POST">
                      {{ csrf_field() }}

                          <input type="hidden" name="id" value="{{ $ticket_reply->id }}">
                          <input type="hidden" name="ticket_id" value="{{ $ticket_reply->ticket_id }}">

                          <button class="btn btn-xs btn-danger btn-flat f-14" data-message="Are you sure you want to delete this reply?" data-bs-target="#confirmDelete" data-title="Delete Reply" data-bs-toggle="modal" title="Delete" type="button" id="admin_reply_button">Delete</button>

                      </form>
              </div>
          </div>

          <div class="box-footer">
              <span class="pull-right f-14"><i class="fa fa-fw fa-clock-o"></i><small><i>{{ dateFormat($ticket_reply->created_at) }}</i></small></span>
          </div>
      </div>
    @endif
@endforeach

<!-- Modal Start -->
<div class="modal fade" id="modal-default">
    <div class="modal-dialog">

        <form  method="POST" action="{{ url(\Config::get('adminPrefix').'/tickets/reply/update') }}" id="replyModal">
            {{ csrf_field() }}

            <input type="hidden" name="id" id="reply_id">

            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title f-18">{{ __('Update Reply') }}</h4>
                <a type="button" class="close f-18" data-bs-dismiss="modal">×</a>
              </div>

              <div class="modal-body">
                <div class="form-group row align-items-center">

                  <div class="modal_editor_textarea">
                      <textarea name="message" class="form-control f-14 editor h-200"></textarea>
                  </div>

                  <div id="error-message-modal"></div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-flat pull-left f-14 rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary btn-flat f-14 rounded">{{ __('Update') }}</button>
              </div>
            </div>
        </form>

    </div>
</div>
</div>

@endsection

@push('extra_body_scripts')
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    'use strict';
    var ticket_id = "{{ $ticket->id }}";
    var token = "{!! csrf_token() !!}";
    var extensions = JSON.parse(@json($extensions)).join('|');
    var extensionsMessage = JSON.parse(@json($extensions)).join(', ');
    var errorMessage = '{{ __("Please select (:x) file.") }}';
    var fileErrorMessage = errorMessage.replace(':x', extensionsMessage);
    var ticketStatusChangeUrl = "{{ url(\Config::get('adminPrefix').'/tickets/change_ticket_status') }}";
</script>
<script src="{{ asset('public/admin_dashboard/js/ticket/ticket.min.js') }}" type="text/javascript"></script>

@endpush

