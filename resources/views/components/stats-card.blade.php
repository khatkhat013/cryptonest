@props([
    'title', 
    'value', 
    'icon' => null,
    'trend' => null,
    'trendValue' => null,
    'color' => 'primary'
])

<div class="card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="text-muted mb-1 text-uppercase">{{ $title }}</h6>
                <h3 class="mb-0">{{ $value }}</h3>
            </div>
            @if($icon)
            <div class="rounded-circle p-3 bg-{{ $color }} bg-opacity-10">
                <i class="bi bi-{{ $icon }} text-{{ $color }} fs-5"></i>
            </div>
            @endif
        </div>
        
        @if($trend)
        <div class="mt-3">
            <span class="badge bg-{{ $trend === 'up' ? 'success' : 'danger' }} me-1">
                <i class="bi bi-arrow-{{ $trend }}"></i>
            </span>
            <small class="text-muted">
                {{ $trendValue }} from last period
            </small>
        </div>
        @endif
    </div>
</div>