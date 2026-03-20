<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['gestor','admin']);
$db  = getDB();
$uid = uid();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    if ($action==='criar' || $action==='editar') {
        $nome    = trim($_POST['nome'] ?? '');
        $codigo  = strtoupper(trim($_POST['codigo'] ?? ''));
        $desc    = trim($_POST['descricao'] ?? '');
        $cred    = max(1,(int)($_POST['creditos'] ?? 6));
        $id      = (int)($_POST['id'] ?? 0);
        if (!$nome || !$codigo) { flash('err','Nome e código são obrigatórios.'); }
        else {
            try {
                if ($action==='criar') {
                    $db->prepare('INSERT INTO unidades_curriculares(nome,codigo,descricao,creditos,criado_por) VALUES(?,?,?,?,?)')->execute([$nome,$codigo,$desc,$cred,$uid]);
                    flash('ok','Unidade Curricular criada.');
                } else {
                    $db->prepare('UPDATE unidades_curriculares SET nome=?,codigo=?,descricao=?,creditos=? WHERE id=?')->execute([$nome,$codigo,$desc,$cred,$id]);
                    flash('ok','UC atualizada.');
                }
            } catch(PDOException $e) { flash('err','Código de UC já existe.'); }
        }
    } elseif ($action==='toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('UPDATE unidades_curriculares SET ativo=NOT ativo WHERE id=?')->execute([$id]);
        flash('inf','Estado da UC alterado.');
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $db->prepare('SELECT * FROM unidades_curriculares WHERE id=?'); $st->execute([(int)$_GET['editar']]); $editar = $st->fetch();
}

$ucs = $db->query('SELECT uc.*,u.nome as criador,(SELECT COUNT(*) FROM plano_estudos pe WHERE pe.uc_id=uc.id) as num_planos FROM unidades_curriculares uc JOIN utilizadores u ON uc.criado_por=u.id ORDER BY uc.nome')->fetchAll();

$pageTitle = 'Gestão de Unidades Curriculares';
include __DIR__ . '/../../includes/header.php';
?>

<div class="card">
  <div class="card-hd"><?= $editar ? '✏️ Editar UC' : '➕ Nova Unidade Curricular' ?></div>
  <form method="post">
    <input type="hidden" name="action" value="<?= $editar ? 'editar' : 'criar' ?>">
    <?php if($editar): ?><input type="hidden" name="id" value="<?= $editar['id'] ?>"><?php endif; ?>
    <div class="g3">
      <div class="fg"><label>Nome da UC *</label><input type="text" name="nome" value="<?= e($editar['nome']??'') ?>" placeholder="Ex: Programação I" required></div>
      <div class="fg"><label>Código *</label><input type="text" name="codigo" value="<?= e($editar['codigo']??'') ?>" placeholder="Ex: PROG1" maxlength="20" required></div>
      <div class="fg"><label>Créditos ECTS</label><input type="number" name="creditos" value="<?= $editar['creditos']??6 ?>" min="1" max="30"></div>
    </div>
    <div class="fg"><label>Descrição</label><textarea name="descricao"><?= e($editar['descricao']??'') ?></textarea></div>
    <div style="display:flex;gap:.75rem">
      <button type="submit" class="btn bp">💾 <?= $editar ? 'Atualizar' : 'Criar UC' ?></button>
      <?php if($editar): ?><a href="<?= SITE_URL ?>/pages/gestor/ucs.php" class="btn bn">Cancelar</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-hd">📚 Unidades Curriculares (<?= count($ucs) ?>)</div>
  <?php if($ucs): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Nome</th><th>Código</th><th>Créditos</th><th>Cursos</th><th>Estado</th><th>Ações</th></tr>
    <?php foreach($ucs as $uc): ?>
    <tr style="opacity:<?= $uc['ativo']?1:.55 ?>">
      <td><strong><?= e($uc['nome']) ?></strong><?php if($uc['descricao']): ?><br><small style="color:#64748b;font-size:.78rem"><?= e(substr($uc['descricao'],0,60)) ?></small><?php endif; ?></td>
      <td><code><?= e($uc['codigo']) ?></code></td>
      <td><?= $uc['creditos'] ?> ECTS</td>
      <td><?= $uc['num_planos'] ?></td>
      <td><?= $uc['ativo'] ? '<span style="color:#16a34a;font-weight:700">● Ativo</span>' : '<span style="color:#94a3b8">● Inativo</span>' ?></td>
      <td style="white-space:nowrap">
        <a href="?editar=<?= $uc['id'] ?>" class="btn bn bsm">✏️ Editar</a>
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $uc['id'] ?>">
          <button type="submit" class="btn bsm <?= $uc['ativo']?'bd':'bs' ?>"><?= $uc['ativo']?'Desativar':'Ativar' ?></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem UCs. Crie a primeira acima.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
