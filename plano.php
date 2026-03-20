<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['gestor','admin']);
$db  = getDB();
$uid = uid();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    if ($action==='adicionar') {
        $curso_id = (int)($_POST['curso_id'] ?? 0);
        $uc_id    = (int)($_POST['uc_id'] ?? 0);
        $ano      = (int)($_POST['ano'] ?? 1);
        $semestre = (int)($_POST['semestre'] ?? 1);
        if (!$curso_id || !$uc_id || !in_array($ano,[1,2,3,4,5]) || !in_array($semestre,[1,2])) {
            flash('err','Preencha todos os campos corretamente.');
        } else {
            try {
                $db->prepare('INSERT INTO plano_estudos(curso_id,uc_id,ano,semestre) VALUES(?,?,?,?)')->execute([$curso_id,$uc_id,$ano,$semestre]);
                flash('ok','UC adicionada ao plano de estudos.');
            } catch(PDOException $e) { flash('err','Esta UC já existe neste curso/ano/semestre.'); }
        }
    } elseif ($action==='remover') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM plano_estudos WHERE id=?')->execute([$id]);
        flash('inf','UC removida do plano.');
    }
    header('Location: '.$_SERVER['PHP_SELF'].($_GET['curso']?'?curso='.(int)$_GET['curso']:'')); exit;
}

$cursos = $db->query('SELECT id,nome,codigo FROM cursos WHERE ativo=1 ORDER BY nome')->fetchAll();
$ucs    = $db->query('SELECT id,nome,codigo FROM unidades_curriculares WHERE ativo=1 ORDER BY nome')->fetchAll();

$curso_sel = isset($_GET['curso']) ? (int)$_GET['curso'] : ($cursos[0]['id'] ?? 0);
$plano = [];
if ($curso_sel) {
    $st = $db->prepare('SELECT pe.*,uc.nome as uc_nome,uc.codigo as uc_cod,uc.creditos FROM plano_estudos pe JOIN unidades_curriculares uc ON pe.uc_id=uc.id WHERE pe.curso_id=? ORDER BY pe.ano,pe.semestre,uc.nome');
    $st->execute([$curso_sel]); $plano = $st->fetchAll();
}
$cursoInfo = $curso_sel ? array_values(array_filter($cursos,fn($c)=>$c['id']===$curso_sel))[0] ?? null : null;

$pageTitle = 'Plano de Estudos';
include __DIR__ . '/../../includes/header.php';
?>

<div class="card">
  <div class="card-hd">🎓 Selecionar Curso</div>
  <form method="get" style="display:flex;gap:.75rem;align-items:flex-end">
    <div class="fg" style="flex:1;margin:0">
      <label>Curso</label>
      <select name="curso" onchange="this.form.submit()">
        <?php foreach($cursos as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $c['id']===$curso_sel?'selected':'' ?>>[<?= e($c['codigo']) ?>] <?= e($c['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>

<?php if($cursoInfo): ?>
<div class="card">
  <div class="card-hd">➕ Adicionar UC ao Plano — <?= e($cursoInfo['nome']) ?></div>
  <form method="post">
    <input type="hidden" name="action" value="adicionar">
    <input type="hidden" name="curso_id" value="<?= $curso_sel ?>">
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
        <label>Ano *</label>
        <select name="ano" required>
          <?php for($i=1;$i<=5;$i++): ?><option value="<?= $i ?>">Ano <?= $i ?></option><?php endfor; ?>
        </select>
      </div>
      <div class="fg">
        <label>Semestre *</label>
        <select name="semestre" required>
          <option value="1">1.º Semestre</option>
          <option value="2">2.º Semestre</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn bp">➕ Adicionar ao Plano</button>
  </form>
</div>

<div class="card">
  <div class="card-hd">🗂 Plano de Estudos — <?= e($cursoInfo['nome']) ?> (<?= count($plano) ?> UCs)</div>
  <?php if($plano):
    $agrupado = [];
    foreach($plano as $p) $agrupado[$p['ano']][$p['semestre']][] = $p;
    ksort($agrupado);
    foreach($agrupado as $ano => $sems):
      ksort($sems);
      foreach($sems as $sem => $ucsPlano):
  ?>
  <div style="margin-bottom:1.5rem">
    <div style="font-weight:800;font-size:.9rem;color:var(--p);margin-bottom:.5rem;padding:.4rem .8rem;background:var(--p10);border-radius:6px;display:inline-block">
      📅 <?= $ano ?>º Ano — <?= $sem ?>º Semestre
    </div>
    <div class="tbl-wrap"><table>
      <tr><th>UC</th><th>Código</th><th>Créditos</th><th>Ação</th></tr>
      <?php foreach($ucsPlano as $u): ?>
      <tr>
        <td><strong><?= e($u['uc_nome']) ?></strong></td>
        <td><code><?= e($u['uc_cod']) ?></code></td>
        <td><?= $u['creditos'] ?> ECTS</td>
        <td>
          <form method="post" style="display:inline" onsubmit="return confirm('Remover esta UC do plano?')">
            <input type="hidden" name="action" value="remover">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <button type="submit" class="btn bd bsm">🗑 Remover</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table></div>
  </div>
  <?php endforeach; endforeach; ?>
  <?php else: ?><p class="empty">Sem UCs no plano deste curso. Adicione acima.</p><?php endif; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
