<?php

namespace App\Http\Controllers;

use App\Models\SiteTrafficEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request): View|Response
    {
        [$start, $end, $days] = $this->resolveDateRange($request);
        $data = $this->buildAnalyticsPayload($start, $end, $days);

        if ($request->query('export') === 'csv') {
            return $this->csvExport($data['dailyTrend'], $days);
        }

        $lastEvent = SiteTrafficEvent::query()->latest('viewed_at')->first();

        return view('admin.analytics.index', array_merge($data, [
            'trackingDiagnostic' => [
                'last_event_at' => $lastEvent?->viewed_at?->toDateTimeString(),
                'total_events' => SiteTrafficEvent::query()->count(),
            ],
        ]));
    }

    public function data(Request $request): JsonResponse
    {
        [$start, $end, $days] = $this->resolveDateRange($request);

        return response()->json($this->buildAnalyticsPayload($start, $end, $days));
    }

    /**
     * @param  array<int, array{date:string,label:string,views:int,sessions:int}>  $dailyTrend
     */
    protected function csvExport(array $dailyTrend, int $days): Response
    {
        $lines = ['date,views,sessions'];
        foreach ($dailyTrend as $row) {
            $lines[] = implode(',', [$row['date'], $row['views'], $row['sessions']]);
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="analytics-'.$days.'d.csv"',
        ]);
    }

    /**
     * @return array{0:Carbon,1:Carbon,2:int}
     */
    protected function resolveDateRange(Request $request): array
    {
        $presetDays = (int) $request->integer('range', 90);
        if (! in_array($presetDays, [7, 30, 90], true)) {
            $presetDays = 90;
        }

        $startInput = trim((string) $request->query('start_date', ''));
        $endInput = trim((string) $request->query('end_date', ''));
        if ($startInput !== '' && $endInput !== '') {
            try {
                $start = Carbon::parse($startInput)->startOfDay();
                $end = Carbon::parse($endInput)->endOfDay();
                if ($start->lte($end)) {
                    $days = (int) $start->diffInDays($end) + 1;
                    return [$start, $end, max(1, $days)];
                }
            } catch (\Throwable) {
                // Fall back to preset range.
            }
        }

        $end = now();
        $start = now()->subDays($presetDays - 1)->startOfDay();

        return [$start, $end, $presetDays];
    }

    /**
     * @return array<string,mixed>
     */
    protected function buildAnalyticsPayload(Carbon $start, Carbon $end, int $days): array
    {
        $baseQuery = SiteTrafficEvent::query()->betweenDates($start, $end);

        $totalViews = (clone $baseQuery)->count();
        $uniqueSessions = (clone $baseQuery)->whereNotNull('session_id')->distinct('session_id')->count('session_id');
        $uniquePages = (clone $baseQuery)->distinct('path')->count('path');
        $topReferrer = (clone $baseQuery)->whereNotNull('referrer_host')->selectRaw('referrer_host, COUNT(*) as views')->groupBy('referrer_host')->orderByDesc('views')->first();

        $trendRows = (clone $baseQuery)
            ->selectRaw('DATE(viewed_at) as day, COUNT(*) as views, COUNT(DISTINCT session_id) as sessions')
            ->groupByRaw('DATE(viewed_at)')
            ->orderByRaw('DATE(viewed_at)')
            ->get()
            ->keyBy('day');

        $dailyTrend = $this->buildDailyTrend($start, $end, $trendRows);
        $lineChart = $this->buildLineChart($dailyTrend);
        $trendBars = $this->buildTrendBarHeights($dailyTrend);
        $kpiDeltas = $this->buildHalfRangeDeltas($dailyTrend);
        $trendXLabels = $this->buildTrendXLabels($start, $end);
        $deviceBreakdown = $this->buildDeviceBreakdown(clone $baseQuery);

        $topPages = (clone $baseQuery)
            ->selectRaw('path, COUNT(*) as views, COUNT(DISTINCT session_id) as sessions')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->label = $this->friendlyPathLabel((string) $row->path);
                return $row;
            });

        $topListings = (clone $baseQuery)
            ->whereNotNull('vehicle_slug')
            ->selectRaw('vehicle_slug, COUNT(*) as views, COUNT(DISTINCT session_id) as sessions')
            ->groupBy('vehicle_slug')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $topReferrers = (clone $baseQuery)
            ->whereNotNull('referrer_host')
            ->selectRaw('referrer_host, COUNT(*) as views')
            ->groupBy('referrer_host')
            ->orderByDesc('views')
            ->limit(6)
            ->get();

        $sessionDepthRows = (clone $baseQuery)
            ->whereNotNull('session_id')
            ->selectRaw('session_id, COUNT(*) as views')
            ->groupBy('session_id')
            ->get();

        $bounceSessions = $sessionDepthRows->where('views', 1)->count();
        $bounceRate = $sessionDepthRows->isEmpty() ? 0.0 : ($bounceSessions / $sessionDepthRows->count()) * 100;
        $avgViewsPerSession = $sessionDepthRows->isEmpty() ? 0.0 : $sessionDepthRows->avg('views');

        return [
            'rangeDays' => $days,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'summary' => [
                'total_views' => $totalViews,
                'unique_sessions' => $uniqueSessions,
                'unique_pages' => $uniquePages,
                'top_referrer' => $topReferrer?->referrer_host,
                'top_referrer_views' => (int) ($topReferrer?->views ?? 0),
                'bounce_rate' => round($bounceRate, 1),
                'avg_views_per_session' => round((float) $avgViewsPerSession, 2),
            ],
            'kpiDeltas' => $kpiDeltas,
            'dailyTrend' => $dailyTrend,
            'trendBars' => $trendBars,
            'trendXLabels' => $trendXLabels,
            'lineChart' => $lineChart,
            'deviceBreakdown' => $deviceBreakdown,
            'topPages' => $topPages,
            'topListings' => $topListings,
            'topReferrers' => $topReferrers,
        ];
    }

    protected function friendlyPathLabel(string $path): string
    {
        $normalized = trim($path);
        if ($normalized === '' || $normalized === '/') {
            return 'Home';
        }
        if (str_starts_with($normalized, 'inventory/')) {
            return 'Listing: '.str_replace('-', ' ', substr($normalized, 10));
        }
        if ($normalized === 'inventory') {
            return 'Inventory';
        }
        if ($normalized === 'compare') {
            return 'Compare';
        }

        return ucfirst(str_replace(['/', '-'], [' / ', ' '], $normalized));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, mixed>  $trendRows
     * @return array<int, array{date:string,label:string,views:int,sessions:int}>
     */
    protected function buildDailyTrend(Carbon $start, Carbon $end, Collection $trendRows): array
    {
        $rows = [];
        foreach (CarbonPeriod::create($start, $end) as $day) {
            $dateKey = $day->format('Y-m-d');
            $rows[] = [
                'date' => $dateKey,
                'label' => $day->format('M j'),
                'views' => (int) ($trendRows->get($dateKey)->views ?? 0),
                'sessions' => (int) ($trendRows->get($dateKey)->sessions ?? 0),
            ];
        }

        return $rows;
    }

    /**
     * @param  array<int, array{date:string,label:string,views:int,sessions:int}>  $dailyTrend
     * @return array<string, mixed>
     */
    protected function buildLineChart(array $dailyTrend): array
    {
        $width = 1200.0;
        $height = 320.0;
        $paddingX = 24.0;
        $paddingY = 24.0;
        $usableWidth = $width - ($paddingX * 2);
        $usableHeight = $height - ($paddingY * 2);

        $maxValue = max(1, collect($dailyTrend)->max(fn (array $point): int => max($point['views'], $point['sessions'])));
        $count = max(1, count($dailyTrend));
        $stepX = $count === 1 ? 0 : ($usableWidth / ($count - 1));

        $viewPoints = [];
        $sessionPoints = [];

        foreach ($dailyTrend as $index => $point) {
            $x = $paddingX + ($stepX * $index);
            $viewY = $paddingY + ($usableHeight * (1 - ($point['views'] / $maxValue)));
            $sessionY = $paddingY + ($usableHeight * (1 - ($point['sessions'] / $maxValue)));
            $viewPoints[] = [$x, $viewY];
            $sessionPoints[] = [$x, $sessionY];
        }

        $viewPath = $this->toPath($viewPoints);
        $sessionPath = $this->toPath($sessionPoints);
        $viewAreaPath = $this->toAreaPath($viewPoints, $height - $paddingY);

        return [
            'width' => (int) $width,
            'height' => (int) $height,
            'max_value' => $maxValue,
            'view_path' => $viewPath,
            'view_area_path' => $viewAreaPath,
            'session_path' => $sessionPath,
            'labels' => Arr::only($dailyTrend, [0, (int) floor(($count - 1) / 3), (int) floor((($count - 1) * 2) / 3), $count - 1]),
        ];
    }

    /**
     * Compare first vs second half of the range for headline trend badges.
     *
     * @param  array<int, array{date:string,label:string,views:int,sessions:int}>  $dailyTrend
     * @return array{views:?float,sessions:?float,pages:?float}
     */
    protected function buildHalfRangeDeltas(array $dailyTrend): array
    {
        $n = count($dailyTrend);
        if ($n < 4) {
            return ['views' => null, 'sessions' => null, 'pages' => null];
        }

        $mid = (int) floor($n / 2);
        $first = array_slice($dailyTrend, 0, $mid);
        $second = array_slice($dailyTrend, $mid);
        $sumV1 = array_sum(array_column($first, 'views'));
        $sumV2 = array_sum(array_column($second, 'views'));
        $sumS1 = array_sum(array_column($first, 'sessions'));
        $sumS2 = array_sum(array_column($second, 'sessions'));

        $delta = function (float $a, float $b): ?float {
            if ($a <= 0.0 && $b <= 0.0) {
                return null;
            }
            if ($a <= 0.0) {
                return $b > 0 ? 100.0 : null;
            }

            return round((($b - $a) / $a) * 100, 1);
        };

        return [
            'views' => $delta((float) $sumV1, (float) $sumV2),
            'sessions' => $delta((float) $sumS1, (float) $sumS2),
            'pages' => null,
        ];
    }

    /**
     * Resample daily trend to 12 column heights (5–100) for the traffic bar strip.
     *
     * @param  array<int, array{date:string,label:string,views:int,sessions:int}>  $dailyTrend
     * @return array<int, array{h:int,highlight:bool}>
     */
    protected function buildTrendBarHeights(array $dailyTrend): array
    {
        $demo = [60, 45, 80, 65, 50, 95, 75, 60, 40, 85, 70, 55];
        $n = count($dailyTrend);
        if ($n === 0) {
            return array_map(fn (int $h, int $i) => ['h' => $h, 'highlight' => $i === 5], $demo, array_keys($demo));
        }

        $barCount = 12;
        $heights = [];
        for ($i = 0; $i < $barCount; $i++) {
            $idx = $n === 1 ? 0 : (int) round($i * ($n - 1) / ($barCount - 1));
            $heights[] = (int) ($dailyTrend[$idx]['views'] ?? 0);
        }
        $maxV = max(1, ...$heights);
        $out = [];
        $maxIdx = 0;
        foreach ($heights as $i => $v) {
            $pct = (int) round(5 + ($v / $maxV) * 95);
            $out[] = ['h' => max(5, min(100, $pct)), 'highlight' => false];
            if ($v > ($heights[$maxIdx] ?? 0)) {
                $maxIdx = $i;
            }
        }
        foreach ($out as $i => &$row) {
            $row['highlight'] = $i === $maxIdx;
        }
        unset($row);

        return $out;
    }

    /**
     * @return array<int, string>
     */
    protected function buildTrendXLabels(Carbon $start, Carbon $end): array
    {
        $totalDays = (int) $start->diffInDays($end) + 1;
        $count = 7;
        if ($totalDays <= 1) {
            return [$start->format('M d')];
        }
        $labels = [];
        for ($i = 0; $i < $count; $i++) {
            $d = $start->copy()->addDays((int) round($i * ($totalDays - 1) / max(1, $count - 1)));
            $labels[] = $d->format('M d');
        }

        return $labels;
    }

    /**
     * @return array<int, array{label:string,value:int,percentage:float,color:string}>
     */
    protected function buildDeviceBreakdown(Builder $query): array
    {
        $events = $query->select(['user_agent'])->get();

        $buckets = [
            'Desktop' => 0,
            'Mobile' => 0,
            'Tablet' => 0,
        ];

        foreach ($events as $event) {
            $ua = strtolower((string) $event->user_agent);
            if ($ua === '') {
                $buckets['Desktop']++;
                continue;
            }
            if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) {
                $buckets['Tablet']++;
                continue;
            }
            if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
                $buckets['Mobile']++;
                continue;
            }
            $buckets['Desktop']++;
        }

        $total = max(1, array_sum($buckets));
        $colors = [
            'Desktop' => 'from-indigo-500 to-indigo-600',
            'Mobile' => 'from-emerald-500 to-emerald-600',
            'Tablet' => 'from-amber-500 to-amber-600',
        ];

        $out = [];
        foreach ($buckets as $label => $value) {
            $out[] = [
                'label' => $label,
                'value' => $value,
                'percentage' => round(($value / $total) * 100, 1),
                'color' => $colors[$label],
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, array{0:float,1:float}>  $points
     */
    protected function toPath(array $points): string
    {
        if ($points === []) {
            return '';
        }

        $chunks = [];
        foreach ($points as $index => [$x, $y]) {
            $prefix = $index === 0 ? 'M' : 'L';
            $chunks[] = sprintf('%s%.2f %.2f', $prefix, $x, $y);
        }

        return implode(' ', $chunks);
    }

    /**
     * @param  array<int, array{0:float,1:float}>  $points
     */
    protected function toAreaPath(array $points, float $baselineY): string
    {
        if ($points === []) {
            return '';
        }

        $path = $this->toPath($points);
        $last = $points[count($points) - 1];
        $first = $points[0];

        return sprintf(
            '%s L%.2f %.2f L%.2f %.2f Z',
            $path,
            $last[0],
            $baselineY,
            $first[0],
            $baselineY
        );
    }
}
