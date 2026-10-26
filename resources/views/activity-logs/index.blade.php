<x-app-layout>
    <x-slot name="breadcrumb"><span class="current">Activity Log</span></x-slot>

    <div class="page-heading"><div><h1 class="page-title">Activity Log</h1><p class="page-subtitle">Jejak aktivitas penting pengguna pada sistem.</p></div></div>

    <div class="card">
        <div class="card-header">
            <h2>Aktivitas <span class="badge badge-secondary">{{ number_format($activityLogs->total(), 0, ',', '.') }}</span></h2>
            <form method="GET" action="{{ route('activity-logs.index') }}" class="filter-form">
                <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau pengguna..." autofocus>
                <select class="form-control" name="user_id" aria-label="Filter pengguna"><option value="">Semua pengguna</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>@endforeach</select>
                <input class="form-control" name="action" value="{{ request('action') }}" placeholder="Jenis aksi...">
                <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i> Cari</button>
                @if(request()->filled('search') || request()->filled('user_id') || request()->filled('action'))<a class="btn btn-secondary btn-sm" href="{{ route('activity-logs.index') }}">Reset</a>@endif
            </form>
        </div>
        <div class="table-wrapper">
            @if($activityLogs->isEmpty())
                <div class="empty-state"><i class="fas fa-scroll"></i><p>Belum ada aktivitas yang sesuai.</p></div>
            @else
                <table class="inv-table"><thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th></tr></thead><tbody>
                    @foreach($activityLogs as $activity)
                        <tr><td>{{ $activity->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}</td><td>{{ $activity->user?->name ?? 'Akun tidak tersedia' }}</td><td><code>{{ $activity->action }}</code></td><td>{{ $activity->description }}</td></tr>
                    @endforeach
                </tbody></table>
            @endif
        </div>
        @if($activityLogs->hasPages())<div class="pagination-footer"><p>Menampilkan {{ number_format($activityLogs->firstItem(), 0, ',', '.') }}–{{ number_format($activityLogs->lastItem(), 0, ',', '.') }} dari {{ number_format($activityLogs->total(), 0, ',', '.') }} aktivitas</p>{{ $activityLogs->links('pagination::bootstrap-5') }}</div>@endif
    </div>
</x-app-layout>
