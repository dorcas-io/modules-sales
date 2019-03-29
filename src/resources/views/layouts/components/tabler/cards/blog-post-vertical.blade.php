<div class="card">
    <a href="{{ $href or '#' }}">
        <img class="card-img-top" src="{{ $imageUrl }}" alt="{{ $title }}">
    </a>
    <div class="card-body d-flex flex-column">
        <h4><a href="{{ $href or '#' }}">{{ $title }}</a></h4>
        <div class="text-muted">{!! $slot !!}</div>
        {!! $footer or '' !!}
    </div>
</div>
