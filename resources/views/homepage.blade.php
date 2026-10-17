@extends('frontend.master')

@section('title', 'Buildwise | Construction Project Management')

@section('styles')
    .hero { position:relative; overflow:hidden; background:var(--paper); }
    .hero-inner { width:min(1160px, calc(100% - 48px)); min-height:590px; margin:auto; padding:76px 0 84px; display:grid; grid-template-columns:1fr .92fr; align-items:center; gap:58px; }
    .eyebrow { display:inline-flex; align-items:center; gap:9px; margin:0 0 20px; color:var(--green); font-size:.78rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .eyebrow::before { width:22px; height:2px; background:var(--orange); content:""; }
    .hero h1 { max-width:650px; margin:0; color:var(--green); font-size:clamp(3.1rem,6.2vw,5.6rem); line-height:.99; letter-spacing:-.065em; }
    .hero h1 em { color:#799147; font-style:normal; }
    .hero-copy { max-width:520px; margin:24px 0 29px; color:var(--muted); font-size:1.08rem; }
    .hero-actions { display:flex; align-items:center; flex-wrap:wrap; gap:16px; }
    .button-primary { color:#fff; background:var(--green); }
    .button-primary:hover { background:#245343; }
    .text-link { color:var(--green); text-decoration:none; font-weight:750; }
    .text-link span { margin-left:5px; }
    .hero-proof { display:flex; align-items:center; gap:13px; margin-top:42px; color:var(--muted); font-size:.86rem; }
    .proof-mark { display:flex; }
    .proof-mark span { display:grid; width:30px; height:30px; margin-right:-7px; place-items:center; border:2px solid var(--paper); border-radius:50%; color:var(--green); background:#d9e5c7; font-size:.66rem; font-weight:800; }
    .proof-mark span:nth-child(2) { background:#f3d5a3; }
    .proof-mark span:nth-child(3) { background:#c6d9d0; }
    .hero-visual { position:relative; min-height:430px; display:grid; place-items:center; }
    .visual-glow { position:absolute; width:380px; height:380px; border-radius:50%; background:#e5edda; }
    .building { position:relative; width:min(100%,440px); height:375px; overflow:hidden; border:8px solid #fff; border-radius:14px; background:linear-gradient(145deg,#cbd7c7 0 23%,#889b8e 23% 24%,#294c3e 24% 100%); box-shadow:0 26px 70px #1c3e3026; transform:rotate(2deg); }
    .building::before { position:absolute; right:0; bottom:0; left:0; height:70%; background:linear-gradient(90deg,transparent 0 10%,#f0b653 10% 11%,transparent 11% 33%,#f0b653 33% 34%,transparent 34% 58%,#f0b653 58% 59%,transparent 59% 83%,#f0b653 83% 84%,transparent 84%),linear-gradient(#f0b653 0 2px,transparent 2px 31%,#f0b653 31% 33%,transparent 33% 64%,#f0b653 64% 66%,transparent 66%); content:""; opacity:.82; }
    .building::after { position:absolute; top:14%; left:14%; width:72%; height:55%; border-top:4px solid #f0b653; border-left:4px solid #f0b653; transform:skewY(-24deg); content:""; opacity:.78; }
    .building-sun { position:absolute; top:35px; right:42px; width:58px; height:58px; border-radius:50%; background:#f1bd64; }
    .float-card { position:absolute; z-index:1; padding:15px 18px; border:1px solid #e8ece5; border-radius:11px; background:#fff; box-shadow:0 12px 35px #1c3e3018; }
    .float-card strong { display:block; color:var(--green); font-size:1.18rem; line-height:1.2; }
    .float-card small { color:var(--muted); font-size:.76rem; }
    .card-progress { right:-6px; bottom:29px; }
    .card-progress strong { display:flex; align-items:center; gap:7px; }
    .card-progress strong::before { width:8px; height:8px; border-radius:50%; background:#8eb454; content:""; }
    .card-budget { top:33px; left:-7px; }
    .section { padding:88px 24px; background:#fff; }
    .section-inner { width:min(1100px,100%); margin:auto; }
    .section-heading { max-width:650px; margin:0 auto 42px; text-align:center; }
    .section-heading .eyebrow { justify-content:center; }
    .section-heading h2 { margin:0 0 13px; color:var(--green); font-size:clamp(2rem,4vw,3rem); line-height:1.1; letter-spacing:-.045em; }
    .section-heading p { margin:0; color:var(--muted); }
    .feature-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
    .feature { padding:27px; border:1px solid var(--line); border-radius:12px; background:#fff; }
    .feature-icon { display:grid; width:44px; height:44px; margin-bottom:21px; place-items:center; border-radius:12px; color:var(--green); background:#eaf1dc; font-size:1.25rem; }
    .feature h3 { margin:0 0 9px; color:var(--green); font-size:1.12rem; }
    .feature p { margin:0; color:var(--muted); font-size:.92rem; }
    .workflow { padding:78px 24px; color:#fff; background:var(--green); }
    .workflow .section-heading h2 { color:#fff; }
    .workflow .section-heading p { color:#bdcdc3; }
    .workflow .eyebrow { color:var(--lime); }
    .steps { display:grid; grid-template-columns:repeat(3,1fr); gap:28px; }
    .step { padding-top:21px; border-top:1px solid #547064; }
    .step-number { color:var(--lime); font-size:.8rem; font-weight:800; letter-spacing:.1em; }
    .step h3 { margin:10px 0 8px; font-size:1.16rem; }
    .step p { margin:0; color:#bdcdc3; font-size:.92rem; }
    @media (max-width:850px) { .hero-inner { grid-template-columns:1fr; gap:15px; padding-top:65px; } .hero-visual { min-height:390px; } .building { max-width:440px; } .feature-grid { grid-template-columns:1fr 1fr; } }
    @media (max-width:560px) { .hero-inner { width:calc(100% - 36px); padding:52px 0 56px; } .hero h1 { font-size:clamp(2.8rem,14vw,4.2rem); } .hero-copy { font-size:1rem; } .hero-visual { min-height:315px; margin:0 -6px; } .visual-glow { width:280px; height:280px; } .building { height:270px; border-width:6px; } .card-budget { top:20px; left:0; } .card-progress { right:0; bottom:12px; } .section { padding:65px 18px; } .feature-grid,.steps { grid-template-columns:1fr; } .feature { padding:22px; } .workflow { padding:65px 18px; } }
@endsection

@section('content')
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-content">
                <p class="eyebrow">Construction, coordinated</p>
                <h1>Build better.<br><em>Stay in control.</em></h1>
                <p class="hero-copy">Bring project plans, people, and materials together. Keep every site moving with a clear view of what’s happening next.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="{{ route('login') }}">Open your workspace <span aria-hidden="true">↗</span></a>
                    <a class="text-link" href="#solutions">Explore the platform <span aria-hidden="true">↓</span></a>
                </div>
                <div class="hero-proof">
                    <div class="proof-mark" aria-hidden="true"><span>PM</span><span>SV</span><span>ST</span></div>
                    <span>One connected view for your whole project team</span>
                </div>
            </div>
            <div class="hero-visual" role="img" aria-label="Illustration of a building under construction">
                <div class="visual-glow"></div>
                <div class="building"><span class="building-sun"></span></div>
                <div class="float-card card-budget"><small>Project overview</small><strong>Built around your site</strong></div>
                <div class="float-card card-progress"><small>Team coordination</small><strong>Moving forward</strong></div>
            </div>
        </div>
    </section>

    <section class="section" id="solutions">
        <div class="section-inner">
            <div class="section-heading">
                <p class="eyebrow">Everything in one place</p>
                <h2>From the first plan to the final handover</h2>
                <p>Keep the details connected so your team can spend less time chasing updates and more time getting work done.</p>
            </div>
            <div class="feature-grid">
                <article class="feature"><div class="feature-icon" aria-hidden="true">⌂</div><h3>Project visibility</h3><p>Keep project details, milestones, documents, and status updates easy to find.</p></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">◷</div><h3>Team coordination</h3><p>Connect staff assignments and task ownership with the work happening on site.</p></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">▦</div><h3>Materials in view</h3><p>Organize supplier orders and site inventory alongside each project.</p></article>
            </div>
        </div>
    </section>

    <section class="workflow" id="workflow">
        <div class="section-inner">
            <div class="section-heading">
                <p class="eyebrow">A clearer workflow</p>
                <h2>Keep the next step in sight</h2>
                <p>Give every role a shared foundation for planning and delivering great work.</p>
            </div>
            <div class="steps">
                <article class="step"><span class="step-number">01 / PLAN</span><h3>Set up the project</h3><p>Bring the client, scope, schedule, and budget into one project record.</p></article>
                <article class="step"><span class="step-number">02 / ORGANIZE</span><h3>Align people and supplies</h3><p>Coordinate team assignments, task ownership, suppliers, and materials.</p></article>
                <article class="step"><span class="step-number">03 / DELIVER</span><h3>Track progress together</h3><p>Follow milestones and updates as work moves from plan to handover.</p></article>
            </div>
        </div>
    </section>
@endsection
