<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) { header('Location: '.dashboardUrl()); exit; }

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome']   ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $pass   = $_POST['pass']   ?? '';
    $pass2  = $_POST['pass2']  ?? '';
    $perfil = $_POST['perfil'] ?? 'aluno';

    if (!$nome || !$email || !$pass || !$pass2) {
        $erro = 'Por favor preencha todos os campos obrigatórios.';
    } elseif (mb_strlen($nome) < 3) {
        $erro = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Endereço de email inválido.';
    } elseif (strlen($pass) < 6) {
        $erro = 'A password deve ter pelo menos 6 caracteres.';
    } elseif ($pass !== $pass2) {
        $erro = 'As passwords não coincidem.';
    } elseif (!in_array($perfil, ['aluno','funcionario','gestor'], true)) {
        $erro = 'Perfil inválido.';
    } else {
        $db = getDB();
        $chk = $db->prepare('SELECT id FROM utilizadores WHERE email=? LIMIT 1');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $erro = 'Este email já está registado. Tente iniciar sessão.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->prepare('INSERT INTO utilizadores(nome,email,password_hash,perfil) VALUES(?,?,?,?)')->execute([$nome,$email,$hash,$perfil]);
            doLogin($email, $pass);
            header('Location: '.dashboardUrl()); exit;
        }
    }
}

$perfilSel = $_POST['perfil'] ?? 'aluno';
$perfilInfo = [
    'aluno'       => ['👨‍🎓','Aluno','Aceda à sua ficha, matrículas e notas.'],
    'funcionario' => ['👨‍💼','Funcionário','Gira matrículas e pautas de avaliação.'],
    'gestor'      => ['👩‍🏫','Gestor Pedagógico','Gere cursos, UCs, planos e fichas.'],
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Criar Conta — Sistema Académico</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--ink:#0d1117;--blue:#1d6fce;--gray:#6b7280;--light:#f5f7fa;--white:#fff;--brd:#e1e7ef;--err:#dc2626;--font:'Plus Jakarta Sans',sans-serif;--serif:'Playfair Display',serif}
body{font-family:var(--font);background:var(--light);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}

.page{width:100%;max-width:980px;display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;align-items:start}
.left{position:sticky;top:2rem}
.left-brand{display:flex;align-items:center;gap:.75rem;font-weight:800;font-size:1rem;color:var(--ink);margin-bottom:2.5rem}
.left-brand-ico{width:38px;height:38px;background:var(--ink);border-radius:9px;display:flex;align-items:center;justify-content:center}
.left-brand a{color:var(--ink)}
.left h2{font-family:var(--serif);font-size:2rem;color:var(--ink);line-height:1.2;margin-bottom:.875rem}
.left h2 em{font-style:italic;color:var(--blue)}
.left p{color:var(--gray);font-size:.9rem;line-height:1.7;margin-bottom:2rem}
.perfil-cards{display:flex;flex-direction:column;gap:.75rem}
.pc{background:var(--white);border:2px solid var(--brd);border-radius:12px;padding:1rem 1.25rem;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:.875rem}
.pc:hover,.pc.sel{border-color:var(--blue);background:#eff6ff}
.pc .pico{font-size:1.6rem;flex-shrink:0}
.pc h3{font-size:.9rem;font-weight:800;margin-bottom:.15rem}
.pc p{font-size:.78rem;color:var(--gray);margin:0}

.right{background:var(--white);border:1px solid var(--brd);border-radius:16px;padding:2.5rem;box-shadow:0 4px 24px rgba(0,0,0,.06)}
.right h1{font-family:var(--serif);font-size:1.75rem;color:var(--ink);margin-bottom:.35rem}
.right .subtitle{color:var(--gray);font-size:.9rem;margin-bottom:2rem}
.fg{margin-bottom:1.1rem}
.fg label{display:block;font-size:.82rem;font-weight:700;color:var(--ink);margin-bottom:.4rem}
.fg label span{color:#94a3b8;font-weight:500}
.fg input,.fg select{width:100%;padding:.7rem 1rem;border:1.5px solid var(--brd);border-radius:9px;font-family:var(--font);font-size:.9rem;color:var(--ink);background:var(--white);transition:border-color .15s,box-shadow .15s;outline:none;appearance:none}
.fg input:focus,.fg select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,111,206,.12)}
.fg input::placeholder{color:#c4c9d4}
.fg input.error{border-color:var(--err)}
.pass-hint{font-size:.75rem;color:#94a3b8;margin-top:.35rem}
.btn-submit{width:100%;padding:.8rem;background:var(--ink);color:var(--white);border:none;border-radius:9px;font-family:var(--font);font-size:.975rem;font-weight:800;cursor:pointer;transition:all .2s;margin-top:.5rem}
.btn-submit:hover{background:var(--blue);transform:translateY(-1px);box-shadow:0 6px 20px rgba(29,111,206,.3)}
.alert-err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:.75rem 1rem;border-radius:9px;font-size:.875rem;font-weight:500;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.5rem}
.note{background:#fefce8;border:1px solid #fde68a;color:#92400e;padding:.7rem 1rem;border-radius:8px;font-size:.8rem;margin-top:1rem;line-height:1.5}
.form-footer{text-align:center;margin-top:1.5rem;font-size:.875rem;color:var(--gray)}
.form-footer a{color:var(--blue);font-weight:700}
.form-footer a:hover{text-decoration:underline}

@media(max-width:720px){.page{grid-template-columns:1fr}.left{position:static;margin-bottom:0}.perfil-cards{flex-direction:row;flex-wrap:wrap}.pc{flex:1;min-width:140px}}
@media(max-width:480px){.perfil-cards{flex-direction:column}}
</style>
</head>
<body>

<div class="page">

  <!-- LEFT: branding + perfil chooser -->
  <div class="left">
    <div class="left-brand">
      <a href="index.php"><div class="left-brand-ico">📚</div></a>
      <a href="index.php">Sistema Académico</a>
    </div>
    <h2>Crie a sua <em>conta</em></h2>
    <p>Selecione o seu perfil e preencha os dados. A conta será criada imediatamente.</p>

    <div class="perfil-cards">
      <?php foreach($perfilInfo as $k => [$ico,$name,$desc]): ?>
      <div class="pc <?= $perfilSel===$k?'sel':'' ?>" onclick="selectPerfil('<?= $k ?>')">
        <div class="pico"><?= $ico ?></div>
        <div>
          <h3><?= $name ?></h3>
          <p><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- RIGHT: form -->
  <div class="right">
    <h1>Criar Conta</h1>
    <p class="subtitle">Preencha os dados para se registar no sistema.</p>

    <?php if ($erro): ?>
    <div class="alert-err">⚠️ <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <input type="hidden" name="perfil" id="perfilInput" value="<?= htmlspecialchars($perfilSel) ?>">

      <div class="fg">
        <label for="nome">Nome Completo</label>
        <input type="text" id="nome" name="nome"
               placeholder="O seu nome completo"
               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
               autocomplete="name" required
               class="<?= $erro&&!($_POST['nome']??'') ? 'error' : '' ?>">
      </div>

      <div class="fg">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               placeholder="seu@email.pt"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               autocomplete="email" required
               class="<?= ($erro && str_contains($erro,'email')) ? 'error' : '' ?>">
      </div>

      <div class="fg">
        <label for="pass">Password</label>
        <input type="password" id="pass" name="pass"
               placeholder="Mínimo 6 caracteres"
               autocomplete="new-password" required>
        <div class="pass-hint">Mínimo 6 caracteres. Escolha uma password segura.</div>
      </div>

      <div class="fg">
        <label for="pass2">Confirmar Password</label>
        <input type="password" id="pass2" name="pass2"
               placeholder="Repita a password"
               autocomplete="new-password" required
               class="<?= ($erro && str_contains($erro,'passwords')) ? 'error' : '' ?>">
      </div>

      <button type="submit" class="btn-submit">Criar Conta →</button>

      <div class="note">
        ℹ️ O perfil <strong>Administrador</strong> não está disponível no registo público. Apenas pode ser atribuído por um administrador do sistema.
      </div>
    </form>

    <div class="form-footer">
      Já tem conta? <a href="<?= SITE_URL ?>/academicologin.php">Iniciar sessão</a>
      &nbsp;·&nbsp;
      <a href="<?= SITE_URL ?>/index.php" style="color:#94a3b8">← Início</a>
    </div>
  </div>
</div>

<script>
function selectPerfil(p) {
  document.getElementById('perfilInput').value = p;
  document.querySelectorAll('.pc').forEach(c => c.classList.remove('sel'));
  event.currentTarget.classList.add('sel');
}
// Mark selected on load
document.querySelectorAll('.pc').forEach((c,i) => {
  const keys = ['aluno','funcionario','gestor'];
  if (keys[i] === document.getElementById('perfilInput').value) c.classList.add('sel');
});
</script>
</body>
</html>
