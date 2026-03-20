<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['aluno']);
$db  = getDB();
$uid = uid();

$notas = $db->prepare('SELECT n.*,uc.nome as uc_nome,uc.codigo as uc_codigo,p.ano_letivo,p.epoca,c.nome as curso FROM notas n JOIN pautas p ON n.pauta_id=p.id JOIN unidades_curriculares uc ON p.uc_id=uc.id JOIN plano_estudos pe ON pe.uc_id=uc.id JOIN cursos c ON pe.curso_id=c.id WHERE n.utilizador_id=? GROUP BY n.id ORDER BY p.ano_letivo DESC,p.epoca');
$notas->execute([$uid]); $notas = $notas->fetchAll();

$media = 0; $count = 0;
foreach($notas as $n) { if($n['nota_final']!==null){$media+=$n['nota_final'];$count++;} }
$media = $count ? round($media/$count,1) : null;

$pageTitle = 'Minhas Notas';
include __DIR__ . '/../../includes/header.php';
?>

<div class="stats">
  <div class="stat"><div class="stat-n"><?= count($notas) ?></div><div class="stat-l">Total de Notas</div></div>
  <div class="stat"><div class="stat-n" style="color:<?= $media >= 10 ? '#16a34a' : '#dc2626' ?>"><?= $media !== null ? number_format($media,1) : '—' ?></div><div class="stat-l">Média Geral</div></div>
  <div class="stat"><div class="stat-n"><?= count(array_filter($notas,fn($n)=>$n['nota_final']>=10)) ?></div><div class="stat-l">Aprovadas (≥10)</div></div>
  <div class="stat"><div class="stat-n"><?= count(array_filter($notas,fn($n)=>$n['nota_final']!==null && $n['nota_final']<10)) ?></div><div class="stat-l">Reprovadas (<10)</div></div>
</div>

<div class="card">
  <div class="card-hd">📊 Histórico de Notas</div>
  <?php if($notas): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Unidade Curricular</th><th>Código</th><th>Ano Letivo</th><th>Época</th><th>Nota Final</th><th>Resultado</th></tr>
    <?php foreach($notas as $n): ?>
    <?php $ap = $n['nota_final'] >= 10; $sem = $n['nota_final'] === null; ?>
    <tr>
      <td><strong><?= e($n['uc_nome']) ?></strong></td>
      <td><code style="font-size:.78rem"><?= e($n['uc_codigo']) ?></code></td>
      <td><?= e($n['ano_letivo']) ?></td>
      <td><?= e($n['epoca']) ?></td>
      <td><strong style="font-size:1.1rem;color:<?= $sem?'#64748b':($ap?'#16a34a':'#dc2626') ?>"><?= $sem ? '—' : number_format($n['nota_final'],1) ?></strong></td>
      <td><?php if($sem) echo '<span style="color:#64748b">Sem nota</span>'; elseif($ap) echo '<span style="color:#16a34a;font-weight:700">✅ Aprovado</span>'; else echo '<span style="color:#dc2626;font-weight:700">❌ Reprovado</span>'; ?></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem notas lançadas ainda.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
