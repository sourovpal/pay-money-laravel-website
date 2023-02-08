    <div class="py-3">
        <button class="navbar-toggler mr-4 text-white d-block d-lg-none btn btn-primary text-28" type="button"  id="sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <ul class="navbar-nav ml-auto pr-3">
        <li class="nav-item active">
            <div class="dropdown mt-2 pr-4">
                <a class="dropdown-toggle text-capitalize" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-globe pr-1 text-20 pt-1"></i> {{ !empty(\Session::get('dflt_lang')) ? \Session::get('dflt_lang') : 'En' }}
                </a>

                <div class="dropdown-menu mt-18" aria-labelledby="dropdownMenuButton" id="lang">
                    @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                        <a class="dropdown-item" href="#" value="{{ $lang->short_name }}">{{ $lang->name }}</a>
                    @endforeach
                </div>
            </div>
        </li>

        <li class="nav-item dropdown position-relative">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if(!empty(Auth::user()->picture))
                        <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}" class="w-30p rounded-circle" id="profileImageHeader">
                    @else
                        <i class="fas fa-user text-20 pt-1"></i>
                    @endif

                    <p class="side-text my-0 ml-2">{{ Auth::user()->first_name .' '.  Auth::user()->last_name}}</p>
                </a>

                <div class="dropdown-menu profile mt-2 p-0 pb-3 dropdown-menu-right">
                    <div class="bg-body">
                        <a class="dropdown-item py-3 d-flex align-items-center">
                            @if(!empty(Auth::user()->picture))
                                <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}" class="w-30p rounded-circle" id="profileImageHeaderdrop">
                            @else
                                <i class="fas fa-user text-20 pt-1"></i>
                            @endif

                            <p class="side-text my-0 ml-2">{{ Auth::user()->first_name .' '.  Auth::user()->last_name}}</p>
                        </a>
                    </div>
                    <a class="dropdown-item mt-2" href="{{url('/profile')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-30 w-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="pl-2 text-14"> {{ __('Settings') }}</span>
                     </a>
                    <a class="dropdown-item mt-2" href="{{ url('/logout') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-30 w-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>

                        <span class="pl-2 text-14">{{ __('Sign Out') }}</span>
                    </a>
                </div>



        </li>
    </ul>

