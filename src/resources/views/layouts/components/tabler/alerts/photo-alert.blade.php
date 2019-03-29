<div class="alert alert-avatar alert-{{ !empty($type) ? $type : 'primary' }} {{ !empty($dismissable) ? 'alert-dismissible' : '' }}" role="alert">
    <span class="avatar" style="background-image: url({{ $photoUrl }})"></span>
    {!! $slot !!}
    {!! !empty($dismissable) ? '<button type="button" class="close" data-dismiss="alert"></button>' : '' !!}
</div>
