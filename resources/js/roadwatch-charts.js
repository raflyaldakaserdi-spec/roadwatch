import Chart from 'chart.js/auto';

const colors = {
    total: '#E995BB',
    today: '#F43F5E',
    week: '#3B82F6',
    month: '#10B981'
};

const charts = {};

function createChart(canvas, key, series) {
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const color = colors[key];

    const gradient = ctx.createLinearGradient(0, 0, 0, 58);
    gradient.addColorStop(0, color + '55');
    gradient.addColorStop(1, color + '00');

    charts[key] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: series.labels,
            datasets: [{
                data: series.values,
                borderColor: color,
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.38,
                pointRadius: 0,
                pointHoverRadius: 4,
                pointHoverBackgroundColor: color,
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 450
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    displayColors: false,
                    backgroundColor: '#1E293B',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 10,
                    cornerRadius: 9,
                    callbacks: {
                        label(context) {
                            return `${context.parsed.y} deteksi`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false,
                    beginAtZero: key !== 'total'
                }
            }
        }
    });
}

function updateCharts(data) {
    if (!data || typeof data !== 'object') return;

    for (const [key, series] of Object.entries(data)) {
        if (!charts[key] || !Array.isArray(series.labels)
            || !Array.isArray(series.values)) continue;

        charts[key].data.labels = series.labels;
        charts[key].data.datasets[0].data = series.values;
        charts[key].update('none');
    }
}


// RoadWatch Detection Analytics - Severity Donut Chart
let severityChart = null;

function updateSeverityChart(data) {
    if (!data || typeof data !== 'object') return;

    const levels = ['High', 'Medium', 'Low'];

    const values = levels.map(level => {
        const value = Number(data[level] ?? 0);
        return Number.isFinite(value) && value >= 0 ? value : 0;
    });

    const total = values.reduce((sum, value) => sum + value, 0);

    if (severityChart) {
        severityChart.data.datasets[0].data = values;
        severityChart.update('none');
    }

    levels.forEach((level, index) => {
        const row = document.querySelector(
            `[data-severity-row="${level}"]`
        );

        if (!row) return;

        const percentage = total > 0
            ? (values[index] / total * 100).toFixed(1)
            : '0.0';

        const count = row.querySelector('[data-severity-count]');
        const percent = row.querySelector('[data-severity-percent]');
        const bar = row.querySelector('[data-severity-bar]');

        if (count) count.textContent = values[index];
        if (percent) percent.textContent = percentage;
        if (bar) bar.style.width = percentage + '%';
    });

    const totalElement = document.querySelector('[data-severity-total]');

    if (totalElement) {
        totalElement.textContent = total.toLocaleString('id-ID');
    }
}

function createSeverityChart(data) {
    const canvas = document.getElementById('rwSeverityChart');

    if (!canvas || severityChart) return;

    const levels = ['High', 'Medium', 'Low'];

    const values = levels.map(level => {
        const value = Number(data?.[level] ?? 0);
        return Number.isFinite(value) && value >= 0 ? value : 0;
    });

    severityChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: levels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#F43F5E',
                    '#F59E0B',
                    '#0EA5E9'
                ],
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverOffset: 7,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '73%',
            animation: {
                duration: 650
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1E293B',
                    padding: 12,
                    cornerRadius: 9,
                    callbacks: {
                        label(context) {
                            const values = context.dataset.data;
                            const total = values.reduce(
                                (sum, value) => sum + Number(value),
                                0
                            );

                            const count = Number(context.parsed);
                            const percentage = total > 0
                                ? (count / total * 100).toFixed(1)
                                : '0.0';

                            return ` ${count} deteksi (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    updateSeverityChart(data);
}


// RoadWatch Detection Trend - Last 7 Days
let trendChart = null;

function updateTrendChart(data) {
    if (!data ||
        !Array.isArray(data.labels) ||
        !Array.isArray(data.values) ||
        data.labels.length !== data.values.length) return;

    const values = data.values.map(value => {
        const number = Number(value);
        return Number.isFinite(number) && number >= 0 ? number : 0;
    });

    if (trendChart) {
        trendChart.data.labels = data.labels;
        trendChart.data.datasets[0].data = values;
        trendChart.update('none');
    }

    const totalElement = document.querySelector('[data-trend-total]');

    if (totalElement) {
        totalElement.textContent = values
            .reduce((sum, value) => sum + value, 0)
            .toLocaleString('id-ID');
    }
}

function createTrendChart(data) {
    const canvas = document.getElementById('rwTrendChart');

    if (!canvas || trendChart ||
        !data ||
        !Array.isArray(data.labels) ||
        !Array.isArray(data.values) ||
        data.labels.length !== data.values.length) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(223, 120, 166, 0.30)');
    gradient.addColorStop(1, 'rgba(223, 120, 166, 0.01)');

    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Deteksi',
                data: data.values,
                borderColor: '#DF78A6',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#DF78A6',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#DF78A6',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 650
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 9,
                    displayColors: false,
                    callbacks: {
                        label(context) {
                            return `${context.parsed.y} deteksi`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#94A3B8'
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        color: '#94A3B8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.12)'
                    },
                    border: {
                        display: false,
                        dash: [4, 4]
                    }
                }
            }
        }
    });

    updateTrendChart(data);
}

document.addEventListener('DOMContentLoaded', () => {
    const initialData = window.roadwatchChartData;

    if (initialData) {
        document.querySelectorAll('[data-rw-chart]').forEach(canvas => {
            const key = canvas.dataset.rwChart;

            if (initialData[key]) {
                createChart(canvas, key, initialData[key]);
            }
        });
    }

    if (window.roadwatchSeverityData) {
        createSeverityChart(window.roadwatchSeverityData);
    }

    window.updateRoadwatchCharts = updateCharts;
    window.updateRoadwatchSeverity = updateSeverityChart;

    if (window.roadwatchTrendData) {
        createTrendChart(window.roadwatchTrendData);
    }

    // Detection Trend period filter (7 / 30 days)
    let selectedTrendPeriod = '7';

    let trendPeriods = window.roadwatchTrendPeriods || {};

    function renderSelectedTrendPeriod() {
        const subtitle = document.getElementById('rwTrendSubtitle');

        if (subtitle) {
            subtitle.textContent =
                `Aktivitas deteksi jalan berlubang selama ${selectedTrendPeriod} hari terakhir`;
        }

        const data = trendPeriods[selectedTrendPeriod];

        if (data) {
            updateTrendChart(data);
        }

        document.querySelectorAll('[data-trend-period]').forEach(button => {
            const active = button.dataset.trendPeriod === selectedTrendPeriod;

            button.setAttribute('aria-pressed', String(active));

            button.classList.toggle('bg-white', active);
            button.classList.toggle('shadow-sm', active);
            button.classList.toggle('text-[#DF78A6]', active);
            button.classList.toggle('text-slate-500', !active);
        });

        const totalLabel = document.querySelector('[data-trend-total]')
            ?.parentElement?.querySelector('.text-slate-500');

        if (totalLabel) {
            totalLabel.textContent = `Total ${selectedTrendPeriod} Hari:`;
        }
    }

    document.querySelectorAll('[data-trend-period]').forEach(button => {
        button.addEventListener('click', () => {
            const period = button.dataset.trendPeriod;

            if (!['7', '30'].includes(period)) return;

            selectedTrendPeriod = period;
            renderSelectedTrendPeriod();
        });
    });

    window.updateRoadwatchTrend = function(data, periods) {
        if (periods && typeof periods === 'object') {
            trendPeriods = periods;
        } else if (data) {
            trendPeriods['7'] = data;
        }

        renderSelectedTrendPeriod();
    };

    renderSelectedTrendPeriod();
});
