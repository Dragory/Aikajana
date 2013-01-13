<?php

class ChartDrawer
{
            // The start and end of the chart
    private $startDatetime, $endDatetime,

            // Widths for different intervals. The month width is used as a base for the others.
            $monthWidth = 10, $yearWidth, $decadeWidth,

            // The total width and height for the chart
            $chartWidth, $chartHeight,

            // Line height, line padding and the padding between two groups.
            $lineHeight = 20, $linePadding = 4, $groupPadding = 100,

            // Arrays containing the groups and events of the chart
            $events = [], $groups = [], $groupsById = [], $eventsByGroupId = [];

    private static $chartNum = 0;

    public function __construct($startTime, $endTime)
    {
        // Save the start and end times
        $this->startDatetime = new DateTime($startTime);
        $this->endDatetime   = new DateTime($endTime);

        // Calculate the total number of years between that period
        $difference = $this->startDatetime->diff($this->endDatetime);

        // If the chart starts after it ends, return.
        if ($difference->invert) return false;

        // Calculate different required widths according to the month width
        $this->yearWidth   = $this->monthWidth * 12;
        $this->decadeWidth = $this->yearWidth * 10;

        // Calculate the full width of the chart by using the number of
        // total months and the width of a single month defined above.
        $this->chartWidth = ($difference->y * 12 + $difference->m) * $this->monthWidth;

        // Start at 0 full height. This will raise according
        // to the events later on in the code.
        $this->chartHeight = 0;
    }

    // Gets a unique chart number that increases each time you get it.
    protected function getChartNum()
    {
        self::$chartNum++;
        return self::$chartNum;
    }

    /**
     * SETTINGS
     */

    public function setMonthWidth($width)
    {
        $this->monthWidth  = intval($width);
        $this->yearWidth   = $this->monthWidth * 12;
        $this->decadeWidth = $yearWidth * 10;
    }

    public function setLineHeight($height)
    {
        $this->lineHeight  = intval($height);
    }

    public function setLinePadding($padding)
    {
        $this->linePadding  = intval($padding);
    }

    // public function setGroupPadding($padding)
    // {
    //     $this->groupPadding  = intval($padding);
    // }

    public function setEvents(&$events)
    {
        // Save the events
        $this->events = &$events;

        // And also have a sorted by groups version available
        $eventsByGroupId = [];
        foreach ($this->events as &$event)
        {
            if (!isset($eventsByGroupId[$event->id_group])) $eventsByGroupId[$event->id_group] = [];
            $eventsByGroupId[$event->id_group][] = $event;
        }
        $this->eventsByGroupId = $eventsByGroupId;
    }

    public function setGroups(&$groups)
    {
        $this->groups = &$groups;
        $this->groupsById = [];

        foreach ($this->groups as &$group)
        {
            $this->groupsById[$group->id_group] = $group;
        }
    }

    /**
     * Gets the width of a difference of two DateTimes.
     * The width will be calculated using a DateInterval
     * object and the set month width.
     */
    protected function getDatetimeDifferenceWidth(DateInterval $difference)
    {
        $width = 0;
        $width += $difference->y * 12 * $this->monthWidth;
        $width += $difference->m * $this->monthWidth;
        $width += $difference->d * ($this->monthWidth/31);

        if ($difference->invert) $width *= -1;

        return floor($width);
    }

    /**
     * Calculate the position and size of each event according to their
     * start time, end time and depth. Depth is calculated in the Chart model.
     * Also sets the chart's maximum height,
     * which is the bottom of the "deepest" event.
     * @return none Modifies the events directly.
     */
    public function calculateEventDimensions()
    {
        if (!$this->eventsByGroupId) return;

        foreach ($this->eventsByGroupId as $group => &$events)
        {
            // Use the current chart height as defined
            // by the previous groups as a baseline to
            // add the group padding to. This way different
            // groups won't intersect with each other.
            if ($this->chartHeight == 0)
                $groupHeight = 0;
            else
                $groupHeight = $this->chartHeight + $this->groupPadding;

            // Loop through the events of this group
            foreach ($events as &$event)
            {
                // Calculate the width of this event
                $timeDifferenceInEvent = $event->event_datetime_start->diff($event->event_datetime_end);
                $thisWidth = $this->getDatetimeDifferenceWidth($timeDifferenceInEvent);

                // Calculate the X coordinate of this event
                $timeDifferenceToStart = $this->startDatetime->diff($event->event_datetime_start);
                $thisX = $this->getDatetimeDifferenceWidth($timeDifferenceToStart);

                // Calculate the Y coordinate of this event
                $thisY = $groupHeight;
                $thisY += $event->depth * ($this->lineHeight + $this->linePadding);

                // Set the values in the event's data
                $event->event_px_width  = $thisWidth;
                $event->event_px_height = $this->lineHeight;
                $event->event_px_x = $thisX;
                $event->event_px_y = $thisY;

                // See if we got a new maximum height for the chart and set that
                $this->chartHeight = max($this->chartHeight, $thisY + $this->lineHeight);
            }
        }
    }

    /**
     * Creates the HTML needed for the
     * decades and years above the chart.
     * @return array An array containing the decade HTML at [0] and the year HTML at [1].
     */
    public function getYearHtml()
    {
        $decadeHtml = '';
        $yearHtml = '';

        // Start at the next full year on the chart,
        // i.e. the 1st of January on the next year on the chart.
        $chartNextFullYear = intval($this->startDatetime->format('Y')) + 1;
        $yearListingStartDatetime = DateTime::createFromFormat('Y-m-d', $chartNextFullYear.'-01-01');

        // Loop through every year from the start defined above to the end of the chart
        $yearInterval = DateInterval::createFromDateString('1 year');
        $timePeriod = new DatePeriod($this->startDatetime, $yearInterval, $this->endDatetime);

        foreach ($timePeriod as $thisTime)
        {
            list($thisMonth, $thisYear) = explode('-', $thisTime->format('m-Y'));

            // Make sure we're at the start of a year...
            if ($thisMonth != 1) continue;

            // Distance from the start of the chart
            $diffFromStart = $this->startDatetime->diff($thisTime);
            $leftFromStart = $this->getDatetimeDifferenceWidth($diffFromStart);

            // Year HTML
            $yearHtml .= '<div class="chart-year" style="left: '.$leftFromStart.'px">'.$thisYear.'</div>';

            // Decade HTML
            if ($thisYear % 10 == 0)
            {
                // Different sizes for centuries
                if ($thisYear % 100 == 0) $decadeClass = 'chart-decade-large';
                else $decadeClass = 'chart-decade-small';

                $decadeHtml .= '<div class="chart-decade '.$decadeClass.'" style="left: '.($leftFromStart).'px">'.
                                   $thisYear.'-luku'.
                               '</div>';
            }
        }

        return [$decadeHtml, $yearHtml];
    }

    /**
     * Gets the HTML for the events display
     */
    public function getEventHtml()
    {
        if (!$this->eventsByGroupId) return null;

        $return = '';

        foreach ($this->events as &$event)
        {
            // If we're supposed to inherit our colours from the group, do so
            if ($event->event_colour_inherit == 1)
            {
                $event->colour_hex = $this->groupsById[$event->id_group]->group_colour;
                $event->colour_rgb = $this->groupsById[$event->id_group]->group_colour_rgb;
            }

            $fullBgColor = 'background-color: rgba('.$event->colour_rgb.', 0.7);';

            // See if we have a "custom", shorter name to display
            if (!empty($event->event_name_short)) $displayName = $event->event_name_short;
            else $displayName = $event->event_name;

            // Is the start or end unsure?
            // That would mean we would need to use a gradient.

            // Always fade for 120 pixels unless the event is thinner than that
            $fadePercentage = round(120/$event->event_px_width*100);
            if ($fadePercentage > 50) $fadePercentage = '20';

            if ($event->event_start_unsure && $event->event_end_unsure)
            {

                $bgColor = 'linear-gradient(left,'.
                                              'rgba('.$event->colour_rgb.', 0) 0%,'.
                                              'rgba('.$event->colour_rgb.', 0.7) '.$fadePercentage.'%,'.
                                              'rgba('.$event->colour_rgb.', 0.7) '.(100-$fadePercentage).'%,'.
                                              'rgba('.$event->colour_rgb.', 0) 100%'.
                                            ');';
            }
            elseif ($event->event_start_unsure)
            {
                $bgColor = 'linear-gradient(left,'.
                                              'rgba('.$event->colour_rgb.', 0) 0%,'.
                                              'rgba('.$event->colour_rgb.', 0.7) '.$fadePercentage.'%,'.
                                              'rgba('.$event->colour_rgb.', 0.7) 100%'.
                                            ');';
            }
            elseif ($event->event_end_unsure)
            {
                $bgColor = 'linear-gradient(left,'.
                                              'rgba('.$event->colour_rgb.', 0.7) 0%,'.
                                              'rgba('.$event->colour_rgb.', 0.7) '.(100-$fadePercentage).'%,'.
                                              'rgba('.$event->colour_rgb.', 0) 100%'.
                                            ');';
            }

            // If we went the gradient route, also add browser prefixes
            if ($event->event_start_unsure || $event->event_end_unsure)
            {
                $fullBgColor .= 'background: -webkit-'.$bgColor;
                $fullBgColor .= 'background: -moz-'.$bgColor;
                $fullBgColor .= 'background: -ms-'.$bgColor;
                $fullBgColor .= 'background: -o-'.$bgColor;
                $fullBgColor .= 'background: '.$bgColor;
            }

            $return .= '<div class="chart-event"'.
                        ' data-id-group="'.$event->id_group.'"'.
                        ' data-id-event="'.$event->id_event.'"'.
                        ' style="left: '.  $event->event_px_x.'px;'.
                                'top: '.   $event->event_px_y.'px;'.
                                'width: '. $event->event_px_width.'px;'.
                                'height: '.$event->event_px_height.'px;'.
                                'background-color: '.$event->colour_hex.';'. // Fallback for some browsers
                                $fullBgColor.
                          '"'.
                       '>'.
                        $displayName.
                       '</div>';
        }

        return $return;
    }

    /**
     * Gets the HTML and JavaScript needed for the charts'
     * JavaScript objects to function. This should be requested
     * ONCE per page. That's why it's also limited at that, ha.
     * Per instance limitation, though.
     */
    protected function getInitializationHtml()
    {
        return null;
        if ($this->initialized) return null;

        return '<script type="text/javascript">'.
                    'var charts = [];'.
               '</script>';
    }

    /**
     * Gets the HTML (and JS) for the whole chart.
     * Uses above HTML functions to construct the
     * chart.
     */
    public function getChartHtml()
    {
        $this->calculateEventDimensions();
        $chartNum = $this->getChartNum();

        $return = '';

        // if (!$this->initialized) $return .= $this->getInitializationHtml();

        list($decadeHtml, $yearHtml) = $this->getYearHtml();

        $return .= '<div class="chart" id="chart-'.$chartNum.'">'.
                        '<div class="chart-controls-container">'.
                            '<div class="chart-controls-left"></div>'.
                            '<div class="chart-controls-right"></div>'.
                            '<div class="chart-scroll-container">'.
                                '<div class="chart-content" style="width: '.$this->chartWidth.'px">'.
                                    '<div class="chart-decade-container">'. $decadeHtml.'</div>'.
                                    '<div class="chart-year-container">'.   $yearHtml.'</div>'.
                                    '<div class="chart-event-container" style="width: '.$this->chartWidth.'px; height: '.$this->chartHeight.'px">'.
                                        '<div class="chart-event-padding">'.$this->getEventHtml().'</div>'.
                                    '</div>'.
                                    '<div class="chart-month-container" style="width: '.$this->chartWidth.'px"></div>'.
                                '</div>'.
                            '</div>'.
                        '</div>'.
                        '<div class="chart-info">'.
                            '<div class="chart-info-help">Napsauta sotaa aikajanalla saadaksesi siitä lisätietoja</div>'.
                        '</div>'.
                   '</div>'.
                   '<script type="text/javascript">'.
                        'charts.push('.json_encode(['id' => 'chart-'.$chartNum, 'events' => $this->events, 'groups' => $this->groups]).');'.
                   '</script>';

        return $return;
    }
}