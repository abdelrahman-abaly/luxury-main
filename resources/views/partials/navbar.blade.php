<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white rounded-3 mb-4">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="theme-toggle">
            <span class="me-2 theme">Theme</span>
            <button class="theme-toggle-btn" onclick="toggleTheme()">
                <i class="fas fa-sun sun"></i>
                <i class="fas fa-moon moon"></i>
                <span class="toggle-thumb"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger rounded-pill">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">New order #1234</a></li>
                        <li><a class="dropdown-item" href="#">Lead follow up reminder</a></li>
                        <li><a class="dropdown-item" href="#">Commission payment received</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-envelope"></i>
                        <span class="badge bg-success rounded-pill">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Messages</h6></li>
                        <li><a class="dropdown-item" href="#">Customer inquiry</a></li>
                        <li><a class="dropdown-item" href="#">Support ticket</a></li>
                        <li><a class="dropdown-item" href="#">Team announcement</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="{{(auth()->user()->avatar != "" && auth()->user()->avatar != null)? asset('storage/' . auth()->user()->avatar) : 'https://randomuser.me/api/portraits/men/32.jpg'}}" alt="Profile" class="profile-img">
                        <span class="ms-2 d-none d-lg-inline navbar-username-text">{{auth()->user()->name}}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{auth()->user()->role->role_name}}</h6></li>
                        <li><a class="dropdown-item" href="{{route('dashboard')}}"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('post-logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        <form id="post-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
