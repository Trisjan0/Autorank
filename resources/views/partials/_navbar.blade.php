<nav>
    <div class="navbar-container">
        <div class="navbar-left-side">
            <i id="menu-toggle-button" class="fa-solid fa-bars" style="color: #ffffff;"></i>
            <div class="navbar-logo-container">
                <img src="{{ asset('images/autorank_logo.png') }}" alt="website logo">
                <h1>AutoRank</h1>
            </div>
        </div>
        <div class="navbar-right-side">
            @auth
            <img src="{{ Auth::user()->avatar }}" alt="{{ auth()->user()->name }}'s profile picture">
            <h2>
                {{ auth()->user()->name }}
                <b>
                    [
                    {{-- Dynamically display the user's current role --}}
                    @php
                    $currentUserRoleForDisplay = 'Guest'; // Default fallback

                    // Re-fetch the user from the database directly for the navbar.
                    // This diagnostic step helps confirm the latest role from DB.
                    $navbarUser = \App\Models\User::with('roles')->find(Auth::id());

                    if ($navbarUser && $navbarUser->roles->isNotEmpty()) {
                    $currentUserRoleForDisplay = $navbarUser->roles->first()->name;
                    }
                    @endphp
                    {{ Str::title(str_replace('_', ' ', $currentUserRoleForDisplay)) }}
                    ]
                </b>
            </h2>
            @endauth
        </div>
    </div>
    <div id="hidden-menu">
        {{-- Always show Dashboard, Profile, Settings, and Logout for any authenticated user --}}
        <a href="{{ route('dashboard') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-chart-simple" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Dashboard</p>
            </div>
        </a>
        <a href="{{ route('profile-page') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-address-book" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Profile</p>
            </div>
        </a>

        {{-- ADMIN/SUPER_ADMIN specific links --}}
        @auth
        {{-- Using @can for permission management with Spatie --}}
        @can('manage users')
        <a href="{{ route('application-page') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-file-circle-check" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Review Applications</p>
            </div>
        </a>
        <a href="{{ route('manage-users') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-users" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Manage Users</p>
            </div>
        </a>
        @endcan

        @can('view research documents')
        <a href="{{ route('research-documents-page') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-file-lines" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Research Documents</p>
            </div>
        </a>
        @endcan

        @can('view evaluations')
        <a href="{{ route('evaluations-page') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-chart-pie" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Evaluations</p>
            </div>
        </a>
        @endcan

        @can('manage events')
        <a href="{{ route('event-participations-page') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-calendar-day" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Event Participations</p>
            </div>
        </a>
        @endcan
        @endauth

        <a href="{{ route('system-settings') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-gear" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Settings</p>
            </div>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">
                <div class="button-menu-icon">
                    <i class="fa-solid fa-key" style="color: #ffffff;"></i>
                </div>
                <div class="button-menu-title">
                    <p>Log Out</p>
                </div>
            </button>
        </form>
    </div>
</nav>