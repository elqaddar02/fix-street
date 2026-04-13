import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    // Shared Chart Config
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // COUNTER ANIMATION
    document.querySelectorAll('.counter').forEach(counter => {
        let target = parseInt(counter.getAttribute('data-target'), 10);
        if (isNaN(target)) return;
        let count = 0;
        let update = () => {
            let increment = target / 40;
            count += increment;
            if (count < target) {
                counter.innerText = Math.floor(count);
                requestAnimationFrame(update);
            } else {
                counter.innerText = target;
            }
        };
        update();
    });

    // STATUS CHART
    const statusChartEl = document.getElementById('statusChart');
    if (statusChartEl) {
        new Chart(statusChartEl, {
            type: 'doughnut',
            data: {
                labels: ['Ouvert', 'En cours', 'Résolu', 'Rejeté'],
                datasets: [{
                    data: window.statusCounts || [0, 0, 0, 0],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#f43f5e'],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '80%',
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // CATEGORY CHART
    const categoryChartEl = document.getElementById('categoryChart');
    if (categoryChartEl) {
        new Chart(categoryChartEl, {
            type: 'bar',
            data: {
                labels: window.categoryLabels || [],
                datasets: [{
                    data: window.categoryData || [],
                    backgroundColor: '#6366f1',
                    borderRadius: 12,
                    barThickness: 32
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { display: false }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
});