@if (!empty($uiResponse) && $uiResponse instanceof \App\Dorcas\Hub\Utilities\UiResponse\UiResponseInterface)
    {!! $uiResponse->toHtml() !!}
@elseif (count($errors) > 0)
    {!! (tabler_ui_html_response($errors->all())->setType(\App\Dorcas\Hub\Utilities\UiResponse\UiResponse::TYPE_ERROR))->toHtml() !!}
@endif
