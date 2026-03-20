<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) { header('Location: '.dashboardUrl()); exit; }

$erro = ''; $info = '';
if (isset($_GET['logout']))  $info = 'Sessão terminada com sucesso.';
if (isset($_GET['expired'])) $info = 'A sua sessão expirou. Por favor inicie sessão novamente.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['pass'] ?? '';
    if (!$email || !$pass) {
        $erro = 'Por favor preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Endereço de email inválido.';
    } elseif (doLogin($email, $pass)) {
        header('Location: '.dashboardUrl()); exit;
    } else {
        $erro = 'Email ou password incorretos. Verifique os seus dados.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Entrar — Sistema Académico</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --ink:#0d1117;--blue:#1d6fce;--blue2:#1558a8;
  --gray:#6b7280;--light:#f5f7fa;--white:#fff;
  --brd:#e1e7ef;--err:#dc2626;--ok:#16a34a;
  --font:'Plus Jakarta Sans',sans-serif;--serif:'Playfair Display',serif;--mono:'JetBrains Mono',monospace
}
body{font-family:var(--font);background:var(--light);min-height:100vh;display:grid;grid-template-columns:1fr 1fr;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}

/* LEFT PANEL */
.left{background:var(--ink);display:flex;flex-direction:column;justify-content:space-between;padding:3rem;position:relative;overflow:hidden}
.left-bg{position:absolute;inset:0;background:radial-gradient(ellipse 70% 60% at 30% 20%,rgba(29,111,206,.25) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 80% 90%,rgba(201,146,42,.1) 0%,transparent 60%);pointer-events:none}
.left-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:48px 48px;pointer-events:none}
.left-top{position:relative}
.brand{display:flex;align-items:center;gap:.75rem;font-weight:800;font-size:1rem;color:var(--white)}
.brand-ico{width:38px;height:38px;background:var(--blue);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1.1rem}
.left-mid{position:relative;flex:1;display:flex;flex-direction:column;justify-content:center;padding:3rem 0}
.left-mid h2{font-family:var(--serif);font-size:clamp(1.8rem,2.5vw,2.4rem);color:var(--white);line-height:1.2;margin-bottom:1rem}
.left-mid h2 em{font-style:italic;color:#60a5fa}
.left-mid p{color:rgba(255,255,255,.5);font-size:.9rem;line-height:1.7;max-width:360px}
.left-features{margin-top:2rem;display:flex;flex-direction:column;gap:.75rem}
.lf-item{display:flex;align-items:center;gap:.75rem;color:rgba(255,255,255,.6);font-size:.85rem}
.lf-item::before{content:'✓';display:flex;align-items:center;justify-content:center;width:22px;height:22px;background:rgba(29,111,206,.3);border:1px solid rgba(29,111,206,.5);border-radius:50%;color:#60a5fa;font-size:.75rem;font-weight:800;flex-shrink:0}
.left-bot{position:relative}
.demo-mini{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:1rem 1.25rem}
.demo-mini-title{font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);margin-bottom:.75rem}
.demo-rows{display:flex;flex-direction:column;gap:.4rem}
.demo-row{display:flex;align-items:center;gap:.75rem;cursor:pointer;padding:.3rem .5rem;border-radius:6px;transition:background .15s}
.demo-row:hover{background:rgba(255,255,255,.06)}
.demo-row .dr-badge{font-family:var(--mono);font-size:.68rem;font-weight:600;background:rgba(255,255,255,.08);color:rgba(255,255,255,.5);padding:.15rem .5rem;border-radius:4px;width:80px;text-align:center;flex-shrink:0}
.demo-row .dr-email{font-family:var(--mono);font-size:.75rem;color:rgba(255,255,255,.55)}
.demo-row .dr-arrow{margin-left:auto;color:rgba(255,255,255,.2);font-size:.8rem;transition:color .15s}
.demo-row:hover .dr-arrow{color:rgba(255,255,255,.6)}

/* RIGHT PANEL */
.right{display:flex;align-items:center;justify-content:center;padding:2rem;background:var(--white)}
.form-box{width:100%;max-width:420px}
.form-box-header{margin-bottom:2rem}
.form-box-header h1{font-family:var(--serif);font-size:1.875rem;color:var(--ink);margin-bottom:.35rem}
.form-box-header p{color:var(--gray);font-size:.9rem}
.fg{margin-bottom:1.1rem}
.fg label{display:block;font-size:.82rem;font-weight:700;color:var(--ink);margin-bottom:.4rem}
.fg input{width:100%;padding:.7rem 1rem;border:1.5px solid var(--brd);border-radius:9px;font-family:var(--font);font-size:.9rem;color:var(--ink);background:var(--white);transition:border-color .15s,box-shadow .15s;outline:none}
.fg input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,111,206,.12)}
.fg input::placeholder{color:#c4c9d4}
.fg input.error{border-color:var(--err)}
.btn-submit{width:100%;padding:.8rem;background:var(--ink);color:var(--white);border:none;border-radius:9px;font-family:var(--font);font-size:.975rem;font-weight:800;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:.25rem}
.btn-submit:hover{background:var(--blue);transform:translateY(-1px);box-shadow:0 6px 20px rgba(29,111,206,.3)}
.alert-err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:.75rem 1rem;border-radius:9px;font-size:.875rem;font-weight:500;margin-bottom:1.1rem;display:flex;align-items:center;gap:.5rem}
.alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;padding:.75rem 1rem;border-radius:9px;font-size:.875rem;font-weight:500;margin-bottom:1.1rem}
.form-footer{text-align:center;margin-top:1.5rem;font-size:.875rem;color:var(--gray)}
.form-footer a{color:var(--blue);font-weight:700}
.form-footer a:hover{text-decoration:underline}
.divider{border:none;border-top:1px solid var(--brd);margin:1.5rem 0}
.back-link{display:flex;align-items:center;justify-content:center;gap:.35rem;color:var(--gray);font-size:.83rem;font-weight:500;transition:color .15s}
.back-link:hover{color:var(--ink)}
@media(max-width:780px){body{grid-template-columns:1fr}.left{display:none}.right{min-height:100vh}}
</style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left">
  <div class="left-bg"></div>
  <div class="left-grid"></div>

  <div class="left-top">
    <a href="index.php" class="brand">
      <div class="brand-ico">📚</div>
      Sistema Académico
    </a>
  </div>

  <div class="left-mid">
    <h2>Bem-vindo de <em>volta</em></h2>
    <p>Plataforma de gestão académica para instituições de ensino superior. Cursos, fichas, matrículas e pautas num só lugar.</p>
    <div class="left-features">
      <div class="lf-item">Gestão completa de cursos e disciplinas</div>
      <div class="lf-item">Registo e acompanhamento de alunos</div>
      <div class="lf-item">Emissão de pautas e certificados</div>
      <div class="lf-item">Acesso seguro e personalizado</div>

<!-- RIGHT PANEL -->
<div class="right">
  <div class="form-box">
    <div class="form-box-header">
      <h1>Iniciar Sessão</h1>
      <p>Introduza as suas credenciais para aceder ao sistema.</p>
    </div>

    <?php if ($erro): ?>
    <div class="alert-err">⚠️ <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($info): ?>
    <div class="alert-info">ℹ️ <?= htmlspecialchars($info) ?></div>
    <?php endif; ?>

    <form method="post" id="loginForm" novalidate>
      <div class="fg">
        <label for="email">Endereço de Email</label>
        <input type="email" id="email" name="email"
               placeholder="seu@email.pt"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               autocomplete="email" required
               class="<?= $erro ? 'error' : '' ?>">
      </div>
      <div class="fg">
        <label for="pass">Password</label>
        <input type="password" id="pass" name="pass"
               placeholder="••••••••"
               autocomplete="current-password" required
               class="<?= $erro ? 'error' : '' ?>">
      </div>
      <button type="submit" class="btn-submit">
        Entrar no Sistema →
      </button>
    </form>

    <hr class="divider">

    <div class="form-footer">
      Não tem conta? <a href="<?= SITE_URL ?>/academicoregisto.php">Criar conta gratuita</a>
    </div>
    <div class="form-footer" style="margin-top:.75rem">
      <a href="<?= SITE_URL ?>/index.php" class="back-link">← Voltar ao início</a>
    </div>
  </div>
</div>

<script>
function fill(email) {
  document.getElementById('email').value = email;
  document.getElementById('pass').value = 'password';
  document.getElementById('pass').focus();
}
</script>
</body>
</html>
