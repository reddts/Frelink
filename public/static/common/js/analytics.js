(function () {
    function getVisitorToken() {
        var key = 'frelink_visitor_token';
        try {
            var token = window.localStorage.getItem(key);
            if (!token) {
                token = 'fv_' + Math.random().toString(36).slice(2) + Date.now().toString(36);
                window.localStorage.setItem(key, token);
            }
            return token;
        } catch (e) {
            return 'fv_' + Date.now().toString(36);
        }
    }

    function send(payload) {
        if (!window.analyticsEndpoint) {
            return;
        }
        payload = payload || {};
        payload.visitor_token = getVisitorToken();
        payload.referrer = document.referrer || '';
        var body = JSON.stringify(payload);

        try {
            if (navigator.sendBeacon) {
                navigator.sendBeacon(window.analyticsEndpoint, new Blob([body], {type: 'application/json'}));
                return;
            }
        } catch (e) {}

        if (!window.fetch) {
            return;
        }

        fetch(window.analyticsEndpoint, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'version': 'v1'},
            body: body,
            credentials: 'same-origin',
            keepalive: true
        }).catch(function () {});
    }

    function extract(el) {
        if (!el) {
            return null;
        }
        return {
            item_type: el.getAttribute('data-analytics-type') || '',
            item_id: parseInt(el.getAttribute('data-analytics-id') || '0', 10),
            list_key: el.getAttribute('data-analytics-list') || '',
            position: parseInt(el.getAttribute('data-analytics-position') || '0', 10),
            source: el.getAttribute('data-analytics-source') || ''
        };
    }

    function eventKey(data, eventType) {
        return [eventType, data.item_type, data.item_id, data.list_key, data.position, data.source, location.pathname].join(':');
    }

    var impressionSeen = new Set();
    var detailSeen = new Set();
    var observer = null;

    function sendImpression(el) {
        var data = extract(el);
        if (!data || !data.item_type || !data.item_id) {
            return;
        }
        var key = eventKey(data, 'impression');
        if (impressionSeen.has(key)) {
            return;
        }
        impressionSeen.add(key);
        send({
            event_type: 'impression',
            item_type: data.item_type,
            item_id: data.item_id,
            list_key: data.list_key,
            position: data.position,
            source: data.source
        });
    }

    function observe(el) {
        if (!observer) {
            if ('IntersectionObserver' in window) {
                observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting || entry.intersectionRatio < 0.5) {
                            return;
                        }
                        sendImpression(entry.target);
                        observer.unobserve(entry.target);
                    });
                }, {threshold: [0.5]});
            } else {
                observer = {
                    observe: sendImpression
                };
            }
        }
        observer.observe(el);
    }

    function scan(root) {
        root = root || document;
        if (!root.querySelectorAll) {
            return;
        }
        root.querySelectorAll('.js-analytics-impression').forEach(function (node) {
            observe(node);
        });
    }

    function trackDetailView(payload) {
        if (!payload || !payload.item_type || !payload.item_id) {
            return;
        }
        var key = eventKey(payload, 'detail_view');
        if (detailSeen.has(key)) {
            return;
        }
        detailSeen.add(key);
        send({
            event_type: 'detail_view',
            item_type: payload.item_type,
            item_id: payload.item_id,
            list_key: payload.list_key || 'detail',
            position: payload.position || 0,
            source: payload.source || 'detail_page'
        });
    }

    document.addEventListener('click', function (event) {
        var target = event.target.closest('.js-analytics-click');
        if (!target) {
            return;
        }
        var data = extract(target) || extract(target.closest('.js-analytics-impression'));
        if (!data || !data.item_type || !data.item_id) {
            return;
        }
        send({
            event_type: 'click',
            item_type: data.item_type,
            item_id: data.item_id,
            list_key: data.list_key,
            position: data.position,
            source: data.source
        });
    }, true);

    if ('MutationObserver' in window) {
        document.addEventListener('DOMContentLoaded', function () {
            var mo = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType !== 1) {
                            return;
                        }
                        if (node.matches && node.matches('.js-analytics-impression')) {
                            observe(node);
                        }
                        scan(node);
                    });
                });
            });
            mo.observe(document.body, {childList: true, subtree: true});
        });
    }

    window.FrelinkAnalytics = {
        scan: scan,
        trackDetailView: trackDetailView
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            scan(document);
        });
    } else {
        scan(document);
    }
})();
