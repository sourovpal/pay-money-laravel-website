@php
	$extensions = json_encode(getFileExtensions(1));
@endphp

@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid" id="dispute-discussion">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Disputes') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="mt-5 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/disputes') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">@lang('message.dashboard.dispute.discussion.form.title')</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-3">
                <h3 class="sub-title"></h3>
                <p class="text-gray-500 text-16">{{ __('See details conversation') }}</p>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 col-xl-3">
                    <!-- Sub title start -->
                    <div class="mt-4 p-4 bg-secondary rounded shadow">
						<div>
							<h3 class="font-weight-600 text-18 text-active">@lang('message.dashboard.dispute.discussion.sidebar.header')</h3>
						</div>

						<div class="mt-3">
							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.dispute-id')</div>
								<div class="generalcolor-txt">{{ $dispute->code }}</div>
							</div>

							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.discussion.sidebar.title')</div>
								<div class="generalcolor-txt">{{ $dispute->title }}</div>
							</div>

							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.claimant')</div>
								<div class="generalcolor-txt">{{ $dispute->claimant->first_name .' '.$dispute->claimant->last_name}}</div>
							</div>


							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.defendant')</div>
								<div class="generalcolor-txt">{{ $dispute->defendant->first_name .' '.$dispute->defendant->last_name}}</div>
							</div>

							<div class="mt-2">
								<div class="font-weight-600">@lang('message.form.date')</div>
								<div class="generalcolor-txt">{{ dateFormat($dispute->created_at) }}</div>
							</div>

							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.transaction-id')</div>
								<div class="generalcolor-txt">{{ $dispute->transaction->uuid }}</div>
							</div>

							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.status')</div>
								<div class="generalcolor-txt">
									@php
										echo getStatusBadge($dispute->status);
									@endphp
								</div>
							</div>

							<div class="mt-2">
								<div class="font-weight-600">@lang('message.dashboard.dispute.discussion.sidebar.reason')</div>
								<div class="generalcolor-txt">{{ $dispute->reason->title }}</div>
							</div>

							<div class="ticket-btn ticket-line mt-3">
								@if($dispute->claimant_id == Auth::user()->id)
									@if ($dispute->status == 'Open')
										<label class="font-weight-600"> @lang('message.dashboard.dispute.discussion.sidebar.change-status')</label>
										<select class="form-control" name="status" id="status">
											<option value="Open" <?= ($dispute->status == 'Open') ? 'selected' : '' ?>>@lang('message.dashboard.dispute.status-type.open')</option>
											<option value="Closed" <?= ($dispute->status == 'Closed') ? 'selected' : '' ?>>@lang('message.dashboard.dispute.status-type.close')</option>
										</select>
									@endif
								@endif
									<input type="hidden" name="id" value="{{$dispute->id}}" id="id">
							</div>
						</div>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-md-8 col-xl-9">
                    <div class="bg-secondary m-0 mt-4 p-35 shadow">
						<div>
							@include('user_dashboard.layouts.common.alert')
							<span id="alertDiv"></span>

							@if($dispute->status == 'Open')
								<form action="{{url('dispute/reply')}}" id="reply" method="post" enctype="multipart/form-data">
									<input type="hidden" name="dispute_id" value="{{ $dispute->id }}">
									{{ csrf_field() }}
									<div class="form-group">
										<label>@lang('message.dashboard.dispute.discussion.form.message')</label>
										<textarea name="description" id="description" class="form-control" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"></textarea>
										@if($errors->has('description'))
											<span class="error">{{ $errors->first('description') }}</span>
										@endif
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<input type="file" name="file" id="file" class="upload-text border rounded p-1"/>
												<span class="file-error" id="fileSpan"></span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="text-right">
												<button class="btn btn-primary px-4 py-2" id="dispute-reply">
													<i class="spinner fa fa-spinner fa-spin d-none"></i>
													<span id="dispute-reply-text" class="font-weight-bolder">
														@lang('message.dashboard.button.submit')
													</span>
												</button>
											</div>
										</div>
									</div>
								</form>
							@endif
							<div>
								<div class="px-4">
									<div class="d-flex">
										<div class="pr-2">
											@if(!empty($dispute->claimant->picture) && file_exists(public_path('user_dashboard/profile/' . $dispute->claimant->picture)))
												<img src="{{ url('public/user_dashboard/profile/' . $dispute->claimant->picture) }}" alt="user" class="rounded-circle" style="width:60px;">
											@else
												<img src="{{ url('public/user_dashboard/images/avatar.jpg') }}" alt="user" class="rounded-circle" style="width:60px;">
											@endif
										</div>
										<div>
											<p class="font-weight-600">{{ $dispute->claimant->first_name .' '.$dispute->claimant->last_name }}</p>
											<p class="text-12 text-gray-500">{{ dateFormat($dispute->created_at) }}</p>
										</div>
									</div>
									<div class="pl-5 mt-3 ml-2">
										<p>{!! $dispute->description !!}</p>
									</div>
								</div>
							</div>
							<br>
							@if( $dispute->disputeDiscussions->count() > 0 )
								@foreach($dispute->disputeDiscussions as $result)
									@if($result->type == 'User' )
                                        <div class="px-4">
                                            <div class="d-flex">
                                                <div class="pr-2">
                                                    @if(!empty($result->user->picture) && file_exists(public_path('user_dashboard/profile/' . $result->user->picture)))
														<img src="{{ url('public/user_dashboard/profile/' . $result->user->picture) }}" class="rounded-circle" style="width:60px;">
													@else
														<img src="{{ url('public/user_dashboard/images/avatar.jpg') }}" alt="User Image" class="rounded-circle" style="width:60px;">
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
													<?php
														$str_arr = explode('_', $result->file);
														$str_position = strlen($str_arr[0])+1;
														$file_name = substr($result->file,$str_position);
													?>
													<div class="mt-3">
														<a class="text-info rounded-lg p-1 bg-light" href="{{ url('/dispute/download',$result->file)}}"><i class="fa fa-download text-active"></i> {{ $file_name }}</a>
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
                                                    <p class="font-weight-600"><?php echo $result->admin->first_name.' '.$result->admin->last_name ?></p>
                                                    <p class="text-12 text-gray-500">{{ dateFormat($result->created_at) }}</p>
                                                </div>
                                            </div>

                                            <div class="pl-5 mt-3 ml-2">
                                                <p>{!! $result->message !!}</p>
                                                @if($result->file)
													<?php
														$str_arr = explode('_', $result->file);
														$str_position = strlen($str_arr[0])+1;
														$file_name = substr($result->file,$str_position);
													?>
                                                    <div class="mt-3">
                                                        <a class="text-info rounded-lg p-1 bg-light" href="{{ url('/dispute/download',$result->file)}}"><i class="fa fa-download text-active"></i> {{ $file_name }}</a>
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
	var requirdFieldText = '{{ __("This field is required.") }}';
	var submittingText = '{{ __("Submitting...") }}';
	var statusChangeText = '{{ __("Dispute discussion :x successfully.") }}';
</script>
<script src="{{ asset('public/user_dashboard/js/dispute/dispute_discussion.min.js')}}" type="text/javascript"></script>
@endsection
