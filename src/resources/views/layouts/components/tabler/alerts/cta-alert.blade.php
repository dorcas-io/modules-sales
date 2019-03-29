<div class="alert alert-{{ !empty($type) ? $type : 'primary' }} {{ !empty($dismissable) ? 'alert-dismissible' : '' }}" role="alert">
    {!! !empty($dismissable) ? '<button type="button" class="close" data-dismiss="alert"></button>' : '' !!}
    <h4>{{ !empty($title) ? $title : 'Heads Up!' }}</h4>
    <p>{!! $slot !!}</p>
    {!! $buttons !!}
</div>
