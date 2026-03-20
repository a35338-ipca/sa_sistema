<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['gestor','admin']);
$db  = getDB();
$uid = uid();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';

    if ($action==='criar' || $action==='editar') {
        $nome  = trim($_POST['nome'] ?? '');
        $codigo= strtoupper(trim($_POST['codigo'] ?? ''));
        $desc  = trim($_POST['descricao'] ?? '');
        $anos  = (int)($_POST['duracao_anos'] ?? 3);
        $id    = (int)($_POST['id'] ?? 0);
        if (!$nome || !$codigo) { flash('err','Nome e código são obrigatórios.'); }
        else {
            try {
                if ($action==='criar') {
                    $db->prepare('INSERT INTO cursos(nome,codigo,descricao,duracao_anos,criado_por) VALUES(?,?,?,?,?)')->execute([$nome,$codigo,$desc,$anos,$uid]);
                    flash('ok','Curso criado com sucesso.');
                } else {
                    $db->prepare('UPDATE cursos SET nome=?,codigo=?,descricao=?,duracao_anos=? WHERE id=?')->execute([$nome,$codigo,$desc,$anos,$id]);
                    flash('ok','Curso atualizado.');
                }
            } catch(PDOException $e) { flash('err','Código de curso já existe.'); }
        }
    } elseif ($action==='toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('UPDATE cursos SET ativo=NOT ativo WHERE id=?')->execute([$id]);
        flash('inf','Estado do curso alterado.');
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $db->prepare('SELECT * FROM cursos WHERE id=?'); $st->execute([(int)$_GET['editar']]); $editar = $st->fetch();
}

$cursos = $db->query('SELECT c.*,u.nome as criador,(SELECT COUNT(*) FROM plano_estudos pe WHERE pe.curso_id=c.id) as num_ucs FROM cursos c JOIN utilizadores u ON c.criado_por=u.id ORDER BY c.nome')->fetchAll();

$pageTitle = 'Gestão de Cursos';
include __DIR__ . '/../../includes/header.php';
?>

<div class="card">
  <div class="card-hd"><?= $editar ? '✏️ Editar Curso' : '➕ Novo Curso' ?></div>
  <form method="post">
    <input type="hidden" name="action" value="<?= $editar ? 'editar' : 'criar' ?>">
    <?php if($editar): ?><input type="hidden" name="id" value="<?= $editar['id'] ?>"><?php endif; ?>
    <div class="g2">
      <div class="fg"><label>Nome do Curso *</label><input type="text" name="nome" value="<?= e($editar['nome']??'') ?>" placeholder="Ex: Licenciatura em Informática" required></div>
      <div class="fg"><label>Código *</label><input type="text" name="codigo" value="<?= e($editar['codigo']??'') ?>" placeholder="Ex: LEI" maxlength="20" required></div>
      <div class="fg"><label>Duração (anos)</label><select name="duracao_anos"><?php for($i=1;$i<=5;$i++): ?><option value="<?= $i ?>" <?= (($editar['duracao_anos']??3)==$i)?'selected':'' ?>><?= $i ?> anos</option><?php endfor; ?></select></div>
    </div>
    <div class="fg"><label>Descrição</label><textarea name="descricao"><?= e($editar['descricao']??'') ?></textarea></div>
    <div style="display:flex;gap:.75rem">
      <button type="submit" class="btn bp">💾 <?= $editar ? 'Atualizar' : 'Criar Curso' ?></button>
      <?php if($editar): ?><a href="<?= SITE_URL ?>/pages/gestor/cursos.php" class="btn bn">Cancelar</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-hd">🎓 Cursos (<?= count($cursos) ?>)</div>
  <?php if($cursos): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Nome</th><th>Código</th><th>Duração</th><th>UCs no Plano</th><th>Estado</th><th>Criado por</th><th>Ações</th></tr>
    <?php foreach($cursos as $c): ?>
    <tr style="opacity:<?= $c['ativo']?1:.55 ?>">
      <td><strong><?= e($c['nome']) ?></strong><?php if($c['descricao']): ?><br><small style="color:#64748b;font-size:.78rem"><?= e(substr($c['descricao'],0,60)) ?>...</small><?php endif; ?></td>
      <td><code><?= e($c['codigo']) ?></code></td>
      <td><?= $c['duracao_anos'] ?> anos</td>
      <td><strong><?= $c['num_ucs'] ?></strong></td>
      <td><?= $c['ativo'] ? '<span style="color:#16a34a;font-weight:700">● Ativo</span>' : '<span style="color:#94a3b8">● Inativo</span>' ?></td>
      <td style="font-size:.82rem;color:#64748b"><?= e($c['criador']) ?></td>
      <td style="white-space:nowrap">
        <a href="?editar=<?= $c['id'] ?>" class="btn bn bsm">✏️ Editar</a>
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= $c['id'] ?>">
          <button type="submit" class="btn bsm <?= $c['ativo']?'bd':'bs' ?>"><?= $c['ativo']?'Desativar':'Ativar' ?></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem cursos. Crie o primeiro acima.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
