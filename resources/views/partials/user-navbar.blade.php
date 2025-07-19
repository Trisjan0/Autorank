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
            <img src="{{ Auth::user()->avatar }}" alt="{{ auth()->user()->name }}'s profile picture">
            <h2>{{ auth()->user()->name }}</h2>
        </div>
    </div>
    <div id="hidden-menu">
        <a href="{{ route('dashboard') }}">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-house" style="color: #ffffff;"></i>
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
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button>
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