<!-- Colored Header Component -->
<div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ $backUrl }}" class="text-white">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="mb-0 text-white">{{ $title }}</h3>
            @if(isset($rightContent))
                {{-- Escape rightContent to prevent XSS. If HTML is intentionally required, pass an instance of HtmlString from the controller after sanitizing it. --}}
                {{ $rightContent }}
            @else
                <div style="width: 24px;"></div> <!-- Spacer for centering -->
            @endif
        </div>

        @if(isset($subtitle))
            <div class="text-center mt-4">
                <p class="mb-0">{{ $subtitle }}</p>
            </div>
        @endif

        @if(isset($slot))
            <div class="mt-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>