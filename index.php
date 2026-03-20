<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    header('Location: ' . dashboardUrl());
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sistema Académico — Plataforma de gestão para instituições de ensino superior. Gerencie cursos, fichas, matrículas e pautas.">
<title>Sistema Académico — Gestão Moderna e Segura</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ─── RESET & BASE ─── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --ink:    #0d1117;
  --ink2:   #1c2333;
  --blue:   #1d6fce;
  --blue2:  #1558a8;
  --sky:    #e8f0fb;
  --white:  #ffffff;
  --gray:   #6b7280;
  --light:  #f5f7fa;
  --brd:    #e1e7ef;
  --gold:   #c9922a;
  --r:      12px;
  --font:   'Plus Jakarta Sans', sans-serif;
  --serif:  'Playfair Display', serif;
  --mono:   'JetBrains Mono', monospace;
}
html { scroll-behavior: smooth; }
body {
  font-family: var(--font);
  background: var(--white);
  color: var(--ink);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}
a { text-decoration: none; color: inherit; }
img { display: block; max-width: 100%; }

/* ─── NAVBAR ─── */
.navbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(14px);
  border-bottom: 1px solid var(--brd);
  padding: 0 2.5rem;
  height: 64px;
  display: flex; align-items: center; justify-content: space-between;
}
.nav-brand {
  display: flex; align-items: center; gap: .75rem;
  font-weight: 800; font-size: 1rem; color: var(--ink);
}
.nav-brand-icon {
  width: 36px; height: 36px;
  background: var(--ink);
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.05rem;
}
.nav-links {
  display: flex; align-items: center; gap: .25rem;
}
.nav-links a {
  padding: .45rem .9rem;
  border-radius: 8px;
  font-size: .875rem;
  font-weight: 500;
  color: var(--gray);
  transition: all .15s;
}
.nav-links a:hover { background: var(--light); color: var(--ink); }
.nav-cta {
  background: var(--ink) !important;
  color: var(--white) !important;
  font-weight: 700 !important;
  padding: .5rem 1.2rem !important;
}
.nav-cta:hover { background: var(--blue) !important; }

/* ─── HERO ─── */
.hero {
  min-height: calc(100vh - 64px);
  display: flex; align-items: center; justify-content: center;
  text-align: center;
  padding: 5rem 2rem;
  position: relative;
  overflow: hidden;
  background: var(--light);
}
.hero-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 50% -10%, rgba(29,111,206,.12) 0%, transparent 70%),
    radial-gradient(ellipse 50% 40% at 0% 100%, rgba(29,111,206,.06) 0%, transparent 60%),
    radial-gradient(ellipse 40% 30% at 100% 80%, rgba(201,146,42,.06) 0%, transparent 60%);
}
.hero-grid-lines {
  position: absolute; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(29,111,206,.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(29,111,206,.04) 1px, transparent 1px);
  background-size: 60px 60px;
}
.hero-content { position: relative; z-index: 1; max-width: 800px; }
.hero-tag {
  display: inline-flex; align-items: center; gap: .5rem;
  background: var(--white);
  border: 1px solid var(--brd);
  padding: .4rem 1rem;
  border-radius: 50px;
  font-size: .78rem; font-weight: 700;
  color: var(--blue);
  margin-bottom: 2rem;
  box-shadow: 0 1px 4px rgba(0,0,0,.06);
  animation: fadeDown .6s ease both;
}
.hero-tag::before { content: '●'; font-size: .5rem; }
.hero h1 {
  font-family: var(--serif);
  font-size: clamp(2.8rem, 6vw, 5rem);
  line-height: 1.08;
  color: var(--ink);
  margin-bottom: 1.5rem;
  animation: fadeUp .7s .1s ease both;
}
.hero h1 em {
  font-style: italic;
  color: var(--blue);
}
.hero h1 span {
  display: inline-block;
  border-bottom: 3px solid var(--gold);
  padding-bottom: 2px;
}
.hero-desc {
  font-size: 1.1rem;
  color: var(--gray);
  max-width: 520px;
  margin: 0 auto 2.5rem;
  animation: fadeUp .7s .2s ease both;
}
.hero-actions {
  display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;
  animation: fadeUp .7s .3s ease both;
}
.btn-hero {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .9rem 2rem;
  border-radius: var(--r);
  font-weight: 700; font-size: .975rem;
  transition: all .2s;
}
.btn-hero-primary {
  background: var(--ink); color: var(--white);
  box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.btn-hero-primary:hover {
  background: var(--blue);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(29,111,206,.3);
}
.btn-hero-secondary {
  background: var(--white); color: var(--ink);
  border: 2px solid var(--brd);
}
.btn-hero-secondary:hover {
  border-color: var(--ink);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.hero-scroll {
  position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);
  display: flex; flex-direction: column; align-items: center; gap: .35rem;
  color: var(--gray); font-size: .72rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  animation: bounce 2s 1.5s infinite;
}
.hero-scroll::after {
  content: '↓';
  font-size: 1.1rem;
}

/* ─── STATS BAR ─── */
.stats-bar {
  background: var(--ink);
  padding: 1.75rem 3rem;
  display: flex; align-items: center; justify-content: center;
  gap: 4rem; flex-wrap: wrap;
}
.stat-item { text-align: center; }
.stat-num {
  font-family: var(--serif);
  font-size: 2rem; font-weight: 900;
  color: var(--white);
  line-height: 1;
}
.stat-lbl {
  font-size: .75rem; font-weight: 600;
  color: rgba(255,255,255,.45);
  text-transform: uppercase; letter-spacing: .1em;
  margin-top: .3rem;
}
.stat-divider { width: 1px; height: 40px; background: rgba(255,255,255,.1); }

/* ─── SECTIONS ─── */
.section { padding: 6rem 3rem; }
.section-alt { background: var(--light); }
.section-center { text-align: center; }
.section-tag {
  display: inline-block;
  background: var(--sky);
  color: var(--blue);
  padding: .3rem .875rem;
  border-radius: 50px;
  font-size: .72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .12em;
  margin-bottom: 1rem;
}
.section-title {
  font-family: var(--serif);
  font-size: clamp(1.8rem, 3.5vw, 2.75rem);
  line-height: 1.2;
  color: var(--ink);
  margin-bottom: .75rem;
  max-width: 640px;
}
.section-center .section-title { margin: 0 auto .75rem; }
.section-sub {
  font-size: 1rem; color: var(--gray);
  max-width: 520px; margin: 0 auto 3.5rem;
}

/* ─── FEATURES GRID ─── */
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.25rem;
  max-width: 1100px; margin: 0 auto;
}
.feature-card {
  background: var(--white);
  border: 1px solid var(--brd);
  border-radius: 16px;
  padding: 2rem;
  transition: all .2s;
  position: relative;
  overflow: hidden;
}
.feature-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--blue), transparent);
  opacity: 0;
  transition: opacity .2s;
}
.feature-card:hover { border-color: var(--blue); box-shadow: 0 8px 32px rgba(29,111,206,.1); transform: translateY(-2px); }
.feature-card:hover::before { opacity: 1; }
.feature-icon {
  width: 48px; height: 48px;
  background: var(--ink);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem;
  margin-bottom: 1.25rem;
}
.feature-card h3 { font-size: 1rem; font-weight: 800; margin-bottom: .5rem; }
.feature-card p  { font-size: .875rem; color: var(--gray); line-height: 1.65; }

/* ─── HOW IT WORKS ─── */
.steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 2rem;
  max-width: 960px; margin: 0 auto;
  position: relative;
}
.step {
  text-align: center;
  position: relative;
}
.step-num {
  width: 56px; height: 56px;
  background: var(--blue);
  color: var(--white);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--serif);
  font-size: 1.4rem; font-weight: 900;
  margin: 0 auto 1.1rem;
  box-shadow: 0 4px 16px rgba(29,111,206,.3);
}
.step h3 { font-size: .95rem; font-weight: 800; margin-bottom: .4rem; }
.step p  { font-size: .85rem; color: var(--gray); }

/* ─── PERFIS ─── */
.perfis-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 1.25rem;
  max-width: 1000px; margin: 0 auto;
}
.perfil-card {
  background: var(--white);
  border: 1px solid var(--brd);
  border-radius: 16px;
  padding: 2rem;
  transition: box-shadow .2s, transform .2s;
}
.perfil-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
.perfil-emoji { font-size: 2.5rem; display: block; margin-bottom: 1rem; }
.perfil-card h3 { font-weight: 800; font-size: 1.05rem; margin-bottom: .25rem; }
.perfil-card .perfil-role {
  font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
  color: var(--blue); margin-bottom: .875rem;
}
.perfil-card ul { list-style: none; }
.perfil-card ul li {
  font-size: .83rem; color: var(--gray);
  padding: .25rem 0;
  display: flex; gap: .5rem; align-items: flex-start;
  border-bottom: 1px solid var(--brd);
}
.perfil-card ul li:last-child { border: none; }
.perfil-card ul li::before { content: '→'; color: var(--blue); font-weight: 700; flex-shrink: 0; margin-top: 1px; }

/* ─── CTA SECTION ─── */
.cta-section {
  background: var(--ink);
  padding: 6rem 3rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.cta-section::before {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 60% 80% at 20% 50%, rgba(29,111,206,.15) 0%, transparent 60%),
    radial-gradient(ellipse 40% 60% at 80% 50%, rgba(201,146,42,.08) 0%, transparent 60%);
}
.cta-section h2 {
  position: relative;
  font-family: var(--serif);
  font-size: clamp(2rem, 4vw, 3.5rem);
  color: var(--white);
  margin-bottom: 1rem;
}
.cta-section p {
  position: relative;
  font-size: 1.05rem;
  color: rgba(255,255,255,.55);
  margin-bottom: 2rem;
}
.demo-accounts {
  position: relative;
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  max-width: 620px;
  margin: 0 auto 2.5rem;
  text-align: left;
}
.demo-title {
  font-size: .72rem; font-weight: 800; text-transform: uppercase;
  letter-spacing: .12em; color: #60a5fa;
  margin-bottom: 1.1rem;
  display: flex; align-items: center; gap: .5rem;
}
.demo-pass {
  font-family: var(--mono);
  background: rgba(255,255,255,.1);
  padding: .1rem .5rem;
  border-radius: 4px;
  color: #fbbf24;
  font-size: .85rem;
}
.demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .625rem; }
.demo-item {
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 8px;
  padding: .75rem 1rem;
}
.demo-item-role {
  font-size: .68rem; font-weight: 800; text-transform: uppercase;
  letter-spacing: .08em; color: rgba(255,255,255,.4);
  margin-bottom: .2rem;
}
.demo-item-email {
  font-family: var(--mono);
  font-size: .82rem; color: rgba(255,255,255,.85);
}
.cta-buttons {
  position: relative;
  display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;
}
.cta-btn {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .875rem 2rem;
  border-radius: var(--r);
  font-weight: 700; font-size: .95rem;
  transition: all .2s;
}
.cta-btn-primary { background: var(--white); color: var(--ink); }
.cta-btn-primary:hover { background: var(--sky); transform: translateY(-1px); }
.cta-btn-secondary {
  background: transparent; color: var(--white);
  border: 2px solid rgba(255,255,255,.25);
}
.cta-btn-secondary:hover { border-color: var(--white); transform: translateY(-1px); }

/* ─── FOOTER ─── */
footer {
  background: var(--ink2);
  border-top: 1px solid rgba(255,255,255,.05);
  padding: 3rem 3rem 2rem;
}
.footer-top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 3rem;
  margin-bottom: 2.5rem;
  padding-bottom: 2.5rem;
  border-bottom: 1px solid rgba(255,255,255,.07);
}
.footer-brand-icon {
  width: 40px; height: 40px;
  background: var(--blue);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
  margin-bottom: 1rem;
}
.footer-brand-name {
  font-weight: 800; font-size: 1rem; color: var(--white);
  margin-bottom: .5rem;
}
.footer-brand-desc {
  font-size: .83rem;
  color: rgba(255,255,255,.4);
  line-height: 1.6;
}
.footer-col h4 {
  font-size: .72rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .12em;
  color: rgba(255,255,255,.35);
  margin-bottom: 1rem;
}
.footer-col ul { list-style: none; }
.footer-col ul li { margin-bottom: .5rem; }
.footer-col ul li a {
  font-size: .85rem;
  color: rgba(255,255,255,.55);
  transition: color .15s;
}
.footer-col ul li a:hover { color: var(--white); }
.footer-bottom {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 1rem;
}
.footer-copy {
  font-size: .8rem; color: rgba(255,255,255,.25);
}
.footer-badges { display: flex; gap: .5rem; }
.footer-badge {
  font-family: var(--mono);
  font-size: .68rem; font-weight: 500;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.08);
  color: rgba(255,255,255,.35);
  padding: .25rem .625rem;
  border-radius: 4px;
}

/* ─── ANIMATIONS ─── */
@keyframes fadeDown { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeUp   { from { opacity:0; transform:translateY(16px);  } to { opacity:1; transform:translateY(0); } }
@keyframes bounce   { 0%,100% { transform:translateX(-50%) translateY(0); } 50% { transform:translateX(-50%) translateY(6px); } }

/* Intersection Observer animations */
.reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-delay-1 { transition-delay: .1s; }
.reveal-delay-2 { transition-delay: .2s; }
.reveal-delay-3 { transition-delay: .3s; }
.reveal-delay-4 { transition-delay: .4s; }

/* ─── RESPONSIVE ─── */
@media (max-width: 768px) {
  .navbar { padding: 0 1.25rem; }
  .nav-links a:not(.nav-cta) { display: none; }
  .section { padding: 4rem 1.5rem; }
  .stats-bar { gap: 1.5rem; padding: 1.5rem; }
  .stat-divider { display: none; }
  .footer-top { grid-template-columns: 1fr 1fr; gap: 2rem; }
  .demo-grid { grid-template-columns: 1fr; }
  .hero { padding: 3rem 1.25rem; }
}
</style>
</head>
<body>

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav class="navbar">
  <a href="index.php" class="nav-brand">
    <div class="nav-brand-icon">📚</div>
    Sistema Académico
  </a>
  <div class="nav-links">
    <a href="#funcionalidades">Funcionalidades</a>
    <a href="#como-funciona">Como Funciona</a>
    <a href="#perfis">Perfis</a>
    <a href="sobre.php">Sobre</a>
    <a href="ajuda.php">Ajuda</a>
    <a href="academicologin.php">Entrar</a>
    <a href="academicoregisto.php" class="nav-cta">Começar →</a>
  </div>
</nav>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid-lines"></div>
  <div class="hero-content">
    <div class="hero-tag">Ensino Superior · Gestão Integrada</div>
    <h1>
      Gestão Académica<br>
      <em>Moderna</em> e <span>Segura</span>
    </h1>
    <p class="hero-desc">
      Sistema completo para instituições de ensino superior. Cursos, fichas de alunos, matrículas e pautas — tudo num só lugar, com controlo total por perfil.
    </p>
    <div class="hero-actions">
      <a href="academicologin.php"    class="btn-hero btn-hero-primary">⚡ Aceder ao Sistema</a>
      <a href="academicoregisto.php" class="btn-hero btn-hero-secondary">Criar Conta</a>
    </div>
  </div>
  <div class="hero-scroll">Explorar</div>
</section>


<!-- ═══════════════ FUNCIONALIDADES ═══════════════ -->
<section class="section section-center" id="funcionalidades">
  <div class="section-tag">Funcionalidades</div>
  <h2 class="section-title reveal">Tudo o que uma instituição necessita</h2>
  <p class="section-sub reveal reveal-delay-1">Módulos completos para gerir toda a vida académica, com fluxos de aprovação, auditoria e controlo de acesso.</p>
  <div class="features-grid">
    <div class="feature-card reveal reveal-delay-1">
      <div class="feature-icon">🔐</div>
      <h3>Autenticação & Perfis</h3>
      <p>Login seguro com hash bcrypt, sessões com expiração automática e controlo de acesso por perfil — Aluno, Funcionário, Gestor e Admin.</p>
    </div>
    <div class="feature-card reveal reveal-delay-2">
      <div class="feature-icon">🎓</div>
      <h3>Gestão de Cursos</h3>
      <p>CRUD completo de cursos e unidades curriculares. Configure o plano de estudos com associação Curso–UC por ano e semestre, impedindo duplicações.</p>
    </div>
    <div class="feature-card reveal reveal-delay-3">
      <div class="feature-icon">📋</div>
      <h3>Ficha do Aluno</h3>
      <p>O aluno preenche dados, faz upload de foto e submete para validação. Fluxo: <strong>Rascunho → Submetida → Aprovada/Rejeitada</strong> com observações.</p>
    </div>
    <div class="feature-card reveal reveal-delay-1">
      <div class="feature-icon">📝</div>
      <h3>Pedidos de Matrícula</h3>
      <p>Aluno pede inscrição, funcionário aprova ou rejeita com observações. Registo automático de quem decidiu, data e hora.</p>
    </div>
    <div class="feature-card reveal reveal-delay-2">
      <div class="feature-icon">📊</div>
      <h3>Pautas de Avaliação</h3>
      <p>Criação de pautas por UC, ano letivo e época (Normal, Recurso, Especial). Alunos elegíveis são adicionados automaticamente via inscrição.</p>
    </div>
    <div class="feature-card reveal reveal-delay-3">
      <div class="feature-icon">🔍</div>
      <h3>Auditoria Completa</h3>
      <p>Registo de todas as decisões — quem aprovou ou rejeitou fichas e matrículas, com timestamp. Histórico consultável a qualquer momento.</p>
    </div>
  </div>
</section>

<!-- ═══════════════ COMO FUNCIONA ═══════════════ -->
<section class="section section-alt section-center" id="como-funciona">
  <div class="section-tag">Como Funciona</div>
  <h2 class="section-title reveal">Fluxo simples, do registo à avaliação</h2>
  <p class="section-sub reveal reveal-delay-1">Quatro passos para gerir um aluno do início ao fim.</p>
  <div class="steps-grid">
    <div class="step reveal reveal-delay-1">
      <div class="step-num">1</div>
      <h3>Registo & Ficha</h3>
      <p>O aluno cria conta, preenche a ficha com dados pessoais e foto e submete para o Gestor Pedagógico.</p>
    </div>
    <div class="step reveal reveal-delay-2">
      <div class="step-num">2</div>
      <h3>Validação da Ficha</h3>
      <p>O Gestor analisa a ficha e aprova ou rejeita com observações. O aluno é notificado do estado.</p>
    </div>
    <div class="step reveal reveal-delay-3">
      <div class="step-num">3</div>
      <h3>Pedido de Matrícula</h3>
      <p>Com ficha aprovada, o aluno pede matrícula. O Funcionário dos Serviços Académicos processa o pedido.</p>
    </div>
    <div class="step reveal reveal-delay-4">
      <div class="step-num">4</div>
      <h3>Pautas & Notas</h3>
      <p>O Funcionário cria pautas e lança as notas. O aluno consulta o seu histórico de avaliações.</p>
    </div>
  </div>
</section>

<!-- ═══════════════ PERFIS ═══════════════ -->
<section class="section section-center" id="perfis">
  <div class="section-tag">Perfis de Acesso</div>
  <h2 class="section-title reveal">Cada utilizador vê apenas o que precisa</h2>
  <p class="section-sub reveal reveal-delay-1">Segurança por design — cada perfil tem acesso exclusivo às suas funcionalidades.</p>
  <div class="perfis-grid">
    <div class="perfil-card reveal reveal-delay-1">
      <span class="perfil-emoji">👨‍🎓</span>
      <h3>Aluno</h3>
      <div class="perfil-role">Área Pessoal</div>
      <ul>
        <li>Preencher ficha com fotografia</li>
        <li>Submeter ficha para validação</li>
        <li>Pedir matrícula num curso</li>
        <li>Consultar estado dos pedidos</li>
        <li>Ver histórico de notas</li>
      </ul>
    </div>
    <div class="perfil-card reveal reveal-delay-2">
      <span class="perfil-emoji">👨‍💼</span>
      <h3>Funcionário</h3>
      <div class="perfil-role">Serviços Académicos</div>
      <ul>
        <li>Listar pedidos pendentes</li>
        <li>Aprovar ou rejeitar matrículas</li>
        <li>Criar pautas de avaliação</li>
        <li>Lançar e editar notas finais</li>
      </ul>
    </div>
    <div class="perfil-card reveal reveal-delay-3">
      <span class="perfil-emoji">👩‍🏫</span>
      <h3>Gestor Pedagógico</h3>
      <div class="perfil-role">Gestão Curricular</div>
      <ul>
        <li>Criar e gerir cursos e UCs</li>
        <li>Configurar plano de estudos</li>
        <li>Validar/rejeitar fichas de aluno</li>
        <li>Acesso às funcionalidades do Funcionário</li>
      </ul>
    </div>
    <div class="perfil-card reveal reveal-delay-4">
      <span class="perfil-emoji">⚙️</span>
      <h3>Administrador</h3>
      <div class="perfil-role">Acesso Total</div>
      <ul>
        <li>Gerir todos os utilizadores</li>
        <li>Alterar perfis e passwords</li>
        <li>Ativar/desativar contas</li>
        <li>Acesso completo ao sistema</li>
      </ul>
    </div>
  </div>
</section>


  <div class="cta-buttons reveal reveal-delay-1">
    <a href="academicologin.php"    class="cta-btn cta-btn-primary">⚡ Entrar no Sistema</a>
    <a href="academicoregisto.php" class="cta-btn cta-btn-secondary">Criar Conta</a>
  </div>
</section>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer>
  <div class="footer-top">
    <div>
      <div class="footer-brand-icon">📚</div>
      <div class="footer-brand-name">Sistema Académico</div>
      <p class="footer-brand-desc">Plataforma de gestão para instituições de ensino superior. Desenvolvida com PHP e MySQL, compatível com XAMPP.</p>
    </div>
    <div class="footer-col">
      <h4>Navegação</h4>
      <ul>
        <li><a href="index.php">Início</a></li>
        <li><a href="#funcionalidades">Funcionalidades</a></li>
        <li><a href="#como-funciona">Como Funciona</a></li>
        <li><a href="#perfis">Perfis</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Acesso</h4>
      <ul>
        <li><a href="academicologin.php">Entrar</a></li>
        <li><a href="academicoregisto.php">Criar Conta</a></li>
        <li><a href="pages/aluno/dashboard.php">Área Aluno</a></li>
        <li><a href="pages/funcionario/dashboard.php">Área Funcionário</a></li>
        <li><a href="pages/gestor/dashboard.php">Área Gestor</a></li>
        <li><a href="pages/admin/dashboard.php">Administração</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Suporte</h4>
      <ul>
        <li><a href="sobre.php">Sobre o Sistema</a></li>
        <li><a href="ajuda.php">Ajuda &amp; FAQ</a></li>
        <li><a href="acesso_negado.php">Acesso Negado</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span class="footer-copy">© <script>document.write(new Date().getFullYear())</script> Sistema Académico — Instituição de Ensino Superior</span>
    <div class="footer-badges">
      <span class="footer-badge">PHP 8+</span>
      <span class="footer-badge">MySQL</span>
      <span class="footer-badge">XAMPP</span>
      <span class="footer-badge">PDO</span>
    </div>
  </div>
</footer>

<script>
// Intersection Observer para animações de scroll
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
