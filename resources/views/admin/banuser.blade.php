<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}"> 
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
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <button class="nav-link-item">Dashboard</button> 
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link-item active-link">Ban User</button> 
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
            

            <main class="main-content">
                
                <h4 class="mb-4">List of All Reports</h4>

                <div class="view-panel">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 table table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offender</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Take Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $report->id }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-semibold">
                                                {{ $report->reporterParticipant->user->name ?? 'Deleted User' }}
                                            </div>
                                            <div class="text-xs text-muted">
                                                ID: {{ $report->reporterParticipant->user->id ?? 'N/A' }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-semibold text-danger">
                                                {{ $report->offenderParticipant->user->name ?? 'Deleted User' }}
                                            </div>
                                            <div class="text-xs text-muted">
                                                ID: {{ $report->offenderParticipant->user->id ?? 'N/A' }}
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 text-sm text-gray-500" style="max-width: 250px;">
                                            <div class="bg-light p-2 rounded" style="font-style: italic;">
                                                "{{ $report->Report_message ?? 'No specific message reported' }}"
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 text-sm text-gray-500" style="max-width: 250px;">
                                            {{ $report->message ?? 'No reason provided' }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('report.dismiss', $report->id) }}" method="POST" class="d-inline mb-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary">
                                                    DISMISS
                                                </button>
                                            </form>
                                            
                                            @if($report->offenderParticipant && $report->offenderParticipant->user)
                                                <form action="{{ route('ban', $report->offenderParticipant->user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Ban user {{ $report->offenderParticipant->user->name }}?')">
                                                        BAN USER
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No reports available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <h4 class="mb-4 mt-5">List of Banned Users</h4>
                <div class="view-panel">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 table table-striped">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium">ID USER</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium">NAME</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium">Banned Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $banned = \App\Models\User::where('is_active', 0)->get();
                                @endphp
                                @forelse ($banned as $user)
                                    <tr>
                                        <td class="px-6 py-4 text-sm">{{ $user->id }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold">{{ $user->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $user->email ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $user->updated_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <form action="{{ route('unban', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Unban user {{ $user->name }}?')">
                                                    UNBAN
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">There are no banned users.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
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
                        <button type="submit" class="btn btn-danger">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>