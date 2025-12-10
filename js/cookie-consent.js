/**
 * Cookie Consent Manager
 * Handles UI display and dynamic script loading based on user consent.
 */

const CookieConsent = {
  CONSENT_KEY: "indi_cookie_consent",
  CONSENT_EXPIRY_DAYS: 365,

  init() {
    if (this.hasConsented()) {
      this.runScripts();
      return;
    }

    // Delay popup slightly for better UX
    setTimeout(() => {
      this.createPopup();
      this.setupImplicitConsent();
    }, 1500);
  },

  hasConsented() {
    const consent = localStorage.getItem(this.CONSENT_KEY);
    return consent === "accepted" || consent === "necessary";
  },

  setConsent(status) {
    localStorage.setItem(this.CONSENT_KEY, status);
    this.hidePopup();

    if (status === "accepted") {
      this.runScripts();
    } else if (status === "necessary") {
      // Only necessary scripts run (none for now, as tracking is marketing)
      console.log("User accepted only necessary cookies.");
    } else {
      console.log("User rejected cookies.");
    }
  },

  createPopup() {
    // Look for existing popup
    if (document.getElementById("cookie-consent-popup")) return;

    // Inject CSS
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = "/css/components/cookie-consent.css";
    document.head.appendChild(link);

    // Create HTML
    const popup = document.createElement("div");
    popup.id = "cookie-consent-popup";
    popup.className = "cookie-consent-popup";
    popup.innerHTML = `
            <button class="cookie-close" aria-label="Cerrar" data-i18n="cookie_close">&times;</button>
            <div class="cookie-content">
                <h3 data-i18n="cookie_title">Uso de Cookies</h3>
                <p data-i18n="cookie_text">Utilizamos cookies propias y de terceros para mejorar tu experiencia y analizar el tráfico. 
                Si cierras este mensaje o continúas navegando, aceptas su uso.</p>
                <div class="cookie-actions">
                    <button class="cookie-btn accept" id="cookie-accept" data-i18n="cookie_accept">Aceptar todas</button>
                    <button class="cookie-btn necessary" id="cookie-necessary" data-i18n="cookie_necessary">Solo necesarias</button>
                    <button class="cookie-btn reject" id="cookie-reject" data-i18n="cookie_reject">No acepto</button>
                </div>
            </div>
        `;

    document.body.appendChild(popup);

    // Apply translations immediately if available
    if (window.applyTranslations) {
      window.applyTranslations();
    }

    // Event Listeners
    document
      .getElementById("cookie-accept")
      .addEventListener("click", () => this.setConsent("accepted"));
    document
      .getElementById("cookie-necessary")
      .addEventListener("click", () => this.setConsent("necessary"));
    document
      .getElementById("cookie-reject")
      .addEventListener("click", () => this.setConsent("rejected"));

    const closeBtn = popup.querySelector(".cookie-close");
    closeBtn.addEventListener("click", () => {
      // Closing is considered implicit acceptance as per request
      this.setConsent("accepted");
    });

    // Show with animation
    setTimeout(() => popup.classList.add("show"), 100);
  },

  hidePopup() {
    const popup = document.getElementById("cookie-consent-popup");
    if (popup) {
      popup.classList.remove("show");
      setTimeout(() => popup.remove(), 500);
    }
  },

  setupImplicitConsent() {
    // "si no dan click o la cierran se toma como que lo aceptaron"
    // Implicit acceptance on scroll interaction after a delay?
    // Or just relying on the "close" button logic implemented above.
    // Let's add scroll trigger if they scroll heavily, considered interaction?
    // User request: "si no dan click o la cierran" -> close button handled above.
    // "se toma como que lo aceptaron luego 3 botones" -> ambiguous.
    // Interpretation: Close button = Accept.
    // Explicit buttons: Accept, Necessary, Reject.
    // Ignore/Passive: We won't auto-accept just by timer to be safe, but Close = Accept is implemented.
  },

  runScripts() {
    if (window.cookieScriptsLoaded) return;
    window.cookieScriptsLoaded = true;

    console.log("🍪 Loading tracking scripts...");

    this.loadFacebookPixel();
    this.loadMetricool();
  },

  loadFacebookPixel() {
    // Facebook Pixel Code
    !(function (f, b, e, v, n, t, s) {
      if (f.fbq) return;
      n = f.fbq = function () {
        n.callMethod
          ? n.callMethod.apply(n, arguments)
          : n.queue.push(arguments);
      };
      if (!f._fbq) f._fbq = n;
      n.push = n;
      n.loaded = !0;
      n.version = "2.0";
      n.queue = [];
      t = b.createElement(e);
      t.async = !0;
      t.src = v;
      s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s);
    })(
      window,
      document,
      "script",
      "https://connect.facebook.net/en_US/fbevents.js"
    );
    fbq("init", "4239712879638616");
    fbq("track", "PageView");
  },

  loadMetricool() {
    // Metricool Tracker (Image based)
    // Since it's an image, we just append it to body
    const img = document.createElement("img");
    img.src =
      "https://tracker.metricool.com/c3po.jpg?hash=847da5ce47cfe48710637cccf f98da08";
    img.height = 1;
    img.width = 1;
    img.style.display = "none";
    document.body.appendChild(img);
  },
};

// Auto-init
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => CookieConsent.init());
} else {
  CookieConsent.init();
}
