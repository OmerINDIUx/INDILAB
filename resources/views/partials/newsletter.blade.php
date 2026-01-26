<section class="contact-next-project">
    <!-- Newsletter -->
    <div class="newsletter-block">
        <h2 data-i18n="newsletter_title"></h2>
        <p data-i18n="newsletter_desc"></p>

        <form id="newsletter-form" novalidate>
            <label for="nm-email" data-i18n="newsletter_email_label"></label>
            <input id="nm-email" name="email" type="email" required data-i18n="newsletter_email_placeholder" />

            <label for="nm-name" data-i18n="newsletter_name_label"></label>
            <input id="nm-name" name="name" type="text" data-i18n="newsletter_name_placeholder" />

            <div class="policy">
                <input id="nm-policy" name="policy" type="checkbox" required />
                <label for="nm-policy">
                    <span data-i18n="newsletter_policy_accept"></span>
                    <a href="{{ url('aviso_privasidad.html') }}" target="_blank" rel="noopener" data-i18n="newsletter_policy_link"></a>
                </label>
            </div>

            <button type="submit" class="newsletter-btn" data-i18n="newsletter_btn"></button>

            <div id="nm-message"></div>

            <div class="social">
                <a href="https://www.instagram.com/indixlab/" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.facebook.com/people/INDIxLab/61582923939627/?locale=es_LA" target="_blank"
                    aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://x.com/INDIxLab" target="_blank" aria-label="Twitter">
                    <i class="fab fa-x-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/company/indixlab" target="_blank" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </form>
    </div>

    <aside class="next-project-block">
        <h3 data-i18n="next_project_title">Next Projects</h3>
        <article class="project-card">
            <img src="{{ asset('projects/ViviendaModular/images/electromovilidad.png') }}"
                alt="Miniatura proyecto siguiente" />
            <div class="project-card__body">
                <h4 data-i18n="Electromobility"></h4>
                <p data-i18n="project_card_link"></p>
            </div>
        </article>
        <article class="project-card">
            <img src="{{ asset('projects/ViviendaModular/images/Generated Image September 09, 2025 - 2_24PM.png') }}"
                alt="Miniatura proyecto siguiente" />
            <div class="project-card__body">
                <h4 data-i18n="Urban Heat Islands"></h4>
                <p data-i18n="project_card_link"></p>
            </div>
        </article>
    </aside>

    <script>
        (function() {
            const form = document.getElementById("newsletter-form");
            const email = document.getElementById("nm-email");
            const policy = document.getElementById("nm-policy");
            const msg = document.getElementById("nm-message");

            function show(message, ok = true) {
                msg.textContent = message;
                msg.style.color = ok ? "#0a0" : "#c00";
            }

            form.addEventListener("submit", function(e) {
                e.preventDefault();
                msg.textContent = "";

                if (!email.value || !/\S+@\S+\.\S+/.test(email.value)) {
                    show("Introduce un correo válido.", false);
                    email.focus();
                    return;
                }
                if (!policy.checked) {
                    show("Debes aceptar la política de privacidad.", false);
                    return;
                }

                const payload = {
                    email: email.value.trim(),
                    name: (document.getElementById("nm-name").value || "").trim(),
                    acceptedPolicy: true,
                    source: window.location.pathname,
                };

                // Temporary pointing to mail.php in public, eventually replace with route
                fetch("{{ asset('mail.php') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(payload),
                    })
                    .then((resp) => {
                        if (!resp.ok) throw new Error("network");
                        return resp.json().catch(() => ({}));
                    })
                    .then(() => {
                        show("Gracias por suscribirte. Revisa tu correo.", true);
                        form.reset();
                    })
                    .catch(() => {
                        try {
                            const pending = JSON.parse(
                                localStorage.getItem("newsletter_pending") || "[]"
                            );
                            pending.push(payload);
                            localStorage.setItem(
                                "newsletter_pending",
                                JSON.stringify(pending)
                            );
                        } catch (err) {}
                        show("Suscripción Enviada", true);
                        form.reset();
                    });
            });
        })();
    </script>
</section>
