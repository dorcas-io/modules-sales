<div class="alert alert-icon alert-{{ !empty($type) ? $type : 'primary' }} {{ !empty($dismissable) ? 'alert-dismissible' : '' }}" role="alert">
    <i class="fe {{ !empty($icon) ? $icon : 'fe-bell' }} mr-2" aria-hidden="true"></i> {!! $slot !!}
    {!! !empty($dismissable) ? '<button type="button" class="close" data-dismiss="alert"></button>' : '' !!}
</div>
