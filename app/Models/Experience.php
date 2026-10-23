<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Experience extends Model
{
    use HasLocalizedContent, SoftDeletes;

    public const KINDS = ['work', 'education', 'highlight'];

    protected $attributes = ['kind' => 'work'];

    protected $fillable = [
        'key',
        'kind',
        'title_th',
        'title_en',
        'company',
        'period',
        'started_on',
        'ended_on',
        'description_th',
        'description_en',
        'tech_stack',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'is_active' => 'boolean',
        'started_on' => 'date',
        'ended_on' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('title_en');
    }

    public function isOngoing(): bool
    {
        return $this->started_on !== null && $this->ended_on === null;
    }

    /** Inclusive month count, so Jun–Oct is five months, matching how a resume reads. */
    public function durationInMonths(): ?int
    {
        if (! $this->started_on) {
            return null;
        }

        $start = $this->started_on->copy()->startOfMonth();
        $end = ($this->ended_on ?? now())->copy()->startOfMonth();

        return (int) $start->diffInMonths($end, true) + 1;
    }

    public function periodLabel(string $locale): ?string
    {
        if (! $this->started_on) {
            return $this->period;
        }

        $pattern = $this->kind === 'education' ? 'Y' : 'M Y';
        $format = fn (CarbonInterface $date): string => $date->copy()->locale($locale)->translatedFormat($pattern);
        $end = $this->ended_on ? $format($this->ended_on) : __('messages.present', [], $locale);

        return $format($this->started_on).' – '.$end;
    }

    public function durationLabel(string $locale): ?string
    {
        $months = $this->durationInMonths();

        return $months === null ? null : static::formatMonths($months, $locale);
    }

    /** @return list<string> */
    public function bullets(string $locale): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->localized('description', $locale)))
            ->map(fn (string $line) => trim(preg_replace('/^\s*[•\-–*]\s*/u', '', $line)))
            ->filter()
            ->values()
            ->all();
    }

    public static function formatMonths(int $months, string $locale): string
    {
        $years = intdiv($months, 12);
        $rest = $months % 12;
        $parts = [];

        if ($years > 0) {
            $parts[] = trans_choice('messages.duration_years', $years, ['count' => $years], $locale);
        }
        if ($rest > 0) {
            $parts[] = trans_choice('messages.duration_months', $rest, ['count' => $rest], $locale);
        }

        return implode(' ', $parts);
    }

    /**
     * Total time worked across work entries, merging overlapping jobs so they are not counted twice.
     *
     * @param  Collection<int, self>  $experiences
     * @return array{months: int, years: float, since: ?int, roles: int}
     */
    public static function careerSummary(Collection $experiences): array
    {
        $work = $experiences->filter(fn (self $item) => $item->kind === 'work');
        $ranges = $work
            ->filter(fn (self $item) => $item->started_on !== null)
            ->map(fn (self $item) => [
                $item->started_on->copy()->startOfMonth(),
                ($item->ended_on ?? now())->copy()->startOfMonth(),
            ])
            ->sortBy(fn (array $range) => $range[0]->timestamp)
            ->values();

        $months = 0;
        $coveredUntil = null;

        foreach ($ranges as [$start, $end]) {
            if ($coveredUntil !== null && $start->lte($coveredUntil)) {
                if ($end->gt($coveredUntil)) {
                    $months += (int) $coveredUntil->diffInMonths($end, true);
                    $coveredUntil = $end;
                }

                continue;
            }

            $months += (int) $start->diffInMonths($end, true) + 1;
            $coveredUntil = $end;
        }

        $first = $ranges->first();

        return [
            'months' => $months,
            'years' => round($months / 12, 1),
            'since' => $first ? $first[0]->year : null,
            'roles' => $work->count(),
        ];
    }
}
