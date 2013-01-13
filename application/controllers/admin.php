<?php

class Admin_Controller extends ActionFilter\Filter_Controller
{
    protected $currentUser = null;
    public $layout = 'layouts.admin';

    public function before()
    {
        // Make sure we're logged in
        $this->before_filter('checkLogin')->except(['login', 'login_post']);

        // Set some defaults for the layout
        $this->layout->breadcrumb = null;
        $this->layout->status     = null;
        $this->layout->content    = null;
        $this->layout->footer     = null;
    }

    protected function checkLogin()
    {
        $auth = new Authentication;
        $this->currentUser = $auth->getCurrentUser();

        if (!$this->currentUser)
        {
            Status::addError(__('errors.not_logged_in'));
            return Redirect::to_route('admin_login');
        }
    }

    protected function setBreadcrumb($parts = [])
    {
        $this->layout->nest('breadcrumb', 'admin.partials.breadcrumb', ['parts' => $parts]);
    }

    protected function loadPage($view, $data = [])
    {
        $this->layout->nest('status', 'admin.partials.status', Status::getMessages());
        $this->layout->nest('content', 'admin.'.$view, $data);
    }

    public function action_login()
    {
        $this->loadPage('login');
    }

    public function action_login_post()
    {
        $username = Input::get('username');
        $password = Input::get('password');
        $stayLoggedIn = (Input::get('stay-logged-in') ? true : false);

        if (!$username || !$password)
        {
            Status::addError(__('error.login_missing_fields'));
            return Redirect::to_route('admin_login');
        }

        $auth = new Authentication;
        $login = $auth->login($username, $password, $stayLoggedIn);

        if (!$login)
        {
            Status::addError(__('error.login_failed'));
            return Redirect::to_route('admin_login');
        }

        Status::addSuccess(__('success.login_successful'));
        return Redirect::to_route('admin_charts');
    }

    public function action_logout()
    {
        $auth = new Authentication;
        $auth->logout();

        return Redirect::to_route('admin_login');
    }

    /**
     * CHARTS
     */

    public function action_charts()
    {
        $chartModel = new Chart;
        $charts = $chartModel->getAllCharts();

        $this->setBreadcrumb([
            [null, __('admin.charts_heading')]
        ]);

        $this->loadPage('charts', ['charts' => $charts]);
    }

    /**
     * Shows a chart's information and its groups.
     * @param  string $chart_url The URL-friendly name of the chart
     * @return none
     */
    public function action_chart($chart_url)
    {
        $chartModel = new Chart;
        $chart = $chartModel->getChartByUrl($chart_url);
        $groups = $chartModel->getGroupsByChartId($chart->id_chart);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [null, $chart->chart_name]
        ]);

        $this->loadPage('chart', ['chart' => $chart, 'groups' => $groups]);
    }

    public function action_chart_save_post($chart_url)
    {
        $chartModel = new Chart;

        $data = $_POST;
        $status = $chartModel->saveChart($data, false);

        if ($status) Status::addSuccess('success.group_saved');
        else Status::addError('error.group_save_failed');

        return Redirect::to_route('admin_chart', [$chart_url]);
    }

    public function action_chart_add()
    {
        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [null, 'Lisää kaavio']
        ]);

        $this->loadPage('chart_add');
    }

    public function action_chart_add_post()
    {
        $chartModel = new Chart;

        $data = $_POST;
        $status = $chartModel->saveChart($data, true);

        if ($status) Status::addSuccess('success.chart_added');
        else Status::addError('error.chart_add_failed');

        return Redirect::to_route('admin_charts');
    }

    public function action_chart_delete($chart_url)
    {
        $chartModel = new Chart;
        $chartModel->deleteChartByUrl($chart_url);

        Status::addSuccess(__('success.chart_deleted'));
        return Redirect::to_route('admin_charts');
    }

    /**
     * GROUPS
     */
    
    public function action_group($id_group)
    {
        $chartModel = new Chart;
        $group = $chartModel->getGroupById($id_group);
        $chart = $chartModel->getChartById($group->id_chart);
        $events = $chartModel->getEventsByGroupId($id_group);
        $colours = $chartModel->getColoursByGroupId($id_group);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [URL::to_route('admin_chart', $chart->chart_url), $chart->chart_name],
            [null, $group->group_name]
        ]);

        $this->loadPage('group', ['chart' => $chart, 'group' => $group, 'colours' => $colours, 'events' => $events]);
    }

    public function action_group_save_post($id_group)
    {
        $chartModel = new Chart;

        $data = $_POST;
        $status = $chartModel->saveGroup($data, false);

        if ($status) Status::addSuccess(__('success.group_saved'));
        else Status::addError(__('error.group_save_failed'));

        return Redirect::to_route('admin_group', [$id_group]);
    }

    public function action_group_add($chart_url)
    {
        $chartModel = new Chart;
        $chart = $chartModel->getChartByUrl($chart_url);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [URL::to_route('admin_chart', $chart_url), $chart->chart_name],
            [null, 'Lisää ryhmä']
        ]);

        $this->loadPage('group_add', ['chart' => $chart]);
    }

    public function action_group_add_post($chart_url)
    {
        $chartModel = new Chart;

        $data = $_POST;
        $status = $chartModel->saveGroup($data, true);

        if ($status) Status::addSuccess(__('success.group_added'));
        else Status::addError(__('error.group_add_failed'));

        return Redirect::to_route('admin_chart', [$chart_url]);
    }

    public function action_group_delete($id_group)
    {
        $chartModel = new Chart;
        $group = $chartModel->getGroupById($id_group);
        $chart = $chartModel->getChartById($group->id_chart);
        $chartModel->deleteGroupById($id_group);

        Status::addSuccess(__('success.group_deleted'));
        return Redirect::to_route('admin_chart', [$chart->chart_url]);
    }

    /**
     * EVENTS
     */

    public function action_event($id_event)
    {
        $chartModel = new Chart;
        $event = $chartModel->getEventById($id_event);
        $group = $chartModel->getGroupById($event->id_group);
        $chart = $chartModel->getChartById($group->id_chart);
        $colours = $chartModel->getColoursByGroupId($group->id_group);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [URL::to_route('admin_chart', $chart->chart_url), $chart->chart_name],
            [URL::to_route('admin_group', $group->id_group), $group->group_name],
            [null, $event->event_name]
        ]);

        $this->loadPage('event', ['chart' => $chart, 'group' => $group, 'event' => $event, 'colours' => $colours]);
    }

    public function action_event_save_post($id_event)
    {
        $chartModel = new Chart;
        $event = $chartModel->getEventById($id_event);
        $group = $chartModel->getGroupById($event->id_group);

        $data = $_POST;
        $status = $chartModel->saveEvent($data, false);

        if ($status) Status::addSuccess(__('success.event_saved'));
        else Status::addError(__('error.event_save_failed'));

        return Redirect::to_route('admin_group', $group->id_group);
    }

    public function action_event_add($id_group)
    {
        $chartModel = new Chart;
        $group = $chartModel->getGroupById($id_group);
        $chart = $chartModel->getChartById($group->id_chart);
        $colours = $chartModel->getColoursByGroupId($group->id_group);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [URL::to_route('admin_chart', $chart->chart_url), $chart->chart_name],
            [URL::to_route('admin_group', $group->id_group), $group->group_name],
            [null, 'Lisää tapahtuma']
        ]);

        $this->loadPage('event_add', ['chart' => $chart, 'group' => $group, 'colours' => $colours]);
    }

    public function action_event_add_post($id_group)
    {
        $chartModel = new Chart;

        $data = $_POST;
        $status = $chartModel->saveEvent($data, true);

        if ($status) Status::addSuccess(__('success.event_added'));
        else Status::addError(__('error.event_add_failed'));

        return Redirect::to_route('admin_group', $id_group);
    }

    public function action_event_delete($id_event)
    {
        $chartModel = new Chart;
        $chartModel->deleteEventById($id_event);

        Status::addSuccess(__('success.event_deleted'));
        return Redirect::to_route('admin_group', $id_group);
    }

    /**
     * COLOURS
     */

    public function action_colour($id_colour)
    {
        $chartModel = new Chart;
        $colour = $chartModel->getColourById($id_colour);
        $group = $chartModel->getGroupById($colour->id_group);
        $chart = $chartModel->getChartById($group->id_chart);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [URL::to_route('admin_chart', $chart->chart_url), $chart->chart_name],
            [URL::to_route('admin_group', $group->id_group), $group->group_name],
            [null, $colour->colour_name]
        ]);

        $this->loadPage('colour', ['chart' => $chart, 'group' => $group, 'colour' => $colour]);
    }

    public function action_colour_save_post($id_colour)
    {
        $chartModel = new Chart;
        $colour = $chartModel->getColourById($id_colour);
        $group = $chartModel->getGroupById($colour->id_group);

        $data = $_POST;
        $status = $chartModel->saveColour($data, false);

        if ($status) Status::addSuccess(__('success.colour_saved'));
        else Status::addError(__('error.colour_save_failed'));

        return Redirect::to_route('admin_group', $group->id_group);
    }

    public function action_colour_add($id_group)
    {
        $chartModel = new Chart;
        $group = $chartModel->getGroupById($id_group);
        $chart = $chartModel->getChartById($group->id_chart);

        $this->setBreadcrumb([
            [URL::to_route('admin_charts'), __('admin.charts_heading')],
            [URL::to_route('admin_chart', $chart->chart_url), $chart->chart_name],
            [URL::to_route('admin_group', $group->id_group), $group->group_name],
            [null, 'Lisää tapahtuma']
        ]);

        $this->loadPage('colour_add', ['chart' => $chart, 'group' => $group]);
    }

    public function action_colour_add_post($id_group)
    {
        $chartModel = new Chart;

        $data = $_POST;
        $status = $chartModel->saveColour($data, true);

        if ($status) Status::addSuccess(__('success.colour_added'));
        else Status::addError(__('error.colour_add_failed'));

        return Redirect::to_route('admin_group', $id_group);
    }

    public function action_colour_delete($id_colour)
    {
        $chartModel = new Chart;
        $colour = $chartModel->getColourById($id_colour);
        $group = $chartModel->getGroupById($colour->id_colour);
        $chartModel->deleteColourById($id_colour);

        Status::addSuccess(__('success.colour_deleted'));
        return Redirect::to_route('admin_group', $group->id_group);
    }
}