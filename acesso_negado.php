<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Acesso Negado — Sistema Académico</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#f5f7fa;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;-webkit-font-smoothing:antialiased}
.box{background:#fff;border:1px solid #e1e7ef;border-radius:20px;padding:3.5rem 3rem;text-align:center;max-width:440px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,.08)}
.icon-wrap{width:80px;height:80px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 1.75rem;border:2px solid #fecaca}
h1{font-family:'Playfair Display',serif;font-size:1.875rem;color:#0d1117;margin-bottom:.75rem}
p{color:#6b7280;line-height:1.7;font-size:.9rem;margin-bottom:.5rem}
.divider{border:none;border-top:1px solid #e1e7ef;margin:1.75rem 0}
.btns{display:flex;flex-direction:column;gap:.75rem}
.btn{display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:10px;font-weight:700;font-size:.9rem;transition:all .2s;cursor:pointer}
.btn-primary{background:#0d1117;color:#fff}
.btn-primary:hover{background:#1d6fce;transform:translateY(-1px);box-shadow:0 6px 20px rgba(29,111,206,.3);text-decoration:none}
.btn-secondary{background:#f5f7fa;color:#374151;border:1px solid #e1e7ef}
.btn-secondary:hover{background:#eef2f8;text-decoration:none}
.code{font-family:monospace;background:#f1f5f9;padding:.2rem .5rem;border-radius:4px;font-size:.82rem;color:#0369a1}
</style>
</head>
<body>
<div class="box">
  <div class="icon-wrap">🚫</div>
  <h1>Acesso Negado</h1>
  <p>Não tem permissão para aceder a esta página.</p>
  <p>O seu perfil não tem acesso a esta área do sistema. Cada perfil tem acesso apenas às suas funcionalidades.</p>
  <hr class="divider">
  <div class="btns">
    <a href="<?= SITE_URL ?>/academicologin.php"   class="btn btn-primary">← Voltar ao Login</a>
    <a href="<?= SITE_URL ?>/index.php"  class="btn btn-secondary">🏠 Página Inicial</a>
    <a href="<?= SITE_URL ?>/ajuda.php"   class="btn btn-secondary">❓ Ajuda</a>
  </div>
</div>
</body>
</html>
