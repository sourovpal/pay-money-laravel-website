<div class="box box-primary">

  {{-- normal template --}}
  <div class="box-header with-border">
    <h3 class="box-title underline">{{ __('SMS Templates') }}</h3>
  </div>
  <div class="box-body no-padding d-inline-block">
    <ul class="nav nav-pills nav-stacked row">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-21' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/21")}}">{{ __('Identity') }}/{{ __('Address') }} {{ __('Verification') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-1' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/1")}}">{{ __('Transferred Payments') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-2' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/2")}}">{{ __('Received Payments') }}</a>
      </li>


      <li {{ isset($list_menu) &&  $list_menu == 'menu-4' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/4")}}">{{ __('Request Payment Creation') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-5' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/5")}}">{{ __('Request Payment Acceptance') }}</a>
      </li>
      @if(config('referral.is_active'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-33' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/33")}}">{{ __('Referral Award') }}</a>
        </li>
      @endif

    </ul>
  </div>
</div>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title underline">{{ __('SMS Templates of Admin actions') }}</h3>
  </div>
  <div class="box-body no-padding d-inline-block">
    <ul class="nav nav-pills nav-stacked row">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-14' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/14")}}">{{ __('Merchant Payment') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-10' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/10")}}">{{ __('Payout') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-6' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/6")}}">{{ __('Transfers') }}</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-8' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/8")}}">{{ __('Request Payments') }} ({{ __('Success') }}/{{ __('Refund') }})</a>
      </li>


      <li {{ isset($list_menu) &&  $list_menu == 'menu-16' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/16")}}">{{ __('Request Payments') }} ({{ __('Cancel') }}/{{ __('Pending') }})</a>
      </li>

      @if(isActive('CryptoExchange'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-36' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/36") }}">{{ __('Crypto Exhange Notification') }} ({{ __('Success') }}/
          {{ __('Cancel') }})</a>
      </li>
      @endif

    </ul>
  </div>
  </div>
