<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['funcionario','gestor','admin']);
$db  = getDB();
$uid = uid();

// Processar decisão
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $acao = $_POST['acao'] ?? '';
    $obs  = trim($_POST['observacoes'] ?? '');
    if ($id && in_array($acao,['aprovada','rejeitada'],true)) {
        $db->prepare('UPDATE matriculas SET estado=?,observacoes=?,decidido_por=?,decidido_em=NOW() WHERE id=? AND estado="pendente"')
           ->execute([$acao,$obs,$uid,$id]);
        flash('ok','Matrícula '.($acao==='aprovada'?'aprovada':'rejeitada').' com sucesso.');
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

$filtro  = $_GET['filtro'] ?? 'pendente';
$filtros = ['pendente'=>'Pendentes','aprovada'=>'Aprovadas','rejeitada'=>'Rejeitadas','todas'=>'Todas'];
$whereSQL = ($filtro==='todas') ? '' : "WHERE m.estado='$filtro'";

$mats = $db->query("SELECT m.*,c.nome as curso,u.nome as aluno,u2.nome as dec_nome
    FROM matriculas m
    JOIN cursos c ON m.curso_id=c.id
    JOIN utilizadores u ON m.utilizador_id=u.id
    LEFT JOIN utilizadores u2 ON m.decidido_por=u2.id
    $whereSQL ORDER BY m.criado_em DESC")->fetchAll();

// Detalhe para decidir
$detalhe = null;
if (isset($_GET['id'])) {
    $st = $db->prepare('SELECT m.*,c.nome as curso,u.nome as aluno,u.email FROM matriculas m JOIN cursos c ON m.curso_id=c.id JOIN utilizadores u ON m.utilizador_id=u.id WHERE m.id=?');
    $st->execute([(int)$_GET['id']]); $detalhe = $st->fetch();
}

$pageTitle = 'Gestão de Matrículas';
include __DIR__ . '/../../includes/header.php';
?>

<?php if($detalhe && $detalhe['estado']==='pendente'): ?>
<div class="card" style="border-color:#d97706;border-width:2px">
  <div class="card-hd">⚖️ Decidir sobre Matrícula #<?= $detalhe['id'] ?></div>
  <div class="g2" style="margin-bottom:1.1rem">
    <div><label style="margin-bottom:.2rem">Aluno</label><strong><?= e($detalhe['aluno']) ?></strong><br><small style="color:#64748b"><?= e($detalhe['email']) ?></small></div>
    <div><label style="margin-bottom:.2rem">Curso</label><strong><?= e($detalhe['curso']) ?></strong></div>
    <div><label style="margin-bottom:.2rem">Ano Letivo</label><?= e($detalhe['ano_letivo']) ?></div>
    <div><label style="margin-bottom:.2rem">Submetido em</label><?= date('d/m/Y H:i', strtotime($detalhe['criado_em'])) ?></div>
  </div>
  <form method="post">
    <input type="hidden" name="id" value="<?= $detalhe['id'] ?>">
    <div class="fg"><label>Observações / Justificação</label><textarea name="observacoes" placeholder="Motivo da aprovação ou rejeição (opcional)..."></textarea></div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
      <button type="submit" name="acao" value="aprovada"  class="btn bs">✅ Aprovar Matrícula</button>
      <button type="submit" name="acao" value="rejeitada" class="btn bd" onclick="return confirm('Tem a certeza que quer rejeitar esta matrícula?')">❌ Rejeitar</button>
      <a href="<?= SITE_URL ?>/pages/funcionario/matriculas.php" class="btn bn">Cancelar</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-hd">📝 Pedidos de Matrícula</div>
  <div style="display:flex;gap:.5rem;margin-bottom:1.1rem;flex-wrap:wrap">
    <?php foreach($filtros as $k=>$v): ?>
    <a href="?filtro=<?= $k ?>" class="btn bsm <?= $filtro===$k?'bp':'bn' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div>
  <?php if($mats): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Aluno</th><th>Curso</th><th>Ano Letivo</th><th>Estado</th><th>Decidido por</th><th>Data Decisão</th><th>Observações</th><th>Ação</th></tr>
    <?php foreach($mats as $m): ?>
    <tr>
      <td><strong><?= e($m['aluno']) ?></strong></td>
      <td><?= e($m['curso']) ?></td>
      <td><?= e($m['ano_letivo']) ?></td>
      <td><?= badgeEstado($m['estado']) ?></td>
      <td><?= $m['dec_nome'] ? e($m['dec_nome']) : '—' ?></td>
      <td><?= $m['decidido_em'] ? date('d/m/Y H:i',strtotime($m['decidido_em'])) : '—' ?></td>
      <td style="max-width:200px;font-size:.8rem;color:#64748b"><?= e($m['observacoes']??'—') ?></td>
      <td><?php if($m['estado']==='pendente'): ?>
        <a href="?id=<?= $m['id'] ?>&filtro=<?= $filtro ?>" class="btn bw bsm">⚖️ Decidir</a>
      <?php else: echo '<span style="color:#94a3b8">—</span>'; endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem matrículas <?= strtolower($filtros[$filtro]) ?>.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
