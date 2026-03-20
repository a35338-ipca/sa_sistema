<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['admin']);
$db  = getDB();
$myId= uid();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action==='toggle' && $id !== $myId) {
        $db->prepare('UPDATE utilizadores SET ativo=NOT ativo WHERE id=?')->execute([$id]);
        flash('inf','Estado do utilizador alterado.');
    } elseif ($action==='alterar_perfil' && $id !== $myId) {
        $perfil = $_POST['perfil'] ?? '';
        if (in_array($perfil,['aluno','funcionario','gestor','admin'],true)) {
            $db->prepare('UPDATE utilizadores SET perfil=? WHERE id=?')->execute([$perfil,$id]);
            flash('ok','Perfil alterado com sucesso.');
        }
    } elseif ($action==='reset_pass' && $id) {
        $newPass = $_POST['new_pass'] ?? '';
        if(strlen($newPass) < 6) { flash('err','Password deve ter pelo menos 6 caracteres.'); }
        else {
            $db->prepare('UPDATE utilizadores SET password_hash=? WHERE id=?')->execute([password_hash($newPass,PASSWORD_DEFAULT),$id]);
            flash('ok','Password redefinida com sucesso.');
        }
    } elseif ($action==='criar') {
        $nome   = trim($_POST['nome'] ?? '');
        $email  = strtolower(trim($_POST['email'] ?? ''));
        $pass   = $_POST['pass'] ?? '';
        $perfil = $_POST['perfil'] ?? 'aluno';
        if (!$nome||!$email||!$pass) { flash('err','Preencha todos os campos.'); }
        elseif (strlen($pass)<6) { flash('err','Password mínimo 6 caracteres.'); }
        else {
            try {
                $db->prepare('INSERT INTO utilizadores(nome,email,password_hash,perfil) VALUES(?,?,?,?)')->execute([$nome,$email,password_hash($pass,PASSWORD_DEFAULT),$perfil]);
                flash('ok','Utilizador criado com sucesso.');
            } catch(PDOException $e) { flash('err','Email já existe.'); }
        }
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

$busca   = trim($_GET['q'] ?? '');
$pfiltro = $_GET['perfil'] ?? '';
$where   = 'WHERE 1=1';
$params  = [];
if ($busca)   { $where .= ' AND (nome LIKE ? OR email LIKE ?)'; $params[]="%$busca%"; $params[]="%$busca%"; }
if ($pfiltro) { $where .= ' AND perfil=?'; $params[]=$pfiltro; }

$st = $db->prepare("SELECT * FROM utilizadores $where ORDER BY criado_em DESC");
$st->execute($params); $users = $st->fetchAll();

$pageTitle = 'Gestão de Utilizadores';
include __DIR__ . '/../../includes/header.php';
?>

<div class="card">
  <div class="card-hd">➕ Criar Novo Utilizador</div>
  <form method="post">
    <input type="hidden" name="action" value="criar">
    <div class="g3">
      <div class="fg"><label>Nome *</label><input type="text" name="nome" placeholder="Nome completo" required></div>
      <div class="fg"><label>Email *</label><input type="email" name="email" placeholder="email@exemplo.pt" required></div>
      <div class="fg"><label>Password *</label><input type="password" name="pass" placeholder="Mínimo 6 caracteres" required></div>
    </div>
    <div class="fg" style="max-width:240px">
      <label>Perfil</label>
      <select name="perfil">
        <option value="aluno">👨‍🎓 Aluno</option>
        <option value="funcionario">👨‍💼 Funcionário</option>
        <option value="gestor">👩‍🏫 Gestor</option>
        <option value="admin">⚙️ Admin</option>
      </select>
    </div>
    <button type="submit" class="btn bp">➕ Criar Utilizador</button>
  </form>
</div>

<div class="card">
  <div class="card-hd">👥 Utilizadores (<?= count($users) ?>)</div>
  <form method="get" style="display:flex;gap:.75rem;margin-bottom:1.1rem;flex-wrap:wrap">
    <input type="text" name="q" value="<?= e($busca) ?>" placeholder="Pesquisar nome ou email..." style="flex:1;min-width:200px">
    <select name="perfil">
      <option value="">Todos os perfis</option>
      <option value="aluno"       <?= $pfiltro==='aluno'?'selected':'' ?>>Aluno</option>
      <option value="funcionario" <?= $pfiltro==='funcionario'?'selected':'' ?>>Funcionário</option>
      <option value="gestor"      <?= $pfiltro==='gestor'?'selected':'' ?>>Gestor</option>
      <option value="admin"       <?= $pfiltro==='admin'?'selected':'' ?>>Admin</option>
    </select>
    <button type="submit" class="btn bn">🔍 Filtrar</button>
    <a href="?" class="btn bn">Limpar</a>
  </form>

  <?php if($users): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Nome</th><th>Email</th><th>Perfil</th><th>Estado</th><th>Registado em</th><th>Ações</th></tr>
    <?php foreach($users as $u): $isMe = ($u['id']===$myId); ?>
    <tr style="opacity:<?= $u['ativo']?1:.5 ?>">
      <td><strong><?= e($u['nome']) ?></strong><?= $isMe?' <span style="font-size:.7rem;color:#16a34a">(você)</span>':'' ?></td>
      <td style="font-size:.82rem;color:#64748b"><?= e($u['email']) ?></td>
      <td>
        <?php if(!$isMe): ?>
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="alterar_perfil">
          <input type="hidden" name="id" value="<?= $u['id'] ?>">
          <select name="perfil" onchange="this.form.submit()" style="padding:.3rem .5rem;font-size:.8rem;width:auto">
            <?php foreach(['aluno','funcionario','gestor','admin'] as $p): ?>
            <option value="<?= $p ?>" <?= $u['perfil']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
        <?php else: ?>
        <span style="font-size:.85rem;font-weight:700;color:var(--p)"><?= ucfirst($u['perfil']) ?></span>
        <?php endif; ?>
      </td>
      <td><?= $u['ativo'] ? '<span style="color:#16a34a;font-weight:700">● Ativo</span>' : '<span style="color:#dc2626;font-weight:700">● Inativo</span>' ?></td>
      <td style="font-size:.8rem;color:#64748b"><?= date('d/m/Y', strtotime($u['criado_em'])) ?></td>
      <td style="white-space:nowrap;display:flex;gap:.4rem;flex-wrap:wrap">
        <?php if(!$isMe): ?>
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $u['id'] ?>">
          <button type="submit" class="btn bsm <?= $u['ativo']?'bd':'bs' ?>"><?= $u['ativo']?'Desativar':'Ativar' ?></button>
        </form>
        <form method="post" style="display:inline" onsubmit="var p=prompt('Nova password (mín. 6 caracteres):');if(!p||p.length<6){alert('Password inválida');return false;}this.querySelector('[name=new_pass]').value=p;return true;">
          <input type="hidden" name="action" value="reset_pass">
          <input type="hidden" name="id" value="<?= $u['id'] ?>">
          <input type="hidden" name="new_pass" value="">
          <button type="submit" class="btn bsm bw">🔑 Reset Pass</button>
        </form>
        <?php else: ?>
        <span style="color:#94a3b8;font-size:.78rem">—</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Nenhum utilizador encontrado.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
