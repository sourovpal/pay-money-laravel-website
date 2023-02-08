<li class="nav-item">
    <a class="nav-link {{ $user_tab_menu == 'user_profile' ? 'active' : '' }}" href="{{ url(\Config::get('adminPrefix'). '/users/edit' , $users->id) }}">{{ __('Profile') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ $user_tab_menu == 'user_transactions' ? 'active' : '' }}" href="{{ url(\Config::get('adminPrefix')."/users/transactions", $users->id) }}">{{ __('Transactions') }}</a>
</li>
<li class="nav-item" >
    <a class="nav-link {{ $user_tab_menu == 'user_wallets' ? 'active' : '' }}" href="{{ url(\Config::get('adminPrefix')."/users/wallets", $users->id) }}">{{ __('Wallets') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ $user_tab_menu == 'user_tickets' ? 'active' : '' }}" href="{{ url(\Config::get('adminPrefix')."/users/tickets", $users->id) }}">{{ __('Tickets') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ $user_tab_menu == 'user_disputes' ? 'active' : '' }}" href="{{ url(\Config::get('adminPrefix')."/users/disputes", $users->id) }}">{{ __('Disputes') }}</a>
</li>
@if (config('referral.is_active') && count($users->referral_award_awarded_user) > 0)
    <li class="nav-item">
        <a class="nav-link {{ $user_tab_menu == 'user_profile' ? 'active' : '' }}" href='{{ url(\Config::get("adminPrefix")."/users/referral-awards" . $users->id) }}'>{{ __('Referral Awards') }}</a>
    </li>
@endif