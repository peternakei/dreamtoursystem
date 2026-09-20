<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\QuotationDay;
use App\Project\Modules\System\Quotations\QuotationDayActivity;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripDay;
use App\Project\Modules\System\Trips\TripDayActivity;
use Illuminate\Support\Carbon;

class ItineraryService
{
    public function seedTripDayPayloads(Trip $trip): array
    {
        if ($trip->tripDays()->count() > 0) {
            return $trip->tripDays()->with('activities')->get()->map(function (TripDay $day) {
                return [
                    'day_number' => $day->day_number,
                    'title' => $day->title,
                    'destination_id' => $day->destination_id,
                    'accommodation_name' => $day->accommodation_name,
                    'accommodation_notes' => $day->accommodation_notes,
                    'stay_type' => $day->stay_type,
                    'nights' => $day->nights,
                    'breakfast' => $day->breakfast,
                    'lunch' => $day->lunch,
                    'dinner' => $day->dinner,
                    'description' => $day->description,
                    'activity_lines' => $day->activities->pluck('title')->filter()->implode("\n"),
                ];
            })->all();
        }

        $tripDestinations = $trip->destinations()->with('destination')->get()->values();
        $tripPoints = $trip->points()->get()->values();
        $rows = [];

        if ($tripPoints->count() > 0) {
            foreach ($tripPoints as $index => $point) {
                $mappedDestination = $tripDestinations->get($index)?->destination;
                $rows[] = [
                    'day_number' => $index + 1,
                    'title' => $point->title,
                    'destination_id' => $mappedDestination?->id,
                    'accommodation_name' => null,
                    'accommodation_notes' => null,
                    'stay_type' => null,
                    'nights' => $this->defaultNightsForIndex($index, $tripPoints->count()),
                    'breakfast' => true,
                    'lunch' => true,
                    'dinner' => true,
                    'description' => $point->description,
                    'activity_lines' => '',
                ];
            }

            return $rows;
        }

        if ($tripDestinations->count() > 0) {
            foreach ($tripDestinations as $index => $tripDestination) {
                $rows[] = [
                    'day_number' => $index + 1,
                    'title' => $tripDestination->destination?->name,
                    'destination_id' => $tripDestination->destination_id,
                    'accommodation_name' => null,
                    'accommodation_notes' => null,
                    'stay_type' => null,
                    'nights' => $this->defaultNightsForIndex($index, $tripDestinations->count()),
                    'breakfast' => true,
                    'lunch' => true,
                    'dinner' => true,
                    'description' => $tripDestination->description ?: $tripDestination->destination?->description,
                    'activity_lines' => '',
                ];
            }

            return $rows;
        }

        $days = $this->resolveDateRangeDays($trip->from_date, $trip->to_date);
        for ($index = 0; $index < $days; $index++) {
            $rows[] = [
                'day_number' => $index + 1,
                'title' => 'Day ' . ($index + 1),
                'destination_id' => null,
                'accommodation_name' => null,
                'accommodation_notes' => null,
                'stay_type' => null,
                'nights' => $this->defaultNightsForIndex($index, $days),
                'breakfast' => true,
                'lunch' => true,
                'dinner' => true,
                'description' => null,
                'activity_lines' => '',
            ];
        }

        return $rows;
    }

    public function seedQuotationDayPayloads(?Trip $trip, Inquiry $inquiry): array
    {
        if ($trip) {
            $trip->loadMissing(['tripDays.activities']);

            if ($trip->tripDays->count() > 0) {
                return $trip->tripDays->map(function (TripDay $day) {
                    return [
                        'trip_day_id' => $day->id,
                        'day_number' => $day->day_number,
                        'travel_date' => null,
                        'title' => $day->title,
                        'destination_id' => $day->destination_id,
                        'accommodation_name' => $day->accommodation_name,
                        'accommodation_notes' => $day->accommodation_notes,
                        'stay_type' => $day->stay_type,
                        'nights' => $day->nights,
                        'breakfast' => $day->breakfast,
                        'lunch' => $day->lunch,
                        'dinner' => $day->dinner,
                        'description' => $day->description,
                        'activity_lines' => $day->activities->pluck('title')->filter()->implode("\n"),
                    ];
                })->all();
            }
        }

        $destinationIds = collect($inquiry->destinations ?? [])->filter()->values();
        $firstDestination = $destinationIds->isNotEmpty()
            ? Destination::find($destinationIds->first())
            : null;

        return [[
            'day_number' => 1,
            'travel_date' => $inquiry->from_date ? Carbon::parse($inquiry->from_date)->toDateString() : null,
            'title' => $firstDestination?->name ?: 'Day 1',
            'destination_id' => $firstDestination?->id,
            'accommodation_name' => null,
            'accommodation_notes' => null,
            'stay_type' => null,
            'nights' => 0,
            'breakfast' => true,
            'lunch' => true,
            'dinner' => true,
            'description' => $firstDestination?->description ?: $inquiry->client_message ?: $inquiry->description,
            'activity_lines' => '',
        ]];
    }

    public function syncTripDays(Trip $trip, array $rows, int $userId): void
    {
        $trip->tripDays()->with('activities')->get()->each(function (TripDay $day) {
            $day->activities()->delete();
            $day->delete();
        });

        $normalizedRows = $this->normalizeRows($rows);
        $daysCount = count($normalizedRows);
        $totalNights = 0;

        foreach ($normalizedRows as $index => $row) {
            $day = $trip->tripDays()->create([
                'day_number' => (int) ($row['day_number'] ?? ($index + 1)),
                'title' => ($row['title'] ?? null) ?: null,
                'destination_id' => ($row['destination_id'] ?? null) ?: null,
                'accommodation_id' => ($row['accommodation_id'] ?? null) ?: null,
                'accommodation_name' => ($row['accommodation_name'] ?? null) ?: null,
                'accommodation_notes' => ($row['accommodation_notes'] ?? null) ?: null,
                'stay_type' => ($row['stay_type'] ?? null) ?: null,
                'nights' => (int) ($row['nights'] ?? 0),
                'breakfast' => (bool) ($row['breakfast'] ?? false),
                'lunch' => (bool) ($row['lunch'] ?? false),
                'dinner' => (bool) ($row['dinner'] ?? false),
                'description' => ($row['description'] ?? null) ?: null,
                'sort_order' => $index + 1,
                'created_by' => $userId,
            ]);

            $this->syncTripDayActivities($day, $row['activity_lines'] ?? '', $userId);
            $totalNights += (int) ($row['nights'] ?? 0);
        }

        $trip->update([
            'duration_days' => $daysCount,
            'duration_nights' => $totalNights,
            'updated_by' => $userId,
        ]);
    }

    public function syncQuotationDays(QuotationVersion $version, array $rows, int $userId): void
    {
        $version->days()->with('activities')->get()->each(function (QuotationDay $day) {
            $day->activities()->delete();
            $day->delete();
        });

        $normalizedRows = $this->normalizeRows($rows);
        $daysCount = count($normalizedRows);
        $totalNights = 0;

        foreach ($normalizedRows as $index => $row) {
            $day = $version->days()->create([
                'trip_day_id' => ($row['trip_day_id'] ?? null) ?: null,
                'day_number' => (int) ($row['day_number'] ?? ($index + 1)),
                'travel_date' => ($row['travel_date'] ?? null) ?: null,
                'title' => ($row['title'] ?? null) ?: null,
                'destination_id' => ($row['destination_id'] ?? null) ?: null,
                'accommodation_id' => ($row['accommodation_id'] ?? null) ?: null,
                'accommodation_name' => ($row['accommodation_name'] ?? null) ?: null,
                'accommodation_notes' => ($row['accommodation_notes'] ?? null) ?: null,
                'stay_type' => ($row['stay_type'] ?? null) ?: null,
                'nights' => (int) ($row['nights'] ?? 0),
                'breakfast' => (bool) ($row['breakfast'] ?? false),
                'lunch' => (bool) ($row['lunch'] ?? false),
                'dinner' => (bool) ($row['dinner'] ?? false),
                'description' => ($row['description'] ?? null) ?: null,
                'sort_order' => $index + 1,
                'created_by' => $userId,
            ]);

            $this->syncQuotationDayActivities($day, $row['activity_ids'] ?? [], $userId, $version->currency_id);
            $totalNights += (int) ($row['nights'] ?? 0);
        }

        $version->update([
            'duration_days' => $daysCount,
            'duration_nights' => $totalNights,
            'updated_by' => $userId,
        ]);
    }

    protected function syncTripDayActivities(TripDay $day, string $activityLines, int $userId): void
    {
        foreach ($this->normalizeActivityLines($activityLines) as $index => $line) {
            $day->activities()->create([
                'title' => $line,
                'description' => null,
                'is_optional' => false,
                'sort_order' => $index + 1,
                'created_by' => $userId,
            ]);
        }
    }

    protected function syncQuotationDayActivities(QuotationDay $day, $activityIds, int $userId, ?int $currencyId): void
    {
        $ids = collect(is_array($activityIds) ? $activityIds : [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $library = Activity::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $index => $activityId) {
            $activity = $library->get($activityId);
            if (!$activity) {
                continue;
            }

            $day->activities()->create([
                'activity_id' => $activity->id,
                'title' => $activity->name,
                'description' => $activity->description,
                'is_optional' => false,
                'price' => 0,
                'currency_id' => $currencyId,
                'sort_order' => $index + 1,
                'created_by' => $userId,
            ]);
        }
    }

    protected function normalizeRows(array $rows): array
    {
        return collect($rows)
            ->map(function ($row, $index) {
                $defaults = [
                    'trip_day_id' => null,
                    'day_number' => $index + 1,
                    'travel_date' => null,
                    'title' => null,
                    'destination_id' => null,
                    'accommodation_name' => null,
                    'accommodation_notes' => null,
                    'stay_type' => null,
                    'nights' => 0,
                    'breakfast' => false,
                    'lunch' => false,
                    'dinner' => false,
                    'description' => null,
                    'activity_lines' => '',
                    'activity_ids' => [],
                ];

                return array_merge($defaults, is_array($row) ? $row : []);
            })
            ->filter(function (array $row) {
                return collect($row)->filter(function ($value, $key) {
                    return !in_array($key, ['breakfast', 'lunch', 'dinner'], true) && $value !== null && $value !== '';
                })->isNotEmpty();
            })
            ->sortBy(function (array $row, $index) {
                return (int) ($row['day_number'] ?? ($index + 1));
            })
            ->values()
            ->all();
    }

    protected function normalizeActivityLines(string $activityLines): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $activityLines))
            ->map(fn($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }

    protected function defaultNightsForIndex(int $index, int $count): int
    {
        return $count > 1 && $index < ($count - 1) ? 1 : 0;
    }

    protected function resolveDateRangeDays($fromDate, $toDate): int
    {
        if (!$fromDate || !$toDate) {
            return 1;
        }

        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        return max($from->diffInDays($to) + 1, 1);
    }
}
