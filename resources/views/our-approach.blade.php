@extends('layouts.app')

@section('title', 'Our Approach | INDI Lab')

@push('css')
    <link id="menu-css" rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}" />
    <link id="menu-css" rel="stylesheet" href="{{ asset('css/Our_Aproach.css') }}" />
@endpush

@section('content')
    <!-- Contenido principal -->
    <section class="hero">
      <video autoplay muted loop playsinline aria-hidden="true">
        <source src="{{ asset('video/videoouraproach.mp4') }}" type="video/mp4" />
        Tu navegador no soporta el elemento de video.
      </video>
      <div class="hero-text">
        <h1 data-i18n="our approach"></h1>
        <h2 data-i18n="our approach sub"></h2>
      </div>
    </section>

    <section class="intro">
      <p>
        <span data-i18n="our_approach_intro"></span>
        <span class="pdp" data-i18n="our_approach_spam"></span>
      </p>
      <p>
        <span data-i18n="our_approach_text"></span>
      </p>

      <div class="secundary-logo">
        <div class="indi-lab-logo" data-svg="{{ asset('svg/Secundary_Brand.svg') }}"></div>
      </div>

      <script>
        // SVGs loaded by global.js
      </script>
    </section>

    <section class="list">
      <div class="phrase">
        <span class="pdp" data-i18n="Señales_Urbanas_title"></span><br />
        <strong><span data-i18n="Señales_Urbanas_text"></strong></span><br />
        <span data-i18n="Señales_Urbanas_text1"></span> <br />
        <br />
        <span data-i18n="Señales_Urbanas_text2"></span>
      </div>
      <div class="phrase">
        <span class="pdp" data-i18n="Sistemas_Urbanos_title"></span><br />
        <strong><span data-i18n="Sistemas_Urbanos_text"></strong></span><br />
        <span data-i18n="Sistemas_Urbanos_text1"></span> <br />
        <br />
        <span data-i18n="Sistemas_Urbanos_text2"></span>
      </div>
      

      <div class="phrase">
        <span class="pdp" data-i18n="Experimentos_urbano_title"></span><br />
        <strong><span data-i18n="Experimentos_urbano_text"></strong></span><br />
        <span data-i18n="Experimentos_urbano_text1"></span> <br />
        <br />
        <span data-i18n="Experimentos_urbano_text2"></span>
      </div>

      <div class="phrase">
        <span class="pdp" data-i18n="Urban_Playbooks_title"></span><br />
        <strong><span data-i18n="Urban_Playbooks_text"></strong></span><br />
        <span data-i18n="Urban_Playbooks_text1"></span> <br />
      </div>

    </section>
    
    <script>
      gsap.registerPlugin(ScrollTrigger);

      //  Unified Animation (Pinned Carousel for ALL devices)
      const phrases = gsap.utils.toArray(".list .phrase");
      
      // Initial setup: all invisible except first one potentially
      gsap.set(phrases, { opacity: 0, scale: 0.95, filter: "blur(10px)", y: 20 });
      
      // Master timeline linked to scroll
      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: ".list",
          start: "top top",
          end: "+=" + phrases.length * 100 + "%", // Extend scroll distance
          scrub: 1, // Smooth interaction
          pin: true,
          anticipatePin: 1,
        },
      });

      phrases.forEach((phrase, i) => {
        // Determine timing: Fade In -> Stay -> Fade Out
        const durationIn = 1;
        const durationHold = 2; 
        const durationOut = 1;
        
        // Entrance
        tl.to(phrase, { 
          opacity: 1, 
          scale: 1, 
          filter: "blur(0px)", 
          y: 0, 
          duration: durationIn, 
          ease: "power2.out" 
        });

        // Hold
        tl.to(phrase, { 
          opacity: 1, 
          duration: durationHold 
        });

        // Exit (unless last one)
        if(i < phrases.length) {
            tl.to(phrase, { 
            opacity: 0, 
            scale: 1.05, 
            filter: "blur(10px)", 
            y: -20, 
            duration: durationOut, 
            ease: "power2.in" 
            });
        }
      });

      // Refresh on resize to ensure correct calculations
      window.addEventListener("resize", () => ScrollTrigger.refresh());
    </script>

    <section class="statement">
      <div id="content-row">
        <div class="content-text">
          <h2 data-i18n="we aim to"></h2>
          <div
            class="corner corner-bottom-left"
            data-svg="{{ asset('svg/cornner_bottom_left.svg') }}"
          ></div>
          <div
            class="corner corner-top-right"
            data-svg="{{ asset('svg/cornner_top_right.svg') }}"
          ></div>
        </div>
      </div>
    </section>

    <script>
      window.addEventListener("DOMContentLoaded", () => {
        gsap.registerPlugin(ScrollTrigger);

        const bottomLeft = document.querySelector(".corner-bottom-left");
        const topRight = document.querySelector(".corner-top-right");
        const content = document.querySelector(".content-text");
        let tl;

        function getPositions() {
          const w = content.offsetWidth;
          const h = content.offsetHeight;

          // ⏳ breakpoint: móvil 500px
          const isMobile = window.innerWidth <= 500;

          if (isMobile) {
            return {
              bottomLeft: { x: w / 10 - 15, y: h / 10 - 25 },
              topRight: { x: w - 40, y: -h - 3 },
              center: { x: w / 2 - 20, y: h / 2 - 20 },
            };
          }

          // ⏹ posiciones normales (tu código original)
          return {
            bottomLeft: { x: -50, y: -25 },
            topRight: { x: +w, y: -h - 65 },
            center: { x: w / 2 - 25, y: h / 2 - 25 },
          };
        }

        function animateCorners() {
          const pos = getPositions();

          gsap.set([bottomLeft, topRight], {
            position: "absolute",
            x: pos.center.x,
            y: pos.center.y,
            opacity: 0,
          });

          const tl = gsap.timeline({
            scrollTrigger: {
              trigger: ".statement",
              start: "top center",
              end: "bottom bottom",
              scrub: true,
            },
          });

          tl.to(bottomLeft, {
            x: pos.bottomLeft.x,
            y: pos.bottomLeft.y,
            opacity: 1,
            ease: "power3.out",
          }).to(
            topRight,
            {
              x: pos.topRight.x,
              y: pos.topRight.y,
              opacity: 1,
              ease: "power3.out",
            },
            "<"
          );

          return tl;
        }

        // Fade-in del texto
        gsap.fromTo(
          ".content-text",
          { opacity: 0.01 },
          {
            opacity: 1,
            scrollTrigger: {
              trigger: ".statement",
              start: "top center",
              end: "bottom bottom",
              scrub: true,
            },
          }
        );

        // iniciar después del delay
        setTimeout(() => {
          tl = animateCorners();
        }, 2000);

        // recalcular al hacer resize
        window.addEventListener("resize", () => {
          if (tl) {
            tl.kill();
            tl = animateCorners();
          }
        });
      });
    </script>
   

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        // 🎨 Lista de colores posibles
        const colors = ["#18B2E8", "#F86230", "#A51C5B", "#9244D6"];
        let lastColor = null; // guardamos el último color usado

        // Seleccionamos todos los <span> y los <h2 class="white">
        const elements = document.querySelectorAll(".pdp, h2.white");

        elements.forEach((el) => {
          let randomColor;
          // Evita repetir el mismo color que el anterior
          do {
            randomColor = colors[Math.floor(Math.random() * colors.length)];
          } while (randomColor === lastColor);

          el.style.color = randomColor;
          lastColor = randomColor;
        });
      });
    </script>

    @include('partials.newsletter')

    <a href="{{ route('home') }}">
      <div class="secundary-brand" data-svg="{{ asset('svg/indi-lab_Vertical_Animate.svg') }}"></div>
    </a>
    <script>
        const container = document.querySelector(".secundary-brand");
        fetch("{{ asset('svg/indi-lab_Vertical_Animate.svg') }}")
        .then((response) => response.text())
        .then((svgText) => {
            container.innerHTML = svgText;
        })
        .catch((err) => console.error("Error cargando SVG:", err));
    </script>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/demo4/traduction.js') }}"></script>
    <!-- GSAP for this page specifically -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.14.0/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.14.0/ScrollTrigger.min.js"></script>
@endpush
