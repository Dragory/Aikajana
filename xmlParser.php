<?php

require_once('application/helpers/generic.php');

/**
 * Parses the given previous-format event XML
 * into SQL values.
 */

error_reporting(E_ALL);

$xml = file_get_contents('events.xml');

$parser = new SimpleXMLElement($xml);
// echo '<pre>'.htmlentities($parser->asXML(), ENT_QUOTES, 'UTF-8').'</pre>';

// If we encounter any "extra" colours (not defined in "colours"),
// save them here.
$coloursByColour = [];
$colourNum = 0;

// Colours
echo 'INSERT INTO colours VALUES ';

$values = [];
$coloursByName = [];
foreach ($parser->colours->colour as $colour)
{
    $rgb = explode(',', (string)$colour->RGB);
    $hex = \GenericHelpers\rgbToHex($rgb);

    $values[] = '('.$colour->id.', 1, \''.$colour->name.'\', \''.$hex.'\', \''.$colour->RGB.'\')';
    $coloursByName[$colour->name] = $colour;

    $colourNum = max($colourNum, intval($colour->id));
}
echo implode(',', $values).';';

// Groups
echo '<br><br>';
echo 'INSERT INTO groups VALUES ';

$values = [];
foreach ($parser->groups->group as $group)
{
    $values[] = '('.$group->id.', 1, \''.$group->name.'\', \'#000000\', \'0,0,0\')';
}
echo implode(',', $values).';';

// Events
echo '<br><br>';
echo 'INSERT INTO events VALUES ';

$colourNum++; // Make sure we're not overwriting any existing colours

$values = [];
foreach ($parser->events->event as $event)
{
    $event->group = intval($event->group) + 1;

    // Find out the colour we should be using
    // and create a new one if that doesn't exist.
    if (substr($event->colour, 0, 1) == '#')
    {
        if (isset($coloursByName[substr($event->event_colour, 1)]))
        {
            $colour = $coloursByName[substr($event->event_colour, 1)];
        }
        else
        {
            if (isset($coloursByColour['0,0,0'])) $colour = $coloursByColour['0,0,0'];
            else
            {
                $colour = new stdClass;
                $colour->id = $colourNum;
                $colour->id_group = $event->group;
                $colour->name = 'NewColour'+$colourNum;
                $colour->hex = '#000000';
                $colour->rgb = '0,0,0';

                $coloursByColour[$colour->rgb] = $colour;
                $colourNum++;
            }
        }
    }
    else
    {
        if (isset($coloursByColour[(string)$event->colour])) $colour = $coloursByColour[(string)$event->colour];
        else
        {
            $colour = new stdClass;
            $colour->id = $colourNum;
            $colour->id_group = $event->group;
            $colour->name = 'NewColour'+$colourNum;

            $rgb = explode(',', (string)$event->colour);
            $hex = \GenericHelpers\rgbToHex($rgb);

            $colour->rgb = (string)$event->colour;
            $colour->hex = $hex;

            $coloursByColour[$colour->rgb] = $colour;
            $colourNum++;
        }
    }

    $sqlStart = explode('.', $event->start);
    $sqlStart = $sqlStart[2].'-'.$sqlStart[1].'-'.$sqlStart[0];

    $sqlEnd = explode('.', $event->end);
    $sqlEnd = $sqlEnd[2].'-'.$sqlEnd[1].'-'.$sqlEnd[0];

    $valueArray = [];
    $valueArray[] = 'NULL';
    $valueArray[] = $event->group;
    $valueArray[] = $colour->id;
    $valueArray[] = "'".\GenericHelpers\objectVal($event, 'name')."'";
    $valueArray[] = "'".\GenericHelpers\objectVal($event, 'shortname')."'";
    $valueArray[] = 0;
    $valueArray[] = "'".$sqlStart."'";
    $valueArray[] = "'".$sqlEnd."'";
    $valueArray[] = "'".\GenericHelpers\objectVal($event, 'start_visible')."'";
    $valueArray[] = "'".\GenericHelpers\objectVal($event, 'end_visible')."'";
    $valueArray[] = (\GenericHelpers\objectVal($event, 'start_unsure') ? 1 : 0);
    $valueArray[] = (\GenericHelpers\objectVal($event, 'end_unsure') ? 1 : 0);
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'location')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'casusbelli')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'result')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'side1')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'side2')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'strength_side1')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'strength_side2')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'injured_side1')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'injured_side2')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'dead_side1')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'dead_side2')))."'";
    $valueArray[] = "'".str_replace("'", '\'', str_replace('<br />', "\\n", \GenericHelpers\objectVal($event, 'info')))."'";

    $values[] = '('.implode(', ', $valueArray).')';
}
echo implode(',', $values).';';

// Extra colours
echo '<br><br>';
echo 'INSERT INTO colours VALUES ';

$values = [];
foreach ($coloursByColour as $colour)
{
    $values[] = '('.$colour->id.', '.$colour->id_group.', \''.$colour->name.'\', \''.$colour->hex.'\', \''.$colour->rgb.'\')';
}
echo implode(',', $values).';';