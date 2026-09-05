<div class="kiosk-page">
    <div class="kiosk-top"><a class="brand"><span class="brand-mark">A</span><span>Attendly<small>Self-service kiosk</small></span></a><div><span class="live-indicator">● System online</span><strong id="clock">{{ now()->format('h:i A') }}</strong><small>{{ now()->format('l, F d, Y') }}</small></div></div>
    <div class="kiosk-grid">
        <section class="kiosk-main">
            <span class="eyebrow light">Quick attendance</span><h1>Welcome to campus.</h1><p>Enter your student ID below to record your time in or time out.</p>
            <form wire:submit="submit"><label>Student ID number</label><div class="kiosk-input"><input wire:model="studentId" placeholder="e.g. 2026-0001" autofocus autocomplete="off"><button>Confirm <span>→</span></button></div>@error('studentId')<small class="error light">{{ $message }}</small>@enderror</form>
            @if($result)
                <div wire:key="kiosk-result-{{ $resultKey }}" class="kiosk-result {{ $result['type'] }}"><span>{{ $result['type']==='success'?'✓':'!' }}</span><div><b>{{ $result['title'] }}</b><p>{{ $result['message'] }}</p></div></div>
                @if($result['type']==='success')
                    <div wire:key="attendance-animation-{{ $resultKey }}" class="attendance-animation {{ $result['animation'] }}" aria-live="polite">
                        <div class="animation-rings"><i></i><i></i><i></i></div>
                        <div class="animation-icon"><span class="door">⌂</span><span class="moving-person">●</span><span class="motion-arrow">→</span></div>
                        <span class="animation-check">✓</span><strong>{{ $result['title'] }}</strong><h2>{{ $result['name'] }}</h2>
                        <p>{{ $result['animation']==='time-in' ? 'Welcome to campus!' : 'Have a wonderful day!' }}</p>
                    </div>
                @endif
            @endif
        </section>
        <aside class="kiosk-side"><div class="today-number"><span>{{ $todayCount }}</span><p>students present today</p></div><h3>Latest arrivals</h3><div class="activity kiosk-activity">@forelse($recent as $log)<div><span class="avatar">{{ strtoupper(substr($log->student->first_name,0,1).substr($log->student->last_name,0,1)) }}</span><p><b>{{ $log->student->full_name }}</b><small>{{ $log->time_in->format('h:i A') }}</small></p><span class="status-dot"></span></div>@empty<p class="empty">Be the first to check in.</p>@endforelse</div></aside>
    </div>
    <footer>Need help? Please approach the attendance desk.</footer>
</div>
<script>setInterval(()=>document.getElementById('clock').textContent=new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}),1000)</script>
