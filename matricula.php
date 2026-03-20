<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['aluno']);
$db  = getDB();
$uid = uid();

// Verificar se tem ficha aprovada
$ficha = $db->prepare('SELECT estado,curso_id FROM fichas_aluno WHERE utilizador_id=? LIMIT 1');
$ficha->execute([$uid]); $ficha = $ficha->fetch();

$cursos = $db->query('SELECT id,nome,codigo FROM cursos WHERE ativo=1 ORDER BY nome')->fetchAll();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!$ficha || $ficha['estado']!=='aprovada') {
        flash('err','Precisa de ter a ficha de aluno aprovada para pedir matrícula.');
    } else {
        $curso_id  = (int)($_POST['curso_id'] ?? 0);
        $ano_letivo= trim($_POST['ano_letivo'] ?? '');
        if (!$curso_id || !$ano_letivo) { flash('err','Preencha todos os campos.'); }
        elseif (!preg_match('/^\d{4}\/\d{4}$/', $ano_letivo)) { flash('err','Formato do ano letivo inválido (ex: 2024/2025).'); }
        else {
            $db->prepare('INSERT INTO matriculas(utilizador_id,curso_id,ano_letivo) VALUES(?,?,?)')->execute([$uid,$curso_id,$ano_letivo]);
            flash('ok','Pedido de matrícula enviado! Aguarde a aprovação.');
        }
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

$mats = $db->prepare('SELECT m.*,c.nome as curso,u2.nome as decidido_por_nome FROM matriculas m JOIN cursos c ON m.curso_id=c.id LEFT JOIN utilizadores u2 ON m.decidido_por=u2.id WHERE m.utilizador_id=? ORDER BY m.criado_em DESC');
$mats->execute([$uid]); $mats = $mats->fetchAll();

$pageTitle = 'Pedidos de Matrícula';
include __DIR__ . '/../../includes/header.php';
?>

<?php if (!$ficha): ?>
<div class="alert a-wa">⚠️ Não tem ficha de aluno. <a href="<?= SITE_URL ?>/pages/aluno/ficha.php" style="font-weight:700">Criar ficha →</a></div>
<?php elseif($ficha['estado']!=='aprovada'): ?>
<div class="alert a-wa">⚠️ A sua ficha precisa de estar aprovada para pedir matrícula. Estado atual: <?= badgeEstado($ficha['estado']) ?></div>
<?php else: ?>
<div class="card">
  <div class="card-hd">📝 Novo Pedido de Matrícula</div>
  <form method="post">
    <div class="g2">
      <div class="fg">
        <label>Curso *</label>
        <select name="curso_id" required>
          <option value="">-- Selecionar --</option>
          <?php foreach($cursos as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($ficha['curso_id']==$c['id'])?'selected':'' ?>>[<?= e($c['codigo']) ?>] <?= e($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="fg">
        <label>Ano Letivo * (ex: 2024/2025)</label>
        <input type="text" name="ano_letivo" placeholder="2024/2025" pattern="\d{4}/\d{4}" value="<?= date('Y').'/'.(date('Y')+1) ?>" required>
      </div>
    </div>
    <button type="submit" class="btn bp">📤 Enviar Pedido</button>
  </form>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-hd">📋 Histórico de Matrículas</div>
  <?php if($mats): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Curso</th><th>Ano Letivo</th><th>Estado</th><th>Decidido por</th><th>Data Decisão</th><th>Observações</th></tr>
    <?php foreach($mats as $m): ?>
    <tr>
      <td><?= e($m['curso']) ?></td>
      <td><?= e($m['ano_letivo']) ?></td>
      <td><?= badgeEstado($m['estado']) ?></td>
      <td><?= $m['decidido_por_nome'] ? e($m['decidido_por_nome']) : '—' ?></td>
      <td><?= $m['decidido_em'] ? date('d/m/Y H:i', strtotime($m['decidido_em'])) : '—' ?></td>
      <td style="max-width:200px;font-size:.8rem;color:#64748b"><?= e($m['observacoes']??'') ?></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem pedidos de matrícula.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
