<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'BPM Skyline Studio')</title>
  <meta name="theme-color" content="#111111">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Fonts: Inter (UI) + Playfair Display (Headings) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />

  <style>
    :root{
      --ui-font: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Noto Sans";
      --display-font: "Playfair Display", Georgia, "Times New Roman", serif;
      --leading-tight: 1.15;
      --leading-normal: 1.6;
      --brand-grad: linear-gradient(45deg, #f3ec78, #af4261);
      --nav-h: 72px; /* navbar height (lg) */
      --nav-h-sm: 64px; /* navbar height (sm) */
    }

    /* Base */
    html { scroll-behavior: smooth; }
    body{
      font-family: var(--ui-font);
      line-height: var(--leading-normal);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      padding-top: var(--nav-h);
      background: #f7f7f8;
      font-synthesis-weight: none;
      text-rendering: optimizeLegibility;
    }
    @media (max-width: 991.98px){ body{ padding-top: var(--nav-h-sm); } }

    /* Respect reduced motion */
    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .animate-section { animation: none !important; }
      .glass { transition: none !important; }
    }

    /* Typography */
    h1,.display-1,.display-2,.display-3,.display-4,.display-5{
      font-family: var(--display-font);
      line-height: var(--leading-tight);
      letter-spacing: .1px;
      text-wrap: balance;
    }
    h2,h3,h4{ line-height: 1.2; text-wrap: balance; }
    .lead{ font-weight: 500; letter-spacing:.2px; }
    .text-gradient{
      background: var(--brand-grad);
      -webkit-background-clip:text; background-clip:text;
      -webkit-text-fill-color: transparent;
    }

    /* Focus styles */
    a:focus-visible, button:focus-visible {
      outline: 3px solid #ffd86b;
      outline-offset: 2px;
      border-radius: .5rem;
    }

    /* Navbar */
    .navbar{
      box-shadow: 0 2px 15px rgba(0,0,0,.2);
      background-color: rgba(0,0,0,.85) !important;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }
    .navbar .navbar-brand{ letter-spacing:.3px; }
    #pageNav .nav-link{ color:#e7e7e7; opacity:.9; }
    #pageNav .nav-link:hover{ opacity:1; }
    #pageNav .nav-link.active{
      color:#fff; position:relative;
    }
    #pageNav .nav-link.active::after{
      content:""; position:absolute; left:0; right:0; bottom:-6px;
      height:2px; background:#fff; opacity:.7;
    }

    /* Parallax */
    .parallax{
      position:relative; min-height: 90vh;
      display:grid; place-items:center; overflow:hidden;
      isolation:isolate; color:#fff;
    }
    .parallax::before{
      content:""; position:absolute; inset:0;
      background-image: var(--bg);
      background-size: cover; background-position:center;
      background-repeat:no-repeat; background-attachment: fixed;
      z-index:-2;
    }
    .parallax::after{
      content:""; position:absolute; inset:0;
      background: linear-gradient(
        to bottom,
        rgba(0,0,0,var(--overlay-top,.55)) 0%,
        rgba(0,0,0,var(--overlay,.40)) 50%,
        rgba(0,0,0,var(--overlay-bottom,.55)) 100%
      );
      z-index:-1;
    }
    @supports (-webkit-overflow-scrolling: touch){
      .parallax::before{ background-attachment: scroll; }
    }
    @media (max-width: 991.98px){ .parallax{ min-height:60vh; } }

    /* Glass card */
    .glass{
      background: rgba(18,18,18,.28);
      border: 1px solid rgba(255,255,255,.18);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1rem;
      box-shadow: 0 12px 32px rgba(0,0,0,.35);
      transition: transform .3s ease, box-shadow .3s ease;
    }
    .glass:hover{ transform: translateY(-4px); box-shadow: 0 18px 38px rgba(0,0,0,.4); }

    /* Sections & helpers */
    .section-block{ padding: 3.5rem 0; }
    .scroll-offset{ scroll-margin-top: calc(var(--nav-h) + 20px); }
    @media (max-width: 991.98px){ .scroll-offset{ scroll-margin-top: calc(var(--nav-h-sm) + 20px); } }
    .cta-cluster .btn{ min-width: 220px; }
    .status-badge{
      font-size:.9rem; padding:.5rem .75rem; border-radius:2rem;
      letter-spacing:.4px;
    }

    /* Sticky mobile CTA */
    .mobile-cta{
      position: fixed; left:0; right:0; bottom:0;
      padding: .9rem; background: rgba(20,20,20,.85);
      backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
      border-top:1px solid rgba(255,255,255,.12); z-index: 1040;
      box-shadow: 0 -6px 20px rgba(0,0,0,.3);
    }
    @media (min-width: 768px){ .mobile-cta{ display:none; } }

    /* Enter animation */
    @keyframes fadeInUp{ from{opacity:0; transform:translateY(18px)} to{opacity:1; transform:none} }
    .animate-section{ animation: fadeInUp .6s ease forwards; }
  </style>

  @stack('head')
</head>
<body data-bs-spy="scroll" data-bs-target="#pageNav" data-bs-offset="110" tabindex="0">

  {{-- Fixed full-width navbar with injectable anchors --}}
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid px-3">
      <a class="navbar-brand fw-semibold me-lg-4 d-flex align-items-center" href="{{ route('home') }}">
        <i class="bi bi-music-note-beamed me-2" aria-hidden="true"></i>
        <span>BPM Skyline Studio</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
              aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav flex-lg-row align-items-lg-center gap-lg-3 mb-2 mb-lg-0" id="pageNav">
          @yield('navbar-links')
        </ul>
      </div>
    </div>
  </nav>

  {{-- Flash messages --}}
  @if (session('success') || session('status') || $errors->any())
    <!--div class="container-fluid px-4 mt-3">
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if (session('status'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          {{ session('status') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div-->
  @endif

  {{-- Content --}}
  <main style="margin-top: -15px;">
    <div class="container-fluid p-0">
      @yield('content')
    </div>
  </main>

  {{-- Footer --}}
  <footer class="border-top py-2 bg-dark text-white">
    <div class="container-fluid px-2">
      
      <div class="pt-3 text-center text-white-50 small">
        &copy; {{ date('Y') }} BPM Skyline Studio. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ScrollSpy init/refresh (offset matches navbar)
    const initSpy = () => new bootstrap.ScrollSpy(document.body, { target: '#pageNav', offset: 110 });
    document.addEventListener('DOMContentLoaded', initSpy);
    document.addEventListener('shown.bs.collapse', () => {
      const spy = bootstrap.ScrollSpy.getInstance(document.body);
      spy ? spy.refresh() : initSpy();
    });

    // Animate sections on enter
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('animate-section'); });
    }, { threshold: 0.1 });

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.glass, .section-block').forEach(el => observer.observe(el));
    });
  </script>

  @stack('scripts')
</body>
</html>