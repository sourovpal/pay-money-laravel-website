
<!-- temp-9, temp-15 and temp-20 - not in database, can be used later-->

<!-- start temp ID = 1 and ending temp-22, we should add from temp-23-->

<div class="box box-primary">

  {{-- normal template --}}
  <div class="box-header with-border">
    <h3 class="box-title underline">{{ __('Email Templates') }}</h3>
  </div>
  <div class="box-body no-padding d-inline-block">
    <ul class="nav nav-pills nav-stacked row">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-17' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/17")}}">{{ __('Email Verification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-19' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/19")}}">{{ __('2-Factor Authentication') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-21' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/21")}}">{{ __('Identity') }}/{{ __('Address') }} {{ __('Verification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-18' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/18")}}">{{ __('Password Reset') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-1' ? 'class=active' : ''}} ><!--1-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/1")}}">{{ __('Transferred Payments') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-2' ? 'class=active' : ''}} ><!--2-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/2")}}">{{ __('Received Payments') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-4' ? 'class=active' : ''}} ><!--4-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/4")}}">{{ __('Request Payment Creation') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-5' ? 'class=active' : ''}} ><!--5-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/5")}}">{{ __('Request Payment Acceptance') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-32' ? 'class=active' : ''}} ><!--6-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/32")}}">{{  __("Cancel Request Payment (:x)", ["x" => __("Receiver")])  }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-34' ? 'class=active' : ''}} ><!--6-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/34")}}">{{  __("Cancel Request Payment (:x)", ["x" => __("Creator")])  }}</a>
      </li>
      
      <li {{ isset($list_menu) &&  $list_menu == 'menu-11' ? 'class=active' : ''}} ><!--11-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/11")}}">{{ __('Ticket') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-12' ? 'class=active' : ''}} ><!--12-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/12")}}">{{ __('Ticket Reply') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-13' ? 'class=active' : ''}} ><!--13-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/13")}}">{{ __('Dispute Reply') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-30' ? 'class=active' : ''}} ><!--13-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/30")}}">{{ __('Deposit via Admin') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-31' ? 'class=active' : ''}} ><!--13-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/31")}}">{{ __('Payout via Admin') }}</a>
      </li>

      @if(config('referral.is_active'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-32' ? 'class=active' : ''}} ><!--13-->
          <a href="{{ URL::to(\Config::get('adminPrefix')."/template/32")}}">{{ __('Referral Award') }}</a>
        </li>
      @endif

    </ul>
  </div>
</div>

<div class="box box-primary">
  {{-- Status template --}}
  <div class="box-header with-border">
    <h3 class="box-title underline">{{ __('Email Templates of Admin actions') }}</h3>
  </div>
  <div class="box-body no-padding d-inline-block">
    <ul class="nav nav-pills nav-stacked row">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-29' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/29")}}">{{ __('User Status Change') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-14' ? 'class=active' : ''}} ><!--14-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/14")}}">{{ __('Merchant Payment') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-10' ? 'class=active' : ''}} ><!--10-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/10")}}">{{ __('Payout') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-6' ? 'class=active' : ''}} ><!--6-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/6")}}">{{ __('Transfers') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-8' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/8")}}">{{ __('Request Payments') }} ({{ __('Success') }}/{{ __('Refund') }})</a><!--8-->
      </li>
      <li {{ isset($list_menu) &&  $list_menu == 'menu-16' ? 'class=active' : ''}} > <!--15-->
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/16")}}">{{ __('Request Payments') }} ({{ __('Cancel') }}/{{ __('Pending') }})</a>
      </li>
      @if(isActive('CryptoExchange'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-35' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/template/35") }}">{{ __('Crypto Exhange Notification') }} ({{ __('Success') }}/
          {{ __('Cancel') }})</a>
        </li>
      @endif

      @if(isActive('Investment'))
        <li {{ isset($list_menu) && $list_menu == 'menu-45' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/template/45") }}">{{ __('Investment Notification') }} ({{ __('Approve') }}/{{ __('Cancel') }})</a>
        </li>
        <li {{ isset($list_menu) && $list_menu == 'menu-46' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/template/46") }}">{{ __('Investment Mature') }}</a>
        </li>
      @endif
    </ul>
  </div>
</div>

<div class="box box-primary">
  {{-- Status template --}}
  <div class="box-header with-border">
    <h3 class="box-title underline">{{ __('Admin Notifications') }}</h3>
  </div>
  <div class="box-body no-padding d-inline-block">
    <ul class="nav nav-pills nav-stacked row">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-23' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/23")}}">{{ __('Deposit Notification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-24' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/24")}}">{{ __('Payout Notification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-25' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/25")}}">{{ __('Exchange Notification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-26' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/26")}}">{{ __('Transfer Notification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-27' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/27")}}">{{ __('Request Acceptance Notification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-28' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/template/28")}}">{{ __('Payment Notification') }}</a>
      </li>

      @if(isActive('CryptoExchange'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-34' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/template/34") }}">{{ __('Crypto Exhange Notification') }}</a>
        </li>
      @endif
      @if(isActive('Investment'))
        <li {{ isset($list_menu) && $list_menu == 'menu-44' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/template/44") }}">{{ __('Investment Notification') }}</a>
        </li>
      @endif
    </ul>
  </div>
</div>
