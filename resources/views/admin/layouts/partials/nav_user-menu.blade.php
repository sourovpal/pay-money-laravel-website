<li class="dropdown user user-menu">
    <a href="javascript:void(0)" class="f-14 text-decoration-none me-3" data-bs-toggle="dropdown">
        <img src={{$admin_image}} class="user-image" alt="{{ __('User Image') }}"><!-- User image -->
        <span class="hidden-xs">{{ ucwords($admin_name)}}</span>
    </a>

    <ul class="dropdown-menu mt-3">
        <!-- User image -->
        <li class="user-header">
            <img src={{$admin_image}} class="img-circle mt-3" alt="{{ __('User Image') }}">
            <p>
                <small>{{ __('Email') }}: {{$admin_email}}</small>
            </p>
        </li>

        <li class="user-footer py-3">
            <div class="pull-left">
                <a href="{{ url(\Config::get('adminPrefix').'/profile') }}" class="profile-btn">{{ __('Profile') }}</a>
            </div>
            <div class="pull-right">
                <a href="{{ url(\Config::get('adminPrefix').'/adminlogout') }}" class="profile-btn">{{ __('Sign out') }}</a>
            </div>
        </li>
    </ul>
</li>
