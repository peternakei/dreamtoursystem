<?php

namespace App\Project\Modules\Core\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AuditLogger
{
    public static function context(): array
    {
        $request = request();
        return [
            'request_id' => $request->attributes->get('audit_request_id'),
            'user_id' => $request->attributes->get('audit_user_id') ?? auth()->id(),
            'occurred_at' => now()->format('Y-m-d H:i:s.u'),
        ];
    }

    public static function write(string $table, array $data): void
    {
        try {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = json_encode($value, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
                }
            }
            DB::connection(config('activitylog.database_connection'))->table($table)->insert(array_merge([
                'uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now(),
            ], $data));
        } catch (Throwable $e) {
            // Never recurse through Laravel's exception reporter or expose SQL/bindings.
            error_log('Audit storage unavailable: '.get_class($e));
        }
    }

    public static function redact(array $data, array $hidden = [], int $depth = 0): array
    {
        if ($depth > 5) return ['notice' => '[TRUNCATED]'];
        $result = [];
        foreach (array_slice($data, 0, 150, true) as $key => $value) {
            if (in_array($key, $hidden, true) || preg_match('/password|token|secret|authorization|cookie|api.?key|app.?key|credential|card|cvv|pin|private.?key|value|content|body/i', (string) $key)) {
                $result[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $result[$key] = $depth < 5 ? self::redact($value, $hidden, $depth + 1) : '[TRUNCATED]';
            } elseif (is_string($value)) {
                // Structured strings can contain nested credentials; do not bypass redaction.
                $decoded = json_decode($value, true);
                $result[$key] = is_array($decoded) ? self::redact($decoded, $hidden, $depth + 1) : Str::limit($value, 2000);
            } elseif (is_scalar($value) || $value === null) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function model(string $event, Model $model): void
    {
        if ((!str_starts_with(get_class($model), 'App\\Project\\Modules\\') && !str_starts_with(get_class($model), 'Spatie\\Permission\\Models\\')) || str_contains(get_class($model), '\\Logs\\')) {
            return;
        }
        $changes = $event === 'updated' ? $model->getChanges() : $model->getAttributes();
        if ($event === 'updated' && !array_diff(array_keys($changes), ['updated_at', 'remember_token'])) {
            return;
        }
        $before = $event === 'created' ? null : ($event === 'updated'
            ? array_intersect_key($model->getRawOriginal(), $changes) : $model->getAttributes());
        $data = array_merge(self::context(), [
            'module' => class_basename($model), 'record_id' => (string) $model->getKey(),
            'action' => $event, 'type' => 'SYS_LOG', 'message' => class_basename($model).' '.$event,
            'old_data' => $before === null ? null : self::redact($before, $model->getHidden()),
            'new_data' => $event === 'deleted' ? null : self::redact($changes, $model->getHidden()),
        ]);
        $model->getConnection()->afterCommit(fn () => self::write('activity_logs', $data));
    }

    public static function error(Throwable $exception): void
    {
        // Raw exception messages/trace arguments may include SQL passwords or customer data.
        $frames = array_map(fn ($frame) => array_intersect_key($frame, array_flip(['file', 'line', 'class', 'function'])), $exception->getTrace());
        self::write('error_logs', array_merge(self::context(), [
            'module' => request()->route()?->getName(), 'action' => request()->method(),
            'message' => get_class($exception),
            'stack_trace' => json_encode(array_slice($frames, 0, 30), JSON_INVALID_UTF8_SUBSTITUTE),
            'context' => ['file' => $exception->getFile(), 'line' => $exception->getLine(), 'code' => $exception->getCode()],
        ]));
    }
}
