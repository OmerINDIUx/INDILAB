/**
 * INDI Lab Analytics Tracker
 * Tracks page views, scroll depth, time on page, and user interactions
 */

(function () {
    "use strict";

    // Configuration
    const config = window.analyticsConfig || {
        endpoint: "/api/analytics/track",
        sessionId: generateSessionId(),
        userId: null,
    };

    // State
    let pageViewId = null;
    let startTime = Date.now();
    let maxScrollDepth = 0;
    let isTracking = true;
    let eventQueue = [];
    let flushInterval = null;

    // Generate session ID if not provided
    function generateSessionId() {
        let sessionId = sessionStorage.getItem("analytics_session_id");
        if (!sessionId) {
            sessionId =
                "sess_" +
                Date.now() +
                "_" +
                Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem("analytics_session_id", sessionId);
        }
        return sessionId;
    }

    // Get device type
    function getDeviceType() {
        const width = window.innerWidth;
        if (width < 768) return "mobile";
        if (width < 1024) return "tablet";
        return "desktop";
    }

    // Get browser name
    function getBrowser() {
        const ua = navigator.userAgent;
        if (ua.indexOf("Firefox") > -1) return "Firefox";
        if (ua.indexOf("Chrome") > -1) return "Chrome";
        if (ua.indexOf("Safari") > -1) return "Safari";
        if (ua.indexOf("Edge") > -1) return "Edge";
        if (ua.indexOf("MSIE") > -1 || ua.indexOf("Trident") > -1) return "IE";
        return "Unknown";
    }

    // Track page view
    function trackPageView() {
        const data = {
            type: "page_view",
            url: window.location.pathname + window.location.search,
            page_title: document.title,
            referrer: document.referrer || null,
            user_agent: navigator.userAgent,
            session_id: config.sessionId,
            user_id: config.userId,
            device_type: getDeviceType(),
            browser: getBrowser(),
            timestamp: new Date().toISOString(),
        };

        sendEvent(data);
        console.log("[Analytics] Page view tracked:", data.url);
    }

    // Track scroll depth
    function trackScrollDepth() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollTop =
            window.pageYOffset || document.documentElement.scrollTop;

        const scrollPercentage = Math.round(
            ((scrollTop + windowHeight) / documentHeight) * 100,
        );

        if (scrollPercentage > maxScrollDepth) {
            maxScrollDepth = Math.min(scrollPercentage, 100);

            // Track milestones: 25%, 50%, 75%, 100%
            const milestones = [25, 50, 75, 100];
            milestones.forEach((milestone) => {
                if (
                    maxScrollDepth >= milestone &&
                    !sessionStorage.getItem(
                        `scroll_${milestone}_${config.sessionId}`,
                    )
                ) {
                    sessionStorage.setItem(
                        `scroll_${milestone}_${config.sessionId}`,
                        "true",
                    );

                    const data = {
                        type: "scroll",
                        url: window.location.pathname,
                        session_id: config.sessionId,
                        scroll_depth: milestone,
                        timestamp: new Date().toISOString(),
                    };

                    sendEvent(data);
                    console.log(`[Analytics] Scroll milestone: ${milestone}%`);
                }
            });
        }
    }

    // Track time on page
    function getTimeOnPage() {
        return Math.round((Date.now() - startTime) / 1000); // seconds
    }

    // Send event to server
    function sendEvent(data) {
        eventQueue.push(data);

        // If queue is large enough, flush immediately
        if (eventQueue.length >= 5) {
            flushEvents();
        }
    }

    // Flush events to server
    function flushEvents() {
        if (eventQueue.length === 0) return;

        const events = [...eventQueue];
        eventQueue = [];

        // Get CSRF token
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]',
        )?.content;

        // Use sendBeacon for reliability (works even when page is closing)
        const payload = JSON.stringify({
            events: events,
            time_on_page: getTimeOnPage(),
            scroll_depth: maxScrollDepth,
        });

        // Use fetch with CSRF token
        fetch(config.endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken || "",
                Accept: "application/json",
            },
            body: payload,
            keepalive: true,
        }).catch((err) =>
            console.error("[Analytics] Error sending events:", err),
        );
    }

    // Track page exit
    function trackPageExit() {
        const data = {
            type: "exit",
            url: window.location.pathname,
            session_id: config.sessionId,
            time_on_page: getTimeOnPage(),
            scroll_depth: maxScrollDepth,
            timestamp: new Date().toISOString(),
        };

        sendEvent(data);
        flushEvents();
    }

    // Initialize tracking
    function init() {
        // Track initial page view
        trackPageView();

        // Track scroll depth
        let scrollTimeout;
        window.addEventListener(
            "scroll",
            function () {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(trackScrollDepth, 150);
            },
            { passive: true },
        );

        // Flush events periodically
        flushInterval = setInterval(flushEvents, 10000); // Every 10 seconds

        // Track page exit
        window.addEventListener("beforeunload", trackPageExit);
        window.addEventListener("pagehide", trackPageExit);

        // Track visibility changes (tab switching)
        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                flushEvents();
            }
        });

        console.log("[Analytics] Tracking initialized");
    }

    // Start tracking when DOM is ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    // Expose API for manual tracking
    window.INDIAnalytics = {
        track: function (eventType, data) {
            sendEvent({
                type: eventType,
                ...data,
                url: window.location.pathname,
                session_id: config.sessionId,
                timestamp: new Date().toISOString(),
            });
        },
        flush: flushEvents,
    };
})();
