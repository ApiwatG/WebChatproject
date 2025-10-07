<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> 
    
    </head>
<body>
    <div class="d-flex">
        
        <nav id="sidebar" class="d-md-block p-0 sidebar"> 
            <div class="position-sticky">
                <ul class="nav flex-column p-3">
                    
                    <li class="nav-item mb-3 border-bottom pb-3">
                        <div class="d-flex align-items-center">
                            <div style="width: 40px; height: 40px; background-color: #ced4da; border-radius: 50%;" class="me-2"></div> 
                            <div>
                                @foreach($admins as $admin)
                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $admin->name ?? 'Admin Name' }}</h6>
                                    <small class="text-muted">{{ $admin->email ?? 'admin@email.com' }}</small>
                                    @break
                                @endforeach
                            </div>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a class="text-decoration-none">
                            <button class="nav-link-item">Dashboard</button> 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('banuser') }}" class="text-decoration-none">
                        <button class="nav-link-item">Ban User</button></a>
                        </li>
                
                    
                </ul>
            </div>
        </nav>

        <div class="flex-grow-1">

            <div class="d-flex align-items-center p-3 bg-white border-bottom sticky-top">
                <div class="ms-auto d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#logoutModal">
                        Logout
                    </button>
                </div>
            </div>

            <div class="container-fluid">
    <div class="row">
        <main class="col-md-12 mx-auto main-content">
            <h2 class="mb-4 dashboard-title"> Admin Dashboard</h2>

            <div class="row mb-5">
                <div class="col-md-6 mb-3">
                    <div class="stat-card card-weekly shadow-sm">
                        <h4>Weekly New Users</h4>
                        <div class="display-4">{{ $weeklyNew ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="stat-card card-all shadow-sm">
                        <h4>All Users</h4>
                        <div class="display-4">{{ $allUsers ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="view-panel h-100">
                        <h5 class="mb-4 panel-title"> Top Game Players</h5>
                        <div class="panel-content-scroll">
                            <ol class="list-group list-group-numbered top-player-list">
                                @forelse($topPlayers as $player)
                                    <li class="list-group-item d-flex justify-content-between align-items-center list-player-item">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold player-name">{{ $player->name }}</div>
                                            <small class="text-muted">ID: {{ $player->user_id }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill play-time-badge">{{ $player->total_play_time }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted">No player data available.</li>
                                @endforelse
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Log Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" >Yes</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>