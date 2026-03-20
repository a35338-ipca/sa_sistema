<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sobre — Sistema Académico</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--ink:#0d1117;--blue:#1d6fce;--gray:#6b7280;--light:#f5f7fa;--white:#fff;--brd:#e1e7ef;--font:'Plus Jakarta Sans',sans-serif;--serif:'Playfair Display',serif}
body{font-family:var(--font);background:var(--white);color:var(--ink);-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}

/* NAV */
nav{display:flex;align-items:center;justify-content:space-between;padding:0 2.5rem;height:64px;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid var(--brd);position:sticky;top:0;z-index:50}
.nbrand{display:flex;align-items:center;gap:.75rem;font-weight:800;font-size:1rem}
.nbrand-ico{width:36px;height:36px;background:var(--ink);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1.05rem}
.nlinks{display:flex;align-items:center;gap:.25rem}
.nlinks a{padding:.45rem .9rem;border-radius:8px;font-size:.875rem;font-weight:500;color:var(--gray);transition:all .15s}
.nlinks a:hover{background:var(--light);color:var(--ink)}
.ncta{background:var(--ink)!important;color:#fff!important;font-weight:700!important}
.ncta:hover{background:var(--blue)!important}

/* PAGE HEADER */
.page-hero{background:var(--light);padding:5rem 2rem 4rem;text-align:center;border-bottom:1px solid var(--brd)}
.page-tag{display:inline-block;background:#e8f0fb;color:var(--blue);padding:.3rem .875rem;border-radius:50px;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;margin-bottom:1.1rem}
.page-hero h1{font-family:var(--serif);font-size:clamp(2rem,4vw,3rem);margin-bottom:.875rem}
.page-hero p{color:var(--gray);max-width:520px;margin:0 auto;font-size:1rem;line-height:1.7}

/* CONTENT */
.container{max-width:900px;margin:0 auto;padding:4rem 2rem}
.section-block{margin-bottom:3rem}
.section-block h2{font-family:var(--serif);font-size:1.6rem;margin-bottom:1rem;color:var(--ink)}
.section-block p{color:var(--gray);line-height:1.8;margin-bottom:.875rem;font-size:.975rem}
.tech-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-top:1.5rem}
.tech-card{background:var(--light);border:1px solid var(--brd);border-radius:12px;padding:1.25rem;text-align:center}
.tech-card .ticon{font-size:2rem;margin-bottom:.5rem;display:block}
.tech-card h3{font-size:.9rem;font-weight:800;margin-bottom:.25rem}
.tech-card p{font-size:.78rem;color:var(--gray)}
.rf-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;margin-top:1.5rem}
.rf-item{background:var(--light);border:1px solid var(--brd);border-radius:12px;padding:1.25rem}
.rf-item-num{font-size:.72rem;font-weight:800;color:var(--blue);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.4rem}
.rf-item h3{font-size:.9rem;font-weight:800;margin-bottom:.35rem}
.rf-item p{font-size:.82rem;color:var(--gray);line-height:1.6}
.back-btn{display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;background:var(--ink);color:#fff;border-radius:8px;font-weight:700;font-size:.875rem;transition:background .15s}
.back-btn:hover{background:var(--blue)}

/* FOOTER */
footer{background:var(--ink);padding:2rem 3rem;display:flex;align-items:center;justify-content:space-between;color:rgba(255,255,255,.35);font-size:.82rem}
.flogo{font-weight:800;color:#fff;display:flex;align-items:center;gap:.5rem}
</style>
</head>
<body>
<nav>
  <a href="index.php" class="nbrand"><div class="nbrand-ico">📚</div>Sistema Académico</a>
  <div class="nlinks">
    <a href="index.php#funcionalidades">Funcionalidades</a>
    <a href="index.php#perfis">Perfis</a>
    <a href="ajuda.php">Ajuda</a>
    <?php if(isLoggedIn()): ?>
    <a href="<?= dashboardUrl() ?>" class="ncta">Dashboard →</a>
    <?php else: ?>
    <a href="academicologin.php">Entrar</a>
    <a href="academicoregisto.php" class="ncta">Começar →</a>
    <?php endif; ?>
  </div>
</nav>

<div class="page-hero">
  <div class="page-tag">Sobre o Projeto</div>
  <h1>Sistema Académico</h1>
  <p>Plataforma web de gestão académica desenvolvida em PHP com base de dados MySQL, projetada para instituições de ensino superior.</p>
</div>

<div class="container">

  <div class="section-block">
    <h2>Contexto e Motivação</h2>
    <p>Este sistema foi desenvolvido para suportar os processos dos <strong>Serviços Académicos</strong> e da <strong>Gestão Pedagógica</strong> de uma instituição de ensino superior. A plataforma permite gerir cursos e planos de estudo, validar fichas de aluno, tratar pedidos de matrícula e produzir pautas de avaliação.</p>
    <p>O sistema implementa <strong>fluxos com estados</strong> (submissão → validação → aprovação/rejeição) com acesso controlado por perfis, garantindo que cada utilizador apenas acede ao que lhe diz respeito.</p>
  </div>

  <div class="section-block">
    <h2>Stack Tecnológica</h2>
    <div class="tech-grid">
      <div class="tech-card"><span class="ticon">🐘</span><h3>PHP 8+</h3><p>Backend com PHP nativo, sem frameworks, fácil de compreender e manter</p></div>
      <div class="tech-card"><span class="ticon">🗄️</span><h3>MySQL / MariaDB</h3><p>Base de dados relacional compatível com XAMPP e phpMyAdmin</p></div>
      <div class="tech-card"><span class="ticon">🔌</span><h3>PDO</h3><p>Acesso à BD via PDO com prepared statements para máxima segurança</p></div>
      <div class="tech-card"><span class="ticon">🎨</span><h3>HTML5 + CSS3</h3><p>Interface responsiva sem dependências externas, puro CSS com variáveis</p></div>
      <div class="tech-card"><span class="ticon">📦</span><h3>XAMPP</h3><p>Totalmente compatível, basta importar o database.sql no phpMyAdmin</p></div>
      <div class="tech-card"><span class="ticon">🔐</span><h3>BCrypt</h3><p>Passwords com hash seguro via password_hash() / password_verify()</p></div>
    </div>
  </div>

  <div class="section-block">
    <h2>Requisitos Funcionais Implementados</h2>
    <div class="rf-grid">
      <div class="rf-item"><div class="rf-item-num">RF1</div><h3>Autenticação e Sessão</h3><p>Login/logout seguro, controlo de acesso por perfil, hash bcrypt, expiração de sessão.</p></div>
      <div class="rf-item"><div class="rf-item-num">RF2</div><h3>Gestão de Cursos</h3><p>CRUD de cursos e UCs, plano de estudos por ano/semestre, proteção contra duplicações.</p></div>
      <div class="rf-item"><div class="rf-item-num">RF3</div><h3>Ficha do Aluno</h3><p>Upload de foto, dados pessoais, fluxo Rascunho→Submetida→Aprovada/Rejeitada com observações.</p></div>
      <div class="rf-item"><div class="rf-item-num">RF4</div><h3>Pedido de Matrícula</h3><p>Pendente→Aprovada/Rejeitada, registo de responsável e timestamp da decisão.</p></div>
      <div class="rf-item"><div class="rf-item-num">RF5</div><h3>Pautas de Avaliação</h3><p>Criação por UC/ano/época, alunos elegíveis automáticos, lançamento de notas 0–20.</p></div>
    </div>
  </div>

  <div class="section-block">
    <h2>Segurança</h2>
    <p>O sistema foi desenvolvido com boas práticas de segurança web: <strong>prepared statements PDO</strong> para prevenir SQL injection, <strong>htmlspecialchars()</strong> em todos os outputs para prevenir XSS, <strong>session_regenerate_id()</strong> no login para prevenir fixação de sessão, <strong>CSRF tokens</strong> nos formulários críticos e <strong>validação de tipo MIME</strong> nos uploads de fotografia.</p>
    <p>O diretório de uploads está protegido com <code>.htaccess</code> que bloqueia a execução de PHP, prevenindo ataques de upload malicioso.</p>
  </div>

  <div style="display:flex;gap:.875rem;flex-wrap:wrap">
    <a href="index.php" class="back-btn">← Voltar ao Início</a>
    <a href="ajuda.php"  style="display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;background:#f5f7fa;color:var(--ink);border:1px solid var(--brd);border-radius:8px;font-weight:700;font-size:.875rem">❓ Ajuda &amp; FAQ</a>
    <?php if(!isLoggedIn()): ?>
    <a href="academicologin.php" style="display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;background:#1d6fce;color:#fff;border-radius:8px;font-weight:700;font-size:.875rem">⚡ Aceder ao Sistema</a>
    <?php endif; ?>
  </div>
</div>

<footer>
  <div class="flogo">📚 Sistema Académico</div>
  <span>© <?= date('Y') ?> — Instituição de Ensino Superior</span>
</footer>
</body>
</html>
