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

/**
 * Converts a HEX number into an array containing
 * the R, G and B components as values 0-255.
 * @param  string $hex  The hex string (# plus 6 characters)
 * @return array        An array containing the components
 */
function hexToRgb($hex)
{
    $return = [
        'r' => 0,
        'g' => 0,
        'b' => 0
    ];

    if (strlen($hex) != 7) return $return;

    $return['r'] = hexdec(substr($hex, 1, 2));
    $return['g'] = hexdec(substr($hex, 3, 2));
    $return['b'] = hexdec(substr($hex, 5, 2));

    return $return;
}

/**
 * Converts an RGB array (r,g,b) into a HEX string
 * @param  string $rgb  The rgb array
 * @return array        A string containing the hex string
 */
function rgbToHex($rgb)
{
    if (!is_array($rgb) || count($rgb) != 3) return '#000000';

    $r .= dechex($rgb[0]);
    if (strlen($r) == 1) $r = '0'.$r;

    $g .= dechex($rgb[1]);
    if (strlen($g) == 1) $g = '0'.$g;

    $b .= dechex($rgb[2]);
    if (strlen($b) == 1) $b = '0'.$b;

    return '#'.$r.$g.$b;
}