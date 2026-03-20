<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['funcionario','gestor','admin']);
$db  = getDB();
$uid = uid();

// Criar pauta
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='criar_pauta') {
    $uc_id     = (int)($_POST['uc_id'] ?? 0);
    $ano_letivo= trim($_POST['ano_letivo'] ?? '');
    $epoca     = $_POST['epoca'] ?? 'Normal';
    if (!$uc_id || !$ano_letivo || !in_array($epoca,['Normal','Recurso','Especial'])) {
        flash('err','Preencha todos os campos corretamente.');
    } else {
        try {
            $db->prepare('INSERT INTO pautas(uc_id,ano_letivo,epoca,criado_por) VALUES(?,?,?,?)')->execute([$uc_id,$ano_letivo,$epoca,$uid]);
            $pid = (int)$db->lastInsertId();
            // Adicionar alunos elegíveis (matrícula aprovada para curso que contém esta UC)
            $alunos = $db->prepare('SELECT DISTINCT m.utilizador_id FROM matriculas m JOIN plano_estudos pe ON pe.curso_id=m.curso_id WHERE pe.uc_id=? AND m.estado="aprovada" AND m.ano_letivo=?');
            $alunos->execute([$uc_id,$ano_letivo]);
            $ins = $db->prepare('INSERT IGNORE INTO notas(pauta_id,utilizador_id) VALUES(?,?)');
            foreach($alunos->fetchAll() as $a) $ins->execute([$pid,$a['utilizador_id']]);
            flash('ok','Pauta criada com '.($alunos->rowCount()).' aluno(s) elegíveis.');
        } catch (PDOException $e) { flash('err','Já existe pauta para esta UC/ano/época.'); }
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}

// Guardar notas
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='guardar_notas') {
    $pid = (int)($_POST['pauta_id'] ?? 0);
    $notas = $_POST['notas'] ?? [];
    $ins = $db->prepare('UPDATE notas SET nota_final=?,registado_por=?,registado_em=NOW() WHERE pauta_id=? AND utilizador_id=?');
    foreach($notas as $aluno_id => $nota) {
        $nota_val = ($nota==='' || $nota===null) ? null : min(20, max(0, (float)$nota));
        $ins->execute([$nota_val,$uid,$pid,(int)$aluno_id]);
    }
    flash('ok','Notas guardadas com sucesso.');
    header('Location: ?pauta='.$pid); exit;
}

$ucs    = $db->query('SELECT id,nome,codigo FROM unidades_curriculares WHERE ativo=1 ORDER BY nome')->fetchAll();
$pautas = $db->query('SELECT p.*,uc.nome as uc_nome,uc.codigo as uc_cod,u.nome as criador,COUNT(n.id) as total_alunos,SUM(n.nota_final IS NOT NULL) as com_nota FROM pautas p JOIN unidades_curriculares uc ON p.uc_id=uc.id JOIN utilizadores u ON p.criado_por=u.id LEFT JOIN notas n ON n.pauta_id=p.id GROUP BY p.id ORDER BY p.criado_em DESC')->fetchAll();

// Detalhe de uma pauta
$pauta = null; $linhas = [];
if (isset($_GET['pauta'])) {
    $st = $db->prepare('SELECT p.*,uc.nome as uc_nome,uc.codigo as uc_cod FROM pautas p JOIN unidades_curriculares uc ON p.uc_id=uc.id WHERE p.id=?');
    $st->execute([(int)$_GET['pauta']]); $pauta = $st->fetch();
    if ($pauta) {
        $st2 = $db->prepare('SELECT n.*,u.nome as aluno,u.email FROM notas n JOIN utilizadores u ON n.utilizador_id=u.id WHERE n.pauta_id=? ORDER BY u.nome');
        $st2->execute([$pauta['id']]); $linhas = $st2->fetchAll();
    }
}

$pageTitle = 'Pautas de Avaliação';
include __DIR__ . '/../../includes/header.php';
?>

<?php if($pauta): ?>
<div style="margin-bottom:1rem">
  <a href="<?= SITE_URL ?>/pages/funcionario/pautas.php" class="btn bn bsm">← Voltar às Pautas</a>
</div>
<div class="card">
  <div class="card-hd">📊 Pauta: <?= e($pauta['uc_nome']) ?> [<?= e($pauta['uc_cod']) ?>] — <?= e($pauta['ano_letivo']) ?> — <?= e($pauta['epoca']) ?></div>
  <?php if($linhas): ?>
  <form method="post">
    <input type="hidden" name="action" value="guardar_notas">
    <input type="hidden" name="pauta_id" value="<?= $pauta['id'] ?>">
    <div class="tbl-wrap"><table>
      <tr><th>#</th><th>Aluno</th><th>Email</th><th>Nota Final (0–20)</th><th>Estado</th></tr>
      <?php foreach($linhas as $i=>$l): ?>
      <tr>
        <td style="color:#94a3b8"><?= $i+1 ?></td>
        <td><strong><?= e($l['aluno']) ?></strong></td>
        <td style="font-size:.8rem;color:#64748b"><?= e($l['email']) ?></td>
        <td><input type="number" name="notas[<?= $l['utilizador_id'] ?>]" min="0" max="20" step="0.1" value="<?= $l['nota_final'] ?? '' ?>" placeholder="—" style="width:90px"></td>
        <td><?php
          if($l['nota_final']===null) echo '<span style="color:#94a3b8">Sem nota</span>';
          elseif($l['nota_final']>=10) echo '<span style="color:#16a34a;font-weight:700">✅ Aprovado ('.number_format($l['nota_final'],1).')</span>';
          else echo '<span style="color:#dc2626;font-weight:700">❌ Reprovado ('.number_format($l['nota_final'],1).')</span>';
        ?></td>
      </tr>
      <?php endforeach; ?>
    </table></div>
    <div style="margin-top:1rem"><button type="submit" class="btn bp">💾 Guardar Notas</button></div>
  </form>
  <?php else: ?><p class="empty">Sem alunos elegíveis nesta pauta. Verifique se existem matrículas aprovadas para o ano letivo e UC.</p><?php endif; ?>
</div>
<?php else: ?>

<div class="card">
  <div class="card-hd">➕ Criar Nova Pauta</div>
  <form method="post">
    <input type="hidden" name="action" value="criar_pauta">
    <div class="g3">
      <div class="fg">
        <label>Unidade Curricular *</label>
        <select name="uc_id" required>
          <option value="">-- Selecionar UC --</option>
          <?php foreach($ucs as $uc): ?>
          <option value="<?= $uc['id'] ?>">[<?= e($uc['codigo']) ?>] <?= e($uc['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="fg">
        <label>Ano Letivo * (ex: 2024/2025)</label>
        <input type="text" name="ano_letivo" placeholder="2024/2025" pattern="\d{4}/\d{4}" value="<?= date('Y').'/'.(date('Y')+1) ?>" required>
      </div>
      <div class="fg">
        <label>Época *</label>
        <select name="epoca" required>
          <option value="Normal">Normal</option>
          <option value="Recurso">Recurso</option>
          <option value="Especial">Especial</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn bp">➕ Criar Pauta</button>
  </form>
</div>

<div class="card">
  <div class="card-hd">📋 Pautas Existentes</div>
  <?php if($pautas): ?>
  <div class="tbl-wrap"><table>
    <tr><th>UC</th><th>Ano Letivo</th><th>Época</th><th>Alunos</th><th>Com Nota</th><th>Criado por</th><th>Data</th><th>Ação</th></tr>
    <?php foreach($pautas as $p): ?>
    <tr>
      <td><strong><?= e($p['uc_nome']) ?></strong><br><code style="font-size:.75rem;color:#64748b"><?= e($p['uc_cod']) ?></code></td>
      <td><?= e($p['ano_letivo']) ?></td>
      <td><?= e($p['epoca']) ?></td>
      <td><?= $p['total_alunos'] ?></td>
      <td><strong style="color:<?= $p['com_nota']>0?'#16a34a':'#94a3b8' ?>"><?= $p['com_nota'] ?></strong></td>
      <td><?= e($p['criador']) ?></td>
      <td><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
      <td><a href="?pauta=<?= $p['id'] ?>" class="btn bp bsm">📝 Editar Notas</a></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem pautas criadas.</p><?php endif; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
