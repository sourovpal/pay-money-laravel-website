<div class="col-md-4">
    <div class="menu-list">
        <ul>
            <li class="{{ isset($menu) && ( $menu == 'dashboard' ) ? 'active' : '' }}"><a href="{{url('dashboard')}}">{{__('Dashboard') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'transactions' ) ? 'active' : '' }}"><a href="{{url('transactions')}}">{{__('Transactions') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'transfer' ) ? 'active' : '' }}"><a href="{{url('moneytransfer')}}">{{__('Money transfer') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'exchanges' ) ? 'active' : '' }}"><a href="{{url('exchanges')}}">{{__('Currency exchange') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'request_payment' ) ? 'active' : '' }}"><a href="{{url('request_payments')}}">{{__('Request payment') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'merchant' ) ? 'active' : '' }}"><a href="{{url('merchants')}}">{{__('Merchants') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'merchant_payment' ) ? 'active' : '' }}"><a href="{{url('merchant/payments')}}">{{__('Merchant Payments') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'payouts' ) ? 'active' : '' }}"><a href="{{url('payouts')}}">{{__('Payouts') }}</a></li>


            <li class="{{ isset($menu) && ( $menu == 'dispute' ) ? 'active' : '' }}"><a href="{{url('disputes')}}">{{__('Disputes') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'ticket' ) ? 'active' : '' }}"><a href="#">{{__('Tickets') }}</a></li>

            <li class="{{ isset($menu) && ( $menu == 'account_setting' ) ? 'active' : '' }}"><a href="#">{{__('Account settings') }}</a></li>
        </ul>
    </div>
</div>
