<?php

class Chart
{
    protected function getCharts($where = [], $order_by = [])
    {
        $query = DB::table('charts');

        if (count($where))
            foreach ($where as $line)
                $query->where($line[0], $line[1], $line[2]);

        if (count($order_by))
            $query->order_by($order_by[0], $order_by[1]);
        else
            $query->order_by('chart_name', 'ASC');

        return $query->get();
    }

    public function getAllCharts()
    {
        return $this->getCharts();
    }

    /**
     * Gets a single chart specified by the given
     * WHERE statements.
     * @param  array $where  An array containing arrays that specify the WHERE statements/rules
     * @return mixed         An object containing the found chart OR null if not found
     */
    protected function getChart($where = [])
    {
        $charts = $this->getCharts($where);

        if (!$charts) return null;
        return $charts[0];
    }

    /**
     * Gets a single chart by its ID.
     * @param  int   $id_chart  The chart's ID
     * @return mixed            See getChart()
     */
    public function getChartById($id_chart)
    {
        return $this->getChart([
            ['id_chart', '=', intval($id_chart)]
        ]);
    }

    /**
     * Gets a single chart by its URL name.
     * @param  string $chart_url The chart's URL name
     * @return mixed             See getChart()
     */
    public function getChartByUrl($chart_url)
    {
        return $this->getChart([
            ['chart_url', '=', $chart_url]
        ]);
    }

    /**
     * Gets a groups specified by the given
     * WHERE statements.
     * @param  array $where  An array containing arrays that specify the WHERE statements/rules
     * @return mixed         An object containing the found groups OR null if none were found
     */
    protected function getGroups($where)
    {
        $query = DB::table('charts')
            ->join('groups', 'groups.id_chart', '=', 'charts.id_chart');

        foreach ($where as $line) $query->where($line[0], $line[1], $line[2]);

        return $query->get(['groups.*']);
    }

    /**
     * Gets groups by their chart's ID.
     * @param  int   $id_chart  The chart's ID
     * @return mixed            See getGroups()
     */
    public function getGroupsByChartId($id_chart)
    {
        return $this->getGroups([
            ['charts.id_chart', '=', intval($id_chart)]
        ]);
    }

    protected function getGroup($where)
    {
        $groups = $this->getGroups($where);

        if (!$groups) return null;
        return $groups[0];
    }

    public function getGroupById($id_group)
    {
        return $this->getGroup([
            ['id_group', '=', $id_group]
        ]);
    }

    protected function getEvents($where = [], $order_by = [])
    {
        $query = DB::table('events')
            ->join('groups', 'events.id_group', '=', 'groups.id_group')
            ->join('charts', 'groups.id_chart', '=', 'charts.id_chart');

        if (count($where))
            foreach ($where as $line)
                $query->where($line[0], $line[1], $line[2]);

        if (count($order_by))
            $query->order_by($order_by[0], $order_by[1]);
        else
            $query->order_by('events.event_time_start', 'ASC');

        return $query->get();
    }

    public function getEventsByChartId($id_chart)
    {
        return $this->getEvents([
            ['charts.id_chart', '=', intval($id_chart)]
        ]);
    }

    public function getEventsByGroupId($id_group)
    {
        return $this->getEvents([
            ['events.id_group', '=', intval($id_group)]
        ]);
    }

    protected function getEvent($where)
    {
        $events = $this->getEvents($where);

        if (!$events) return null;
        return $events[0];
    }

    public function getEventById($id_event)
    {
        return $this->getEvent([
            ['id_event', '=', $id_event]
        ]);
    }

    /**
     * Calculates the "depth" for the given events and sorts
     * them in an array where the events' group IDs are the keys.
     *
     * E.g.
     * [0] => [event-object, event-object],
     * [14] => [event-object]
     *
     * The events' depth increases if they intersect with a previously
     * handled event. In this case, the event should e.g. be displayed lower
     * in the front-end code that draws the chart.
     * 
     * @param  array $events  An array of objects containing the events' information
     * @return array          An array containing arrays for each of the events'
     *                        groups, which in turn contain the ordered events' objects.
     */
    public function orderEvents($events)
    {
        // Sort the events by group
        $eventsPerGroup = [];
        foreach ($events as $event)
        {
            $event->depth = 0;
            $event->event_datetime_start = new DateTime($event->event_time_start);
            $event->event_datetime_end   = new DateTime($event->event_time_end);

            if (!isset($eventsPerGroup[$event->id_group])) $eventsPerGroup[$event->id_group] = [];
            $eventsPerGroup[$event->id_group][] = $event;
        }

        // Free some memory
        unset($events);
        unset($event);

        // Now let's order the events per-group
        foreach ($eventsPerGroup as $group => &$events)
        {
            // Keep an array of already handled events.
            // We only need to loop through this when checking
            // for intersecting.
            $handledEvents = [];

            // Loop through the group's events
            foreach ($events as &$event)
            {
                // See if we're intersecting with any already handled events ("targets")
                foreach ($handledEvents as $target)
                {
                    // If we're at a different depth, skip
                    if ($target->depth != $event->depth) continue;

                    // If this target starts after our event ends, skip
                    if ($target->event_datetime_start > $event->event_datetime_end) continue;

                    // If this target ends before our event starts, skip
                    if ($event->event_datetime_start > $target->event_datetime_end) continue;

                    // Alright, we're intersecting, let's go deeper
                    $event->depth++;
                }

                // Add a reference to our event to
                // the array of handled events.
                $handledEvents[] = &$event;
            }
        }

        return $eventsPerGroup;
    }

    /**
     * Updates an existing event or creates a new one.
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function saveEvent($info = [], $new = true)
    {
        // If we're about to update an existing record,
        // make sure we have its ID before continuing.
        if (!$new)
        {
            if (array_key_exists('id_event', $info))
            {
                $id_event = intval($info['id_event']);
                unset($info['id_event']);
            }
            else
            {
                return false;
            }
        }

        // Remove any non-existing columns
        $dbValues = [];

        $columns = DBUtil::columns('events');
        foreach ($columns as $column)
        {
            if (isset($info[$column])) $dbValues[$column] = $info[$column];
        }

        // Do some column-specific changes
        // $dbValues['event_location'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_location'));
        // $dbValues['event_casusbelli'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_casusbelli'));
        // $dbValues['event_result'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_result'));
        // $dbValues['event_side1'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_side1'));
        // $dbValues['event_side2'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_side2'));
        // $dbValues['event_strength1'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_strength1'));
        // $dbValues['event_strength2'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_strength2'));
        // $dbValues['event_injured1'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_injured1'));
        // $dbValues['event_injured1'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_injured1'));
        // $dbValues['event_dead1'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_dead1'));
        // $dbValues['event_dead1'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_dead1'));
        // $dbValues['event_info'] = nl2br(\GenericHelpers\objectVal($dbValues, 'event_info'));

        // Are we talking about a new record?
        if ($new)
        {
            DB::table('events')
                ->insert($dbValues);
        }
        // If not, update the existing record.
        else
        {
            DB::table('events')
                ->where('id_event', '=', $id_event)
                ->update($dbValues);
        }

        return true;
    }

    /**
     * Updates an existing group or creates a new one.
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function saveGroup($info = [], $new = true)
    {
        // If we're about to update an existing record,
        // make sure we have its ID before continuing.
        if (!$new)
        {
            if (array_key_exists('id_group', $info))
            {
                $id_group = intval($info['id_group']);
                unset($info['id_group']);
            }
            else
            {
                return false;
            }
        }

        // Remove any non-existing columns
        $dbValues = [];

        $columns = DBUtil::columns('groups');
        foreach ($columns as $column)
        {
            if (isset($info[$column])) $dbValues[$column] = $info[$column];
        }

        // Are we talking about a new record?
        if ($new)
        {
            DB::table('groups')
                ->insert($dbValues);
        }
        // If not, update the existing record.
        else
        {
            DB::table('groups')
                ->where('id_group', '=', $id_group)
                ->update($dbValues);
        }

        return true;
    }

    /**
     * Updates an existing chart or creates a new one.
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function saveChart($info = [], $new = true)
    {
        // If we're about to update an existing record,
        // make sure we have its ID before continuing.
        if (!$new)
        {
            if (array_key_exists('id_chart', $info))
            {
                $id_chart = intval($info['id_chart']);
                unset($info['id_chart']);
            }
            else
            {
                return false;
            }
        }

        // Remove any non-existing columns
        $dbValues = [];

        $columns = DBUtil::columns('charts');
        foreach ($columns as $column)
        {
            if (isset($info[$column])) $dbValues[$column] = $info[$column];
        }

        // Are we talking about a new record?
        if ($new)
        {
            DB::table('charts')
                ->insert($dbValues);
        }
        // If not, update the existing record.
        else
        {
            DB::table('charts')
                ->where('id_chart', '=', $id_chart)
                ->update($dbValues);
        }

        return true;
    }

    protected function deleteChart($where)
    {
        $query = DB::table('charts');

        foreach ($where as $line) $query->where($line[0], $line[1], $line[2]);

        return $query->delete();
    }

    public function deleteChartByUrl($chart_url)
    {
        $this->deleteChart([
            ['chart_url', '=', $chart_url]
        ]);
    }

    protected function deleteGroup($where)
    {
        $query = DB::table('groups');

        foreach ($where as $line) $query->where($line[0], $line[1], $line[2]);

        return $query->delete();
    }

    public function deleteGroupById($id_group)
    {
        $this->deleteGroup([
            ['id_group', '=', $id_group]
        ]);
    }

    protected function deleteEvent($where)
    {
        $query = DB::table('events');

        foreach ($where as $line) $query->where($line[0], $line[1], $line[2]);

        return $query->delete();
    }

    public function deleteEventById($id_event)
    {
        $this->deleteEvent([
            ['id_event', '=', $id_event]
        ]);
    }
}