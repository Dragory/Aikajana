<?php

class ChartDrawer
{
    private $startYear, $endYear, $totalYears,                                    // Start and end year plus their difference
            $startYearDatetime, $endYearDatetime,                                 // Datetime objects for the start and end years
            $monthWidth = 10, $yearWidth, $decadeWidth, $fullWidth,               // Different widths for the chart
            $lineHeight = 20, $linePadding = 4, $groupPadding = 100, $fullHeight, // Different heights for the chart
            $events; // An array containing the chart's events

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
        $this->events = &$events;
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

        return floor($width);
    }

    /**
     * Calculate the events' dimensions and, at the same time,
     * find out and set the chart's maximum height (i.e. the bottom
     * edge of the bottommost event).
     */
    public function calculateEventDimensions()
    {
        if (!$this->events) return;

        $groupMult = 0;
        foreach ($this->events as $group => &$events)
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
        if (!$this->events) return null;

        $return = '';

        foreach ($this->events as $group => &$events)
        {
            foreach ($events as &$event)
            {
                $return .= '<div class="chart-event"'.
                            ' data-id="'.$event->id_event.'"'.
                            ' style="left: '.$event->event_px_x.'px;
                                     top: '.$event->event_px_y.'px;
                                     width: '.$event->event_px_width.'px;
                                     height: '.$event->event_px_height.'px'.
                              '"'.
                           '>'.
                            $event->event_name.
                           '</div>';

                $return .= '<!--'.print_r($event->event_datetime_start->diff($event->event_datetime_end), true).'-->';
            }
        }

        return $return;
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
        $return .= '<div class="chart" id="chart-'.$chartNum.'">'.
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
                   '</div>';

        return $return;
    }
}