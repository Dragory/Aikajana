<?php

Form::macro('utf8fix', function()
{
    return '<input type="hidden" name="utf8fix" value="&#9760;">';
});

Form::macro('start', function($action = '', $method = 'post', $csrf = true, $encoding = 'UTF-8')
{
    $return = '<form action="'.$action.'" method="'.$method.'" accept-encoding="'.$encoding.'">';
    if ($csrf) $return .= Form::token();
    if ($encoding == 'UTF-8') $return .= Form::utf8fix();

    return $return;
});

Form::macro('end', function()
{
    return '</form>';
});