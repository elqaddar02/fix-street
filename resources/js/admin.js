import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/* ------------------------------------------------------------------ *
 * Toasts
 *
 * Small stack in the corner. Each toast can carry one action, which is
 * what makes "undo" cheaper than a confirm() dialog up front.
 * ------------------------------------------------------------------ */

function toastRoot() {
    let root = document.getElementById('admin-toasts');
    if (!root) {
        root = document.createElement('div');
        root.id = 'admin-toasts';
        root.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(root);
    }
    return root;
}

const TOAST_TONES = {
    success: 'border-emerald-200 bg-white text-slate-800',
    error: 'border-rose-200 bg-white text-rose-800',
};

export function toast(message, { tone = 'success', action = null, duration = 6000 } = {}) {
    const el = document.createElement('div');
    el.className = `pointer-events-auto flex items-center gap-4 rounded-xl border px-4 py-3 shadow-lg shadow-slate-900/5 text-sm transition-all duration-200 translate-y-2 opacity-0 ${TOAST_TONES[tone] || TOAST_TONES.success}`;

    const text = document.createElement('span');
    text.className = 'font-medium';
    text.textContent = message;
    el.appendChild(text);

    if (action) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ml-auto shrink-0 rounded-lg px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors';
        btn.textContent = action.label;
        btn.addEventListener('click', () => {
            dismiss();
            action.onClick();
        });
        el.appendChild(btn);
    }

    toastRoot().appendChild(el);
    requestAnimationFrame(() => el.classList.remove('translate-y-2', 'opacity-0'));

    let timer = setTimeout(dismiss, duration);
    el.addEventListener('mouseenter', () => clearTimeout(timer));
    el.addEventListener('mouseleave', () => {
        timer = setTimeout(dismiss, 2000);
    });

    function dismiss() {
        clearTimeout(timer);
        el.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => el.remove(), 200);
    }

    return dismiss;
}

/* ------------------------------------------------------------------ *
 * Status toggles
 *
 * The old flow was: pick a value in a <select>, answer a confirm()
 * dialog, wait for a full page reload, then hunt for the row again.
 * This flips the switch straight away, saves in the background, and
 * offers an undo instead of asking permission first.
 * ------------------------------------------------------------------ */

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function paintToggle(form, active) {
    const button = form.querySelector('[data-status-switch]');
    const knob = form.querySelector('[data-status-knob]');
    const text = form.querySelector('[data-status-text]');
    const input = form.querySelector('[data-status-value]');

    button.setAttribute('aria-checked', active ? 'true' : 'false');
    button.classList.toggle('bg-emerald-500', active);
    button.classList.toggle('bg-slate-300', !active);
    knob.classList.toggle('translate-x-5', active);
    knob.classList.toggle('translate-x-0', !active);
    text.textContent = active ? 'Actif' : 'Inactif';
    text.classList.toggle('text-emerald-700', active);
    text.classList.toggle('text-slate-500', !active);

    // The hidden input always carries the value the *next* submit would send.
    input.value = active ? 0 : 1;
}

async function submitStatus(form, active, { announce = true } = {}) {
    const button = form.querySelector('[data-status-switch]');
    const input = form.querySelector('[data-status-value]');
    const field = input.name;
    const previous = !active;

    paintToggle(form, active);
    button.disabled = true;

    const body = new FormData();
    body.append('_method', 'PATCH');
    body.append(field, active ? 1 : 0);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json().catch(() => ({}));

        // Trust the server's value over the optimistic one.
        if (typeof data.active === 'boolean' && data.active !== active) {
            paintToggle(form, data.active);
        }

        if (announce) {
            const label = form.dataset.label;
            const agreed = form.dataset.feminine === '1' ? 'e' : '';
            const verb = active ? `activé${agreed}` : `désactivé${agreed}`;
            toast(label ? `${label} ${verb}.` : data.message || 'Statut mis à jour.', {
                action: {
                    label: 'Annuler',
                    onClick: () => submitStatus(form, previous, { announce: false }),
                },
            });
        }
    } catch (error) {
        // Roll back so the switch never shows a state the server rejected.
        paintToggle(form, previous);
        toast("Le statut n'a pas pu être enregistré.", { tone: 'error' });
    } finally {
        button.disabled = false;
    }
}

function initStatusToggles() {
    document.querySelectorAll('form[data-status-toggle]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const active = form.querySelector('[data-status-switch]').getAttribute('aria-checked') !== 'true';
            submitStatus(form, active);
        });
    });
}

/* ------------------------------------------------------------------ *
 * Multi-value status selects (report workflow)
 * ------------------------------------------------------------------ */

const SELECT_TONES = {
    OPEN: ['border-amber-200', 'bg-amber-50', 'text-amber-800'],
    IN_PROGRESS: ['border-sky-200', 'bg-sky-50', 'text-sky-800'],
    RESOLVED: ['border-emerald-200', 'bg-emerald-50', 'text-emerald-800'],
    REJECTED: ['border-rose-200', 'bg-rose-50', 'text-rose-800'],
};

function paintSelect(select) {
    Object.values(SELECT_TONES).forEach((classes) => select.classList.remove(...classes));
    const tone = SELECT_TONES[select.value];
    if (tone) select.classList.add(...tone);
}

function initStatusSelects() {
    document.querySelectorAll('select[data-status-select]').forEach((select) => {
        let previous = select.value;

        select.addEventListener('change', async () => {
            const chosen = select.value;
            const priorValue = previous;
            previous = chosen;
            select.disabled = true;
            paintSelect(select);

            // The select carries its own endpoint rather than sitting in a
            // <form>: these rows live inside the bulk-action form, and nested
            // forms are invalid HTML that browsers silently discard.
            const body = new FormData();
            body.set('_method', 'PATCH');
            body.set(select.name, chosen);

            try {
                const response = await fetch(select.dataset.action, {
                    method: 'POST',
                    body,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const chosenLabel = select.options[select.selectedIndex].text;
                toast(`Statut : ${chosenLabel}.`, {
                    action: {
                        label: 'Annuler',
                        onClick: () => {
                            select.value = priorValue;
                            previous = priorValue;
                            select.dispatchEvent(new Event('change'));
                        },
                    },
                });
            } catch (error) {
                select.value = priorValue;
                previous = priorValue;
                paintSelect(select);
                toast("Le statut n'a pas pu être enregistré.", { tone: 'error' });
            } finally {
                select.disabled = false;
            }
        });
    });
}

/* ------------------------------------------------------------------ *
 * Charts
 * ------------------------------------------------------------------ */

const PALETTE = {
    indigo: '#6366f1',
    amber: '#f59e0b',
    sky: '#0ea5e9',
    emerald: '#10b981',
    rose: '#f43f5e',
    slate: '#94a3b8',
};

const BASE = {
    chart: {
        fontFamily: 'Figtree, ui-sans-serif, system-ui, sans-serif',
        toolbar: { show: false },
        zoom: { enabled: false },
        animations: { easing: 'easeinout', speed: 500 },
    },
    dataLabels: { enabled: false },
    tooltip: {
        style: { fontSize: '12px' },
        y: { formatter: (value) => `${value}` },
    },
    grid: {
        borderColor: '#f1f5f9',
        strokeDashArray: 4,
        padding: { left: 8, right: 8 },
    },
};

// ApexCharts is by far the heaviest dependency here and only the dashboard
// needs it, so it is pulled in on demand rather than shipped to every admin page.
let ApexCharts = null;

async function loadApex() {
    if (!ApexCharts) {
        ApexCharts = (await import('apexcharts')).default;
    }
    return ApexCharts;
}

function merge(...objects) {
    return objects.reduce((acc, obj) => {
        Object.entries(obj || {}).forEach(([key, value]) => {
            acc[key] = value && typeof value === 'object' && !Array.isArray(value)
                ? merge(acc[key] || {}, value)
                : value;
        });
        return acc;
    }, {});
}

function render(Apex, selector, options) {
    const el = document.querySelector(selector);
    if (!el) return null;
    const chart = new Apex(el, merge(BASE, options));
    chart.render();
    return chart;
}

async function initCharts() {
    const data = window.dashboardData;
    if (!data) return;

    const Apex = await loadApex();

    // Daily report volume.
    render(Apex, '#chart-activity', {
        chart: { type: 'area', height: 300, sparkline: { enabled: false } },
        series: [{ name: 'Signalements', data: data.activity.map((d) => d.count) }],
        xaxis: {
            type: 'datetime',
            categories: data.activity.map((d) => d.date),
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8', fontSize: '11px' }, formatter: (v) => Math.round(v) },
        },
        colors: [PALETTE.indigo],
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 100] },
        },
        tooltip: { x: { format: 'dd MMM yyyy' } },
    });

    // Status split.
    render(Apex, '#chart-status', {
        chart: { type: 'donut', height: 260 },
        series: data.status.values,
        labels: data.status.labels,
        colors: [PALETTE.amber, PALETTE.sky, PALETTE.emerald, PALETTE.rose],
        stroke: { width: 0 },
        legend: { show: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '76%',
                    labels: {
                        show: true,
                        value: {
                            fontSize: '28px',
                            fontWeight: 700,
                            color: '#0f172a',
                            offsetY: 4,
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '11px',
                            fontWeight: 600,
                            color: '#94a3b8',
                            formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                        },
                    },
                },
            },
        },
    });

    // Volume per category.
    render(Apex, '#chart-categories', {
        chart: { type: 'bar', height: 320 },
        series: [{ name: 'Signalements', data: data.categories.values }],
        xaxis: {
            categories: data.categories.labels,
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: { labels: { style: { colors: '#64748b', fontSize: '12px' } } },
        colors: [PALETTE.indigo],
        plotOptions: {
            bar: { horizontal: true, borderRadius: 6, borderRadiusApplication: 'end', barHeight: '62%' },
        },
        grid: { xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
    });
}

/* ------------------------------------------------------------------ */

/* ------------------------------------------------------------------ *
 * Flash messages
 *
 * Server-side flashes (redirect-based actions such as delete or create)
 * land in the same toast stack as the inline ones, so feedback appears
 * in one consistent place.
 * ------------------------------------------------------------------ */

function initFlash() {
    const node = document.getElementById('flash-message');
    if (!node) return;
    try {
        const { message, tone } = JSON.parse(node.textContent);
        if (message) toast(message, { tone });
    } catch (error) {
        /* malformed flash payload is not worth breaking the page over */
    }
}

/* ------------------------------------------------------------------ */

document.addEventListener('DOMContentLoaded', () => {
    initStatusToggles();
    initStatusSelects();
    initCharts();
    initFlash();
});
