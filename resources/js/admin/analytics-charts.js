import ApexCharts from 'apexcharts';
import 'apexcharts/dist/apexcharts.css';

const NAVY = '#0B1F3A';
const BRONZE = '#A87E59';
const GRID = '#E2E8F0';
const MUTED = '#64748B';
const FONT = 'Inter, system-ui, sans-serif';

/** @type {Record<string, ApexCharts>} */
let instances = {};

let resizeBound = false;

function bindResize() {
    if (resizeBound) {
        return;
    }
    resizeBound = true;
    window.addEventListener('resize', () => {
        Object.values(instances).forEach((c) => {
            try {
                c.resize();
            } catch {
                /* ignore */
            }
        });
    });
}

function baseChartOptions() {
    return {
        chart: {
            fontFamily: FONT,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: { enabled: true, easing: 'easeinout', speed: 450 },
        },
        grid: {
            borderColor: GRID,
            strokeDashArray: 4,
            padding: { left: 8, right: 12, top: 8, bottom: 4 },
        },
        dataLabels: { enabled: false },
        legend: {
            fontFamily: FONT,
            labels: { colors: MUTED },
        },
        tooltip: {
            theme: 'light',
            style: { fontFamily: FONT },
            x: { show: true },
        },
        theme: { mode: 'light' },
    };
}

function trafficOptions(state) {
    const base = baseChartOptions();
    const trend = state.dailyTrend || [];
    const categories = trend.map((d) => String(d.label ?? ''));
    const views = trend.map((d) => Number(d.views ?? 0));
    const sessions = trend.map((d) => Number(d.sessions ?? 0));

    return {
        ...base,
        chart: {
            ...base.chart,
            type: 'area',
            height: 300,
            stacked: false,
        },
        series: [
            { name: 'Views', data: views },
            { name: 'Sessions', data: sessions },
        ],
        stroke: {
            curve: 'smooth',
            width: [2.5, 2],
            dashArray: [0, 6],
        },
        colors: [NAVY, BRONZE],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.4,
                opacityFrom: 0.42,
                opacityTo: 0.02,
                stops: [0, 100],
            },
            opacity: [0.45, 0],
        },
        markers: {
            size: 0,
            hover: { size: 5 },
        },
        grid: {
            ...base.grid,
            padding: { left: 4, right: 8, top: 12, bottom: 18 },
        },
        xaxis: {
            categories,
            tickPlacement: 'between',
            labels: {
                style: { colors: MUTED, fontSize: '10px', fontWeight: 500 },
                rotate: -35,
                rotateAlways: true,
                hideOverlappingLabels: true,
                trim: true,
                maxHeight: 120,
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: MUTED, fontSize: '11px' },
                formatter: (v) => (Number.isFinite(v) ? String(Math.round(v)) : ''),
            },
        },
        tooltip: {
            ...base.tooltip,
            y: {
                formatter: (v) => new Intl.NumberFormat().format(v ?? 0),
            },
        },
    };
}

function deviceTotalPct(rows) {
    const t = rows.reduce((a, b) => a + Number(b.percentage ?? 0), 0);
    if (t < 0.5) {
        return 100;
    }
    return Math.min(100, Math.round(t));
}

function donutOptions(state) {
    const base = baseChartOptions();
    const rows = state.deviceBreakdown || [];
    const labels = rows.map((r) => String(r.label ?? ''));
    const series = rows.map((r) => Number(r.percentage ?? 0));
    const totalPct = deviceTotalPct(rows);

    const palette = [NAVY, BRONZE, '#94a3b8'];

    return {
        ...base,
        chart: {
            ...base.chart,
            type: 'donut',
            height: 280,
        },
        series: series.length ? series : [100],
        labels: series.length ? labels : ['—'],
        colors: labels.map((_, i) => palette[i % palette.length]),
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '11px', color: MUTED, offsetY: 4, formatter: () => 'Total' },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontWeight: 700,
                            color: NAVY,
                            offsetY: -4,
                            formatter: () => `${totalPct}%`,
                        },
                        total: { show: false },
                    },
                },
            },
        },
        legend: { show: false },
        dataLabels: { enabled: false },
        tooltip: {
            ...base.tooltip,
            y: {
                formatter: (v) => `${Number(v ?? 0).toFixed(1)}%`,
            },
        },
    };
}

function referrerOptions(state) {
    const base = baseChartOptions();
    const raw = [...(state.topReferrers || [])].sort(
        (a, b) => Number(b.views ?? 0) - Number(a.views ?? 0),
    );
    const categories = raw.map((r) => String(r.referrer_host || '—'));
    const data = raw.map((r) => Number(r.views ?? 0));

    const shortCat = (v) => {
        const s = String(v ?? '');
        if (s.length <= 28) {
            return s;
        }
        return `${s.slice(0, 26)}…`;
    };

    return {
        ...base,
        chart: {
            ...base.chart,
            type: 'bar',
            height: Math.max(220, categories.length * 36 + 80),
        },
        legend: { show: false },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 6,
                barHeight: '62%',
            },
        },
        colors: [NAVY],
        series: [{ name: 'Views', data: data.length ? data : [0] }],
        xaxis: {
            categories: categories.length ? categories : ['—'],
            labels: {
                style: { colors: MUTED, fontSize: '10px', fontWeight: 500 },
                trim: true,
                maxHeight: 160,
                formatter: shortCat,
            },
        },
        yaxis: {
            labels: {
                maxWidth: 220,
                style: { colors: MUTED, fontSize: '10px', fontWeight: 500 },
            },
        },
        tooltip: {
            ...base.tooltip,
            y: {
                formatter: (v) => new Intl.NumberFormat().format(v ?? 0),
            },
        },
    };
}

function listingsOptions(state) {
    const base = baseChartOptions();
    const top = (state.topPrograms || []).filter((r) => r.route_name).slice(0, 8);
    const categories = top.map((r) => String(r.label || r.route_name || '—'));
    const data = top.map((r) => Number(r.views ?? 0));

    return {
        ...base,
        chart: {
            ...base.chart,
            type: 'bar',
            height: 260,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 8,
                columnWidth: '52%',
            },
        },
        colors: [NAVY],
        series: [{ name: 'Views', data: data.length ? data : [0] }],
        xaxis: {
            categories: categories.length ? categories : ['—'],
            labels: {
                style: { colors: MUTED, fontSize: '10px', fontWeight: 700 },
                rotate: -35,
                rotateAlways: categories.length > 5,
                hideOverlappingLabels: true,
                trim: true,
                maxHeight: 100,
            },
        },
        yaxis: {
            labels: {
                style: { colors: MUTED, fontSize: '11px' },
                formatter: (v) => new Intl.NumberFormat().format(v ?? 0),
            },
        },
        tooltip: {
            ...base.tooltip,
            y: {
                formatter: (v) => new Intl.NumberFormat().format(v ?? 0),
            },
        },
    };
}

function dailyStackedOptions(state) {
    const base = baseChartOptions();
    const points = (state.dailyTrend || []).slice(-7);
    const categories = points.map((p) => String(p.label ?? ''));
    const views = points.map((p) => Number(p.views ?? 0));
    const sessions = points.map((p) => Number(p.sessions ?? 0));

    return {
        ...base,
        chart: {
            ...base.chart,
            type: 'bar',
            height: 280,
            stacked: true,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: '58%',
            },
        },
        colors: [NAVY, BRONZE],
        series: [
            { name: 'Views', data: views.length ? views : [0] },
            { name: 'Sessions', data: sessions.length ? sessions : [0] },
        ],
        grid: {
            ...base.grid,
            padding: { left: 4, right: 8, top: 8, bottom: 16 },
        },
        xaxis: {
            categories: categories.length ? categories : ['—'],
            tickPlacement: 'between',
            labels: {
                style: { colors: MUTED, fontSize: '10px', fontWeight: 600 },
                rotate: -35,
                rotateAlways: true,
                hideOverlappingLabels: true,
                trim: true,
                maxHeight: 110,
            },
        },
        yaxis: {
            labels: {
                style: { colors: MUTED, fontSize: '11px' },
                formatter: (v) => new Intl.NumberFormat().format(v ?? 0),
            },
        },
        tooltip: {
            ...base.tooltip,
            shared: true,
            intersect: false,
            y: {
                formatter: (v) => new Intl.NumberFormat().format(v ?? 0),
            },
        },
    };
}

function engagementValue(state) {
    const bounce = Number(state.summary?.bounce_rate ?? 0);
    const v = 100 - bounce;
    return Math.max(0, Math.min(100, v));
}

function gaugeOptions(state) {
    const base = baseChartOptions();
    const val = engagementValue(state);

    return {
        ...base,
        chart: {
            ...base.chart,
            type: 'radialBar',
            height: 260,
            sparkline: { enabled: false },
        },
        series: [val],
        colors: [BRONZE],
        plotOptions: {
            radialBar: {
                hollow: { size: '72%' },
                track: { background: GRID },
                dataLabels: {
                    show: true,
                    name: { show: false },
                    value: {
                        show: true,
                        offsetY: 4,
                        fontSize: '22px',
                        fontWeight: 800,
                        color: NAVY,
                        formatter: () => `${val.toFixed(1)}%`,
                    },
                },
            },
        },
        stroke: { lineCap: 'round' },
        labels: [''],
    };
}

const REF_KEYS = [
    'trafficChart',
    'donutChart',
    'referrerChart',
    'listingsChart',
    'dailyChart',
    'gaugeChart',
];

/**
 * @param {{ state: object, refs: Record<string, HTMLElement> }} params
 */
export async function initCharts({ state, refs }) {
    destroyCharts();
    bindResize();

    const builders = [
        { key: 'trafficChart', opts: trafficOptions(state) },
        { key: 'donutChart', opts: donutOptions(state) },
        { key: 'referrerChart', opts: referrerOptions(state) },
        { key: 'listingsChart', opts: listingsOptions(state) },
        { key: 'dailyChart', opts: dailyStackedOptions(state) },
        { key: 'gaugeChart', opts: gaugeOptions(state) },
    ];

    for (const { key, opts } of builders) {
        const el = refs[key];
        if (!el) {
            continue;
        }
        const chart = new ApexCharts(el, opts);
        await chart.render();
        instances[key] = chart;
    }
}

export function updateCharts(state) {
    const pairs = [
        ['trafficChart', trafficOptions(state)],
        ['donutChart', donutOptions(state)],
        ['referrerChart', referrerOptions(state)],
        ['listingsChart', listingsOptions(state)],
        ['dailyChart', dailyStackedOptions(state)],
        ['gaugeChart', gaugeOptions(state)],
    ];

    for (const [key, opts] of pairs) {
        const chart = instances[key];
        if (!chart) {
            continue;
        }
        try {
            chart.updateOptions(opts, false, true);
        } catch {
            /* chart may be mid-destroy */
        }
    }
}

export function destroyCharts() {
    for (const key of REF_KEYS) {
        const c = instances[key];
        if (c) {
            try {
                c.destroy();
            } catch {
                /* ignore */
            }
            delete instances[key];
        }
    }
}
