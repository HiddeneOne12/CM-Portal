/**
 * Admin dashboard – chart initialization (C3.js)
 * Expects window.dashboardStats (object of counts) to be set by the page.
 */
(function () {
    'use strict';

    function initDashboardChart() {
        if (typeof c3 === 'undefined') {
            console.warn('C3 not loaded. Dashboard chart skipped.');
            return;
        }

        var stats = window.dashboardStats || {};
        var labels = [
            'Companies',
            'Interviews',
            'Events',
            'Documents',
            'Training',
            'Reports'
        ];
        var keys = [
            'companies',
            'interviews',
            'events',
            'documents',
            'training',
            'reports'
        ];
        var values = keys.map(function (k) {
            return stats[k] || 0;
        });

        var bindto = document.getElementById('dashboardStatsChart');
        if (!bindto) return;

        c3.generate({
            bindto: '#dashboardStatsChart',
            data: {
                columns: [
                    ['Count'].concat(values)
                ],
                type: 'bar',
                names: {
                    Count: 'Count'
                },
                color: function () {
                    return '#483183';
                }
            },
            axis: {
                rotated: true,
                x: {
                    type: 'category',
                    categories: labels
                },
                y: {
                    tick: {
                        fit: true,
                        format: function (v) {
                            return Math.round(v);
                        }
                    }
                }
            },
            legend: {
                show: false
            },
            bar: {
                width: {
                    ratio: 0.6
                }
            },
            padding: {
                top: 16,
                right: 24,
                bottom: 16,
                left: 80
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardChart);
    } else {
        initDashboardChart();
    }
})();
