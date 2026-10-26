<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityLogIndexRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(ActivityLogIndexRequest $request)
    {
        $filters = $request->validated();
        $query = ActivityLog::with('user');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        $activityLogs = $query->latest()->paginate(15)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('activity-logs.index', compact('activityLogs', 'users'));
    }
}
