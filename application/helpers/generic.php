<?php namespace GenericHelpers;

function objectVal($object, $key, $default = null)
{
    if (is_array($object))
    {
        if (array_key_exists($key, $object)) return $object[$key];
    }
    else
    {
        if (isset($object->$key)) return $object->$key;
    }

    return $default;
}

function br2nl($text)
{
    return str_replace(['<br>', '<br/>', '<br />'], "\n", $text);
}