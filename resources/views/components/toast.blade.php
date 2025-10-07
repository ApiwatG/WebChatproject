{{-- resources/views/components/toast.blade.php --}}

<link rel="stylesheet" href="{{ asset('css/toast.css') }}">

<div class="toast-container" id="toast-container">
    @if(session('error'))
        <div class="toast toast-error" data-duration="3000">
            <span class="toast-icon">❌</span>
            <div class="toast-content">
                <div class="toast-title">Error</div>
                <div class="toast-message">{{ session('error') }}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if(session('success'))
        <div class="toast toast-success" data-duration="3000">
            <span class="toast-icon">✅</span>
            <div class="toast-content">
                <div class="toast-title">Success</div>
                <div class="toast-message">{{ session('success') }}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="toast toast-warning" data-duration="3000">
            <span class="toast-icon">⚠️</span>
            <div class="toast-content">
                <div class="toast-title">Warning</div>
                <div class="toast-message">{{ session('warning') }}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if(session('info'))
        <div class="toast toast-info" data-duration="3000">
            <span class="toast-icon">ℹ️</span>
            <div class="toast-content">
                <div class="toast-title">Info</div>
                <div class="toast-message">{{ session('info') }}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif
</div>

<script>
(function() {
    const toasts = document.querySelectorAll('.toast');
    
    toasts.forEach(toast => {
        const duration = parseInt(toast.dataset.duration) || 3000;
        
        setTimeout(() => {
            toast.remove();
        }, duration);
    });
})();
</script>