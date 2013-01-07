<?php

class ChartDrawer
{
    private $startYear, $endYear, $totalYears,                                    // Start and end year plus their difference
            $startYearDatetime, $endYearDatetime,                                 // Datetime objects for the start and end years
            $monthWidth = 10, $yearWidth, $decadeWidth, $fullWidth,               // Different widths for the chart
            $lineHeight = 20, $linePadding = 4, $groupPadding = 100, $fullHeight, // Different heights for the chart
            
            $events = [], $groups = [], $ordered = [], // Arrays containing the chart's events and groups.
                                                       // "$ordered" is an array that contains the references to the events grouped by groups.

            $initialized = false; // Have we already initialized the charts' JS?

    private static $chartNum = 0;

    public function __construct($start, $end)
    {
        $this->startYear = intval($start);
        $this->endYear   = intval($end) + 1; // We add one year so when you have "1900-2010" the code sees it as "1.1.1900-1.1.2011" i.e. shows the full years

        $this->startYearDatetime = new DateTime($this->startYear.'-01-01');
        $this->endYearDatetime   = new DateTime($this->endYear.'-01-01');

        if ($this->endYear < $this->startYear) return false; // Invalid date range

        $this->yearWidth   = $this->monthWidth * 12;
        $this->decadeWidth = $this->yearWidth * 10;

        $this->totalYears = $this->endYear - $this->startYear;
        $this->fullWidth  = $this->totalYears * $this->yearWidth;

        $this->fullHeight = 0;
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

    public function setLinePadding($height)
    {
        $this->linePadding  = intval($height);
    }

    public function setGroupPadding($height)
    {
        $this->groupPadding  = intval($height);
    }

    public function setEvents(&$events)
    {
        // Save the events
        $this->events = &$events;

        // And also have a sorted by groups version available
        $ordered = [];
        foreach ($this->events as &$event)
        {
            if (!isset($ordered[$event->id_group])) $ordered[$event->id_group] = [];
            $ordered[$event->id_group][] = $event;
        }
        $this->ordered = $ordered;
    }

    public function setGroups(&$groups)
    {
        $this->groups = &$groups;
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
     * Calculate the events' dimensions and, at the same time,
     * find out and set the chart's maximum height (i.e. the bottom
     * edge of the bottommost event).
     */
    public function calculateEventDimensions()
    {
        if (!$this->ordered) return;

        $groupMult = 0;
        foreach ($this->ordered as $group => &$events)
        {
            foreach ($events as &$event)
            {
                // Calculate the width of this event
                $timeDifferenceInEvent = $event->event_datetime_start->diff($event->event_datetime_end);
                $thisWidth = $this->getDatetimeDifferenceWidth($timeDifferenceInEvent);

                // Calculate the X coordinate of this event
                $timeDifferenceToStart = $this->startYearDatetime->diff($event->event_datetime_start);
                $thisX = $this->getDatetimeDifferenceWidth($timeDifferenceToStart);

                // Calculate the Y coordinate of this event
                $thisY = $groupMult * $this->groupPadding;
                $thisY += $event->depth * ($this->lineHeight + $this->linePadding);

                // Set the values in the event's data
                $event->event_px_width  = $thisWidth;
                $event->event_px_height = $this->lineHeight;
                $event->event_px_x = $thisX;
                $event->event_px_y = $thisY;

                // See if we got a new maximum height for the chart and set that
                $this->fullHeight = max($this->fullHeight, $thisY + $this->lineHeight);
            }

            $groupMult++;
        }
    }

    /**
     * Gets the HTML for the decades display
     */
    public function getDecadeHtml()
    {
        $return = '';
        for ($year = $this->startYear; $year < $this->endYear; $year++)
        {
            if ($year % 10 != 0) continue;

            $yearsFromStart = $year - $this->startYear;
            $return .= '<div class="chart-decade" style="left: '.($yearsFromStart * $this->yearWidth).'px">'.$year.'</div>';
        }

        return $return;
    }

    /**
     * Gets the HTML for the years display
     */
    public function getYearHtml()
    {
        $return = '';
        for ($year = $this->startYear; $year < $this->endYear; $year++)
        {
            $yearsFromStart = $year - $this->startYear;
            $return .= '<div class="chart-year" style="left: '.($yearsFromStart * $this->yearWidth).'px">'.$year.'</div>';
        }

        return $return;
    }

    /**
     * Gets the HTML for the events display
     */
    public function getEventHtml()
    {
        if (!$this->ordered) return null;

        $return = '';

        foreach ($this->events as &$event)
        {
            $return .= '<div class="chart-event"'.
                        ' data-id-group="'.$event->id_group.'"'.
                        ' data-id-event="'.$event->id_event.'"'.
                        ' style="left: '.  $event->event_px_x.'px;'.
                                'top: '.   $event->event_px_y.'px;'.
                                'width: '. $event->event_px_width.'px;'.
                                'height: '.$event->event_px_height.'px'.
                          '"'.
                       '>'.
                        $event->event_name.
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

        if (!$this->initialized) $return .= $this->getInitializationHtml();

        $return .= '<div class="chart" id="chart-'.$chartNum.'">'.
                        '<div class="chart-controls-container">'.
                            '<div class="chart-controls-left"></div>'.
                            '<div class="chart-controls-right"></div>'.
                            '<div class="chart-scroll-container">'.
                                '<div class="chart-content" style="width: '.$this->fullWidth.'px">'.
                                    '<div class="chart-decade-container">'. $this->getDecadeHtml().'</div>'.
                                    '<div class="chart-year-container">'.   $this->getYearHtml().'</div>'.
                                    '<div class="chart-event-container" style="width: '.$this->fullWidth.'px; height: '.$this->fullHeight.'px">'.
                                        '<div class="chart-event-padding">'.$this->getEventHtml().'</div>'.
                                    '</div>'.
                                    '<div class="chart-month-container" style="width: '.$this->fullWidth.'px"></div>'.
                                '</div>'.
                            '</div>'.
                        '</div>'.
                        '<div class="chart-info"></div>'.
                   '</div>'.
                   '<script type="text/javascript">'.
                        'charts.push('.json_encode(['id' => 'chart-'.$chartNum, 'events' => $this->events, 'groups' => $this->groups]).');'.
                   '</script>';

        return $return;
    }
}