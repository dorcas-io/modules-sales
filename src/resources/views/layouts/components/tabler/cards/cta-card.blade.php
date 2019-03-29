<div class="card">
    @if (!empty($status))
        <div class="card-status {{ !empty($sideStatus) ? 'card-status-left' : '' }} bg-{{ $status }}"></div>
    @endif
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
        <div class="card-options">
            {!! $buttons !!}
        </div>
    </div>
    <div class="card-body">
        {!! $slot !!}
    </div>
    {!! $footer or '' !!}
</div>
