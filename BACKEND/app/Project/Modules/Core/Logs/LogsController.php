<?php

namespace App\Project\Modules\Core\Logs;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    private function query(string $type)
    {
        abort_unless(in_array($type, ['activity', 'requests', 'errors'], true), 404);
        $table = ['activity' => 'activity_logs', 'requests' => 'request_logs', 'errors' => 'error_logs'][$type];
        return DB::connection(config('activitylog.database_connection'))->table($table);
    }

    public function index(Request $request, string $type)
    {
        $query = $this->query($type);
        $input = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => ['nullable', 'date_format:Y-m-d', ...($request->filled('start_date') ? ['after_or_equal:start_date'] : [])],
            'search' => 'nullable|string|max:150', 'user_id' => 'nullable|integer|min:1',
            'request_id' => 'nullable|uuid', 'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);
        if (!empty($input['start_date'])) $query->where('occurred_at', '>=', $input['start_date'].' 00:00:00');
        if (!empty($input['end_date'])) $query->where('occurred_at', '<', Carbon::parse($input['end_date'])->addDay()->format('Y-m-d').' 00:00:00');
        foreach (['user_id', 'request_id'] as $key) {
            if (!empty($input[$key])) $query->where($key, $input[$key]);
        }
        if (!empty($input['search'])) {
            $columns = $type === 'requests' ? ['url', 'route_name', 'method', 'response_status', 'request_id'] : ['module', 'action', 'message', 'request_id'];
            $query->where(function ($query) use ($columns, $input) {
                foreach ($columns as $column) $query->orWhere($column, 'like', '%'.$input['search'].'%');
            });
        }
        $columns = $type === 'requests'
            ? ['id', 'uuid', 'request_id', 'user_id', 'occurred_at', 'method', 'url', 'response_status', 'duration']
            : ['id', 'uuid', 'request_id', 'user_id', 'occurred_at', 'module', 'action', 'message'];
        $page = $query->select($columns)->orderByDesc('id')->paginate($input['per_page'] ?? 25);
        // Resolve users on the main database, never via a cross-database join.
        $users = User::whereIn('id', $page->getCollection()->pluck('user_id')->filter()->unique())->pluck('username', 'id');
        $page->getCollection()->transform(function ($row) use ($users) {
            $row->user_name = $users[$row->user_id] ?? ($row->user_id ? 'User #'.$row->user_id : 'Guest / system');
            return $row;
        });
        return response()->json(array_merge($page->toArray(), ['timezone' => config('app.timezone')]));
    }

    public function show(string $type, string $uuid)
    {
        $row = $this->query($type)->where('uuid', $uuid)->first();
        abort_unless($row, 404);
        foreach (['old_data', 'new_data', 'payload', 'context'] as $key) {
            if (isset($row->$key)) $row->$key = json_decode($row->$key, true);
        }
        $row->user_name = $row->user_id ? (User::find($row->user_id)?->username ?? 'User #'.$row->user_id) : 'Guest / system';
        return response()->json(['data' => $row]);
    }
}
