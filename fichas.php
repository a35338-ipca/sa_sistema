<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['gestor','admin']);
$db  = getDB();
$uid = uid();

// Processar decisão
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $acao = $_POST['acao'] ?? '';
    $obs  = trim($_POST['observacoes'] ?? '');
    if ($id && in_array($acao,['aprovada','rejeitada'],true)) {
        $db->prepare('UPDATE fichas_aluno SET estado=?,observacoes=?,validado_por=?,validado_em=NOW() WHERE id=? AND estado="submetida"')
           ->execute([$acao,$obs,$uid,$id]);
        flash('ok','Ficha '.($acao==='aprovada'?'aprovada':'rejeitada').' com sucesso.');
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

$filtro  = $_GET['filtro'] ?? 'submetida';
$filtros = ['submetida'=>'Submetidas','aprovada'=>'Aprovadas','rejeitada'=>'Rejeitadas','rascunho'=>'Rascunhos','todas'=>'Todas'];
$whereSQL = ($filtro==='todas') ? '' : "AND f.estado='$filtro'";

$fichas = $db->query("SELECT f.*,u.nome as aluno,u.email,c.nome as curso FROM fichas_aluno f JOIN utilizadores u ON f.utilizador_id=u.id JOIN cursos c ON f.curso_id=c.id WHERE 1=1 $whereSQL ORDER BY f.atualizado_em DESC")->fetchAll();

$detalhe = null;
if (isset($_GET['id'])) {
    $st = $db->prepare('SELECT f.*,u.nome as aluno,u.email,c.nome as curso,u2.nome as validado_por_nome FROM fichas_aluno f JOIN utilizadores u ON f.utilizador_id=u.id JOIN cursos c ON f.curso_id=c.id LEFT JOIN utilizadores u2 ON f.validado_por=u2.id WHERE f.id=?');
    $st->execute([(int)$_GET['id']]); $detalhe = $st->fetch();
}

$pageTitle = 'Validação de Fichas de Aluno';
include __DIR__ . '/../../includes/header.php';
?>

<?php if($detalhe): ?>
<div style="margin-bottom:1rem"><a href="<?= SITE_URL ?>/pages/gestor/fichas.php" class="btn bn bsm">← Voltar</a></div>
<div class="card" style="border-color:<?= $detalhe['estado']==='submetida'?'#d97706':'#e2e8f0' ?>;border-width:<?= $detalhe['estado']==='submetida'?2:1 ?>px">
  <div class="card-hd">👤 Ficha de: <?= e($detalhe['aluno']) ?> — <?= badgeEstado($detalhe['estado']) ?></div>
  <div class="g2" style="margin-bottom:1.25rem">
    <div>
      <label>Aluno</label><strong><?= e($detalhe['aluno']) ?></strong><br>
      <small style="color:#64748b"><?= e($detalhe['email']) ?></small>
    </div>
    <div><label>Curso Pretendido</label><strong><?= e($detalhe['curso']) ?></strong></div>
    <div><label>Data de Nascimento</label><?= $detalhe['data_nascimento'] ? date('d/m/Y',strtotime($detalhe['data_nascimento'])) : '—' ?></div>
    <div><label>NIF</label><?= e($detalhe['nif']??'—') ?></div>
    <div><label>Telefone</label><?= e($detalhe['telefone']??'—') ?></div>
    <div><label>Morada</label><?= e($detalhe['morada']??'—') ?></div>
  </div>
  <?php if($detalhe['foto']): ?>
  <div style="margin-bottom:1.25rem">
    <label>Fotografia</label>
    <img src="<?= UPLOAD_URL.e($detalhe['foto']) ?>" alt="Foto" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid #e2e8f0;display:block;margin-top:.4rem">
  </div>
  <?php endif; ?>
  <?php if($detalhe['observacoes']): ?>
  <div class="alert a-in"><strong>Observações anteriores:</strong> <?= e($detalhe['observacoes']) ?></div>
  <?php endif; ?>
  <?php if($detalhe['estado']==='submetida'): ?>
  <form method="post">
    <input type="hidden" name="id" value="<?= $detalhe['id'] ?>">
    <div class="fg"><label>Observações / Justificação</label><textarea name="observacoes" placeholder="Motivo da aprovação ou rejeição (opcional)..."></textarea></div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
      <button type="submit" name="acao" value="aprovada"  class="btn bs">✅ Aprovar Ficha</button>
      <button type="submit" name="acao" value="rejeitada" class="btn bd" onclick="return confirm('Rejeitar esta ficha?')">❌ Rejeitar</button>
    </div>
  </form>
  <?php elseif($detalhe['validado_por_nome']): ?>
  <div class="alert a-ok">Decisão tomada por <strong><?= e($detalhe['validado_por_nome']) ?></strong> em <?= date('d/m/Y H:i',strtotime($detalhe['validado_em'])) ?></div>
  <?php endif; ?>
</div>
<?php else: ?>

<div class="card">
  <div class="card-hd">📋 Fichas de Aluno</div>
  <div style="display:flex;gap:.5rem;margin-bottom:1.1rem;flex-wrap:wrap">
    <?php foreach($filtros as $k=>$v): ?>
    <a href="?filtro=<?= $k ?>" class="btn bsm <?= $filtro===$k?'bp':'bn' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>
  <?php if($fichas): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Aluno</th><th>Email</th><th>Curso</th><th>Estado</th><th>Última Atualização</th><th>Foto</th><th>Ação</th></tr>
    <?php foreach($fichas as $f): ?>
    <tr>
      <td><strong><?= e($f['aluno']) ?></strong></td>
      <td style="font-size:.8rem;color:#64748b"><?= e($f['email']) ?></td>
      <td><?= e($f['curso']) ?></td>
      <td><?= badgeEstado($f['estado']) ?></td>
      <td><?= date('d/m/Y H:i',strtotime($f['atualizado_em'])) ?></td>
      <td><?= $f['foto'] ? '📷' : '—' ?></td>
      <td><a href="?id=<?= $f['id'] ?>&filtro=<?= $filtro ?>" class="btn <?= $f['estado']==='submetida'?'bw':'bn' ?> bsm">👁 Ver</a></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem fichas <?= strtolower($filtros[$filtro]) ?>.</p><?php endif; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
