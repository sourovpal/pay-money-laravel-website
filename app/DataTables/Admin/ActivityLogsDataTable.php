<?php

namespace App\DataTables\Admin;

use Yajra\DataTables\Services\DataTable;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use Config, Auth;

class ActivityLogsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($activityLog) {
                return dateFormat($activityLog->created_at);
            })
            ->addColumn('username', function ($activityLog) {
                if ($activityLog->type == 'Admin') {
                    $admin = getColumnValue($activityLog->admin);
                    if ($admin <> '-') {
                        return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_admin')) ? '<a href="' . url(Config::get('adminPrefix') . '/admin-user/edit/' . $activityLog->admin->id) . '">' . $admin . '</a>' : $admin;
                    }
                } else {
                    $user = getColumnValue($activityLog->user);
                    return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $activityLog->user->id) . '">' . $user . '</a>' : $user;
                }
            })
            ->editColumn('browser_agent', function ($activityLog) {
                $getBrowser = getBrowser($activityLog->browser_agent);
                return $getBrowser['name'] . ' ' . substr($getBrowser['version'], 0, 4) . ' | ' . ucfirst($getBrowser['platform']);
            })
            ->rawColumns(['user_id', 'username'])
            ->make(true);
    }

    public function query()
    {
        $query = ActivityLog::with([
            'user'   => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            },
            'admin' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            },
        ])
            ->select('activity_logs.*');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'activity_logs.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'activity_logs.created_at', 'title' => __('Date')])
            ->addColumn(['data' => 'type', 'name' => 'activity_logs.type', 'title' => __('User Type')])
            ->addColumn(['data' => 'username', 'name' => 'user.last_name', 'title' => __('User'), 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'user.first_name', 'title' => __('User'), 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'admin.last_name', 'title' => __('User'), 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'admin.first_name', 'title' => __('User'), 'visible' => false])
            ->addColumn(['data' => 'username', 'name' => 'username', 'title' => __('Username')])
            ->addColumn(['data' => 'ip_address', 'name' => 'activity_logs.ip_address', 'title' =>__( 'IP Address')])
            ->addColumn(['data' => 'browser_agent', 'name' => 'activity_logs.browser_agent', 'title' => __('Browser | Platform')])
            ->parameters(dataTableOptions());
    }
}
