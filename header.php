<?php
require_once __DIR__ . '/auth.php';
$_u     = currentUser();
$_perf  = $_u['perfil'] ?? '';

$_navAluno = [
    ['🏠','Dashboard',    SITE_URL.'/pages/aluno/dashboard.php'],
    ['📋','Minha Ficha',  SITE_URL.'/pages/aluno/ficha.php'],
    ['📝','Matrícula',    SITE_URL.'/pages/aluno/matricula.php'],
    ['📊','Minhas Notas', SITE_URL.'/pages/aluno/notas.php'],
];
$_navFunc = [
    ['🏠','Dashboard',  SITE_URL.'/pages/funcionario/dashboard.php'],
    ['📝','Matrículas', SITE_URL.'/pages/funcionario/matriculas.php'],
    ['📊','Pautas',     SITE_URL.'/pages/funcionario/pautas.php'],
];
$_navGest = [
    ['🏠','Dashboard',     SITE_URL.'/pages/gestor/dashboard.php'],
    ['🎓','Cursos',        SITE_URL.'/pages/gestor/cursos.php'],
    ['📚','UCs',           SITE_URL.'/pages/gestor/ucs.php'],
    ['🗂','Plano Estudos', SITE_URL.'/pages/gestor/plano.php'],
    ['👤','Fichas Aluno',  SITE_URL.'/pages/gestor/fichas.php'],
    ['📝','Matrículas',    SITE_URL.'/pages/funcionario/matriculas.php'],
    ['📊','Pautas',        SITE_URL.'/pages/funcionario/pautas.php'],
];
$_navAdmin = [
    ['🏠','Dashboard',    SITE_URL.'/pages/admin/dashboard.php'],
    ['👥','Utilizadores', SITE_URL.'/pages/admin/utilizadores.php'],
    ['🎓','Cursos',       SITE_URL.'/pages/gestor/cursos.php'],
    ['📚','UCs',          SITE_URL.'/pages/gestor/ucs.php'],
    ['🗂','Plano Estudos',SITE_URL.'/pages/gestor/plano.php'],
    ['👤','Fichas Aluno', SITE_URL.'/pages/gestor/fichas.php'],
    ['📝','Matrículas',   SITE_URL.'/pages/funcionario/matriculas.php'],
    ['📊','Pautas',       SITE_URL.'/pages/funcionario/pautas.php'],
];

$_nav = match($_perf) {
    'aluno'       => $_navAluno,
    'funcionario' => $_navFunc,
    'gestor'      => $_navGest,
    'admin'       => $_navAdmin,
    default       => [],
};

$_colors = ['aluno'=>'#4f46e5','funcionario'=>'#0891b2','gestor'=>'#7c3aed','admin'=>'#be123c'];
$_col    = $_colors[$_perf] ?? '#1e293b';
$_cur    = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle).' — ' : '' ?>Sistema Académico</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --p:<?= $_col ?>;
  --p10:color-mix(in srgb,var(--p) 10%,transparent);
  --p20:color-mix(in srgb,var(--p) 20%,transparent);
  --bg:#f1f5f9;
  --card:#fff;
  --brd:#e2e8f0;
  --txt:#0f172a;
  --mut:#64748b;
  --r:10px;
  --sh:0 1px 3px rgba(0,0,0,.07),0 4px 16px rgba(0,0,0,.05);
}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--txt);min-height:100vh;display:flex;flex-direction:column}
a{color:var(--p);text-decoration:none}
a:hover{text-decoration:underline}

/* ── LAYOUT ── */
.layout{display:flex;flex:1;height:100vh;overflow:hidden}
.sidebar{width:236px;background:var(--card);border-right:1px solid var(--brd);display:flex;flex-direction:column;flex-shrink:0;overflow-y:auto}
.s-logo{padding:1.25rem;display:flex;align-items:center;gap:.625rem;border-bottom:1px solid var(--brd)}
.s-logo-ico{width:34px;height:34px;background:var(--p);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
.s-logo-txt{font-weight:800;font-size:.9rem;line-height:1.2}
.s-role{padding:.5rem 1.25rem;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--p);background:var(--p10)}
.s-nav{padding:.5rem .625rem;flex:1}
.s-nav a{display:flex;align-items:center;gap:.55rem;padding:.55rem .75rem;border-radius:8px;color:var(--mut);font-size:.875rem;font-weight:500;transition:all .15s;margin-bottom:1px}
.s-nav a:hover,.s-nav a.on{background:var(--p10);color:var(--p);text-decoration:none;font-weight:600}
.s-nav a .ico{font-size:1rem;width:20px;text-align:center}
.s-foot{padding:1rem 1.25rem;border-top:1px solid var(--brd)}
.s-user{display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem}
.s-av{width:32px;height:32px;border-radius:50%;background:var(--p);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;flex-shrink:0}
.s-info{min-width:0;flex:1}
.s-name{font-size:.82rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.s-role2{font-size:.7rem;color:var(--mut);text-transform:capitalize}
.btn-out{display:block;text-align:center;padding:.45rem;background:#fee2e2;color:#dc2626!important;border-radius:8px;font-size:.8rem;font-weight:700;transition:background .15s}
.btn-out:hover{background:#fecaca;text-decoration:none!important}

/* ── MAIN ── */
.main{flex:1;display:flex;flex-direction:column;overflow:auto}
.topbar{background:var(--card);border-bottom:1px solid var(--brd);padding:.875rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-shrink:0}
.topbar h1{font-size:1.1rem;font-weight:800;flex:1;color:var(--txt)}
.content{padding:1.5rem;flex:1}

/* ── COMPONENTS ── */
.card{background:var(--card);border:1px solid var(--brd);border-radius:var(--r);box-shadow:var(--sh);padding:1.5rem;margin-bottom:1.25rem}
.card-hd{font-size:1rem;font-weight:800;margin-bottom:1rem;color:var(--txt);display:flex;align-items:center;gap:.5rem}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1.25rem}
.stat{background:var(--card);border:1px solid var(--brd);border-radius:var(--r);padding:1.25rem;box-shadow:var(--sh)}
.stat-n{font-size:2.25rem;font-weight:800;color:var(--p);line-height:1}
.stat-l{font-size:.78rem;color:var(--mut);margin-top:.3rem;font-weight:600}
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.875rem}
th{background:#f8fafc;padding:.7rem 1rem;text-align:left;font-weight:700;font-size:.73rem;text-transform:uppercase;letter-spacing:.06em;color:var(--mut);border-bottom:2px solid var(--brd);white-space:nowrap}
td{padding:.7rem 1rem;border-bottom:1px solid var(--brd);vertical-align:middle}
tr:last-child td{border:none}
tr:hover td{background:#f8fafc}
.fg{margin-bottom:1.1rem}
label{display:block;font-size:.83rem;font-weight:700;margin-bottom:.35rem;color:var(--txt)}
input,select,textarea{width:100%;padding:.6rem .875rem;border:1.5px solid var(--brd);border-radius:8px;font-family:inherit;font-size:.9rem;color:var(--txt);background:var(--card);transition:border-color .15s,box-shadow .15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px var(--p20)}
textarea{resize:vertical;min-height:80px}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;border-radius:8px;font-family:inherit;font-size:.875rem;font-weight:700;cursor:pointer;border:1.5px solid transparent;transition:all .15s;text-decoration:none!important;line-height:1.4}
.btn:hover{opacity:.88}
.bp{background:var(--p);color:#fff!important;border-color:var(--p)}
.bs{background:#16a34a;color:#fff!important;border-color:#16a34a}
.bd{background:#dc2626;color:#fff!important;border-color:#dc2626}
.bn{background:#f1f5f9;color:var(--txt)!important;border-color:var(--brd)}
.bw{background:#d97706;color:#fff!important;border-color:#d97706}
.bsm{padding:.35rem .75rem;font-size:.78rem}
.alert{padding:.8rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem;font-weight:500;border:1px solid transparent}
.a-ok{background:#dcfce7;color:#166534;border-color:#bbf7d0}
.a-er{background:#fee2e2;color:#991b1b;border-color:#fecaca}
.a-in{background:#dbeafe;color:#1e40af;border-color:#bfdbfe}
.a-wa{background:#fef3c7;color:#92400e;border-color:#fde68a}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:1.1rem}
.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.1rem}
.mt1{margin-top:1rem}
.empty{text-align:center;color:var(--mut);padding:2rem;font-size:.9rem}
@media(max-width:680px){.sidebar{display:none}.g2,.g3{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="layout">
<aside class="sidebar">
  <div class="s-logo">
    <div class="s-logo-ico">📚</div>
    <div class="s-logo-txt">Sistema<br>Académico</div>
  </div>
  <div class="s-role"><?= ucfirst($_perf) ?></div>
  <nav class="s-nav">
    <?php foreach($_nav as [$ico,$lbl,$href]):
      $on = (strpos($_cur, parse_url($href,PHP_URL_PATH)) !== false) ? 'on' : '';
    ?>
    <a href="<?= $href ?>" class="<?= $on ?>"><span class="ico"><?= $ico ?></span><?= $lbl ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="s-foot">
    <div class="s-user">
      <div class="s-av"><?= strtoupper(mb_substr(nomeUser(),0,1)) ?></div>
      <div class="s-info">
        <div class="s-name"><?= e(nomeUser()) ?></div>
        <div class="s-role2"><?= ucfirst($_perf) ?></div>
      </div>
    </div>
    <a href="<?= SITE_URL ?>/logout.php" class="btn-out">Terminar Sessão</a>
  </div>
</aside>
<main class="main">
  <div class="topbar">
    <h1><?= isset($pageTitle) ? e($pageTitle) : 'Dashboard' ?></h1>
    <div style="display:flex;align-items:center;gap:.5rem;margin-left:auto">
      <?php if(isset($topActions)) echo $topActions; ?>
      <a href="<?= SITE_URL ?>/sobre.php" style="padding:.35rem .7rem;border-radius:6px;font-size:.78rem;font-weight:600;color:var(--mut);transition:all .15s;display:flex;align-items:center;gap:.3rem" onmouseover="this.style.background='var(--p10)';this.style.color='var(--p)'" onmouseout="this.style.background='';this.style.color='var(--mut)'">ℹ️ Sobre</a>
      <a href="<?= SITE_URL ?>/ajuda.php" style="padding:.35rem .7rem;border-radius:6px;font-size:.78rem;font-weight:600;color:var(--mut);transition:all .15s;display:flex;align-items:center;gap:.3rem" onmouseover="this.style.background='var(--p10)';this.style.color='var(--p)'" onmouseout="this.style.background='';this.style.color='var(--mut)'">❓ Ajuda</a>
    </div>
  </div>
  <div class="content">
<?php
if($m=flash('ok'))  echo "<div class='alert a-ok'>✅ $m</div>";
if($m=flash('err')) echo "<div class='alert a-er'>❌ $m</div>";
if($m=flash('inf')) echo "<div class='alert a-in'>ℹ️ $m</div>";
if($m=flash('war')) echo "<div class='alert a-wa'>⚠️ $m</div>";
?>
