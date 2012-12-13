<?php

class Chart
{
    public function getChart($id_chart)
    {
        return DB::table('charts')
            ->where('id_chart', '=', intval($id_chart))
            ->first();
    }

    public function getGroups($id_chart)
    {
        return DB::table('charts')
            ->join('groups', 'groups.id_chart', '=', 'charts.id_chart')
            ->where('charts.id_chart', '=', intval($id_chart))
            ->get(['groups.*']);
    }

    public function getEvents($id_chart)
    {
        return DB::table('charts')
            ->join('groups', 'groups.id_chart', '=', 'charts.id_chart')
            ->join('events', 'events.id_group', '=', 'groups.id_group')
            ->where('charts.id_chart', '=', intval($id_chart))
            ->get(['events.*']);
    }

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
}