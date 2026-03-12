/**
 * Visitor analytics: record page visit and time spent.
 * Set window.visitorAnalyticsSource to 'frontend' | 'admin' | 'portal' before loading this script.
 */
(function () {
    'use strict';
    var source = (typeof window.visitorAnalyticsSource === 'string' && window.visitorAnalyticsSource) ? window.visitorAnalyticsSource : 'frontend';
    var baseUrl = (typeof window.visitorAnalyticsBaseUrl === 'string' && window.visitorAnalyticsBaseUrl) ? window.visitorAnalyticsBaseUrl : '';
    var token = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute ? (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute('content') : '';
    var visitStart = Date.now();
    var visitId = null;

    function recordVisit() {
        if (!token) return;
        var body = new FormData();
        body.append('_token', token);
        body.append('source', source);
        var path = (typeof window.location !== 'undefined' && window.location.pathname) ? window.location.pathname : '';
        if (path) body.append('path', path);
        fetch(baseUrl + '/record-visit', {
            method: 'POST',
            body: body,
            credentials: 'same-origin'
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data && data.visit_id) {
                visitId = data.visit_id;
                try { sessionStorage.setItem('_va_id', String(data.visit_id)); } catch (e) {}
            }
        }).catch(function () {});
    }

    function sendTimeSpent() {
        if (!visitId || !token) {
            try { visitId = sessionStorage.getItem('_va_id'); } catch (e) {}
        }
        if (!visitId) return;
        var seconds = Math.round((Date.now() - visitStart) / 1000);
        if (seconds < 1) return;
        var body = new FormData();
        body.append('_token', token);
        body.append('seconds', String(seconds));
        navigator.sendBeacon && navigator.sendBeacon(baseUrl + '/record-visit/' + visitId + '/time', body);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', recordVisit);
    } else {
        recordVisit();
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') sendTimeSpent();
    });
    window.addEventListener('pagehide', sendTimeSpent);
    window.addEventListener('beforeunload', sendTimeSpent);
})();
