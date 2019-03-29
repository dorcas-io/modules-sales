<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $slot !!}
            </div>
            {!! !empty($footer) ? $footer : '' !!}
        </div>
    </div>
</div>