@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
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
                            <p class="text-16 font-weight-600 text-active">{{ __('Tickets list') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Sub title start -->
                    <div class="mt-2">
                        <h3 class="sub-title">{{ __('Latest tickets') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Make conversation with admin.') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>
            </div>


            <!--tickete list section start-->
            <div class="row bg-secondary rounded m-0 mt-4 shadow">
                <div class="table-responsive">
                    @if($tickets->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-left pl-5" width="16%">@lang('message.dashboard.ticket.ticket-no')</th>
                                <th class="text-left">@lang('message.dashboard.ticket.subject')</th>
                                <th width="15%">@lang('message.dashboard.ticket.status')</th>
                                <th width="6%">@lang('message.dashboard.ticket.priority')</th>
                                <th width="15%">@lang('message.dashboard.ticket.date')</th>
                                <th class="pr-5 text-right" width="6%">@lang('message.dashboard.ticket.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($tickets as $result)
                                <tr>
                                    <td class="text-left pl-5">{{ $result->code}} </td>
                                    <td class="text-left">
                                        <p class="font-weight-600">
                                            <a class="text-dark" href="{{ url('ticket/reply').'/'.$result->id }}">{{ $result->subject}}</a>
                                        </p>
                                    </td>
                                    <td>
                                        @php
                                            echo getStatusBadge($result->ticket_status->name);
                                        @endphp
                                    </td>
                                    <td>{{ $result->priority }} </td>
                                    <td>{{ dateFormat($result->created_at) }} </td>
                                    <td class="pr-5 text-right">
                                        <a href="{{ url('ticket/reply').'/'.$result->id }}" class="btn btn-light btn-sm"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="p-5 text-center">
                            <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                            <p class="mt-4">{{ __('Sorry!') }} @lang('message.dashboard.ticket.no-ticket')</p>
                        </div>
                    @endif
                </div>
            </div>
            <!--Tickets list end-->
        </div>
    </div>
</section>
@endsection
