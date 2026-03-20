<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['gestor','admin']);
$db = getDB();

$nCursos  = $db->query('SELECT COUNT(*) FROM cursos WHERE ativo=1')->fetchColumn();
$nUCs     = $db->query('SELECT COUNT(*) FROM unidades_curriculares WHERE ativo=1')->fetchColumn();
$nFichas  = $db->query('SELECT COUNT(*) FROM fichas_aluno WHERE estado="submetida"')->fetchColumn();
$nAlunos  = $db->query('SELECT COUNT(*) FROM utilizadores WHERE perfil="aluno"')->fetchColumn();

$fichasPend = $db->query('SELECT f.*,u.nome as aluno,c.nome as curso FROM fichas_aluno f JOIN utilizadores u ON f.utilizador_id=u.id JOIN cursos c ON f.curso_id=c.id WHERE f.estado="submetida" ORDER BY f.atualizado_em ASC LIMIT 8')->fetchAll();

$pageTitle = 'Dashboard — Gestão Pedagógica';
include __DIR__ . '/../../includes/header.php';
?>
<div class="stats">
  <div class="stat"><div class="stat-n"><?= $nCursos ?></div><div class="stat-l">Cursos Ativos</div></div>
  <div class="stat"><div class="stat-n"><?= $nUCs ?></div><div class="stat-l">UCs Ativas</div></div>
  <div class="stat"><div class="stat-n" style="color:#d97706"><?= $nFichas ?></div><div class="stat-l">Fichas Pendentes</div></div>
  <div class="stat"><div class="stat-n"><?= $nAlunos ?></div><div class="stat-l">Alunos Registados</div></div>
</div>

<?php if($nFichas > 0): ?>
<div class="alert a-wa">⚠️ Existem <strong><?= $nFichas ?></strong> ficha(s) de aluno para validar. <a href="<?= SITE_URL ?>/pages/gestor/fichas.php" style="font-weight:700">Validar agora →</a></div>
<?php endif; ?>

<div class="card">
  <div class="card-hd">📋 Fichas Aguardando Validação</div>
  <?php if($fichasPend): ?>
  <div class="tbl-wrap"><table>
    <tr><th>Aluno</th><th>Curso Pretendido</th><th>Submetido em</th><th>Ação</th></tr>
    <?php foreach($fichasPend as $f): ?>
    <tr>
      <td><strong><?= e($f['aluno']) ?></strong></td>
      <td><?= e($f['curso']) ?></td>
      <td><?= date('d/m/Y H:i', strtotime($f['atualizado_em'])) ?></td>
      <td><a href="<?= SITE_URL ?>/pages/gestor/fichas.php?id=<?= $f['id'] ?>" class="btn bp bsm">Validar</a></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php else: ?><p class="empty">Sem fichas pendentes de validação. ✅</p><?php endif; ?>
</div>

<div class="card">
  <div class="card-hd">🚀 Ações Rápidas</div>
  <div style="display:flex;gap:.75rem;flex-wrap:wrap">
    <a href="<?= SITE_URL ?>/pages/gestor/cursos.php"  class="btn bp">🎓 Gerir Cursos</a>
    <a href="<?= SITE_URL ?>/pages/gestor/ucs.php"     class="btn bp">📚 Gerir UCs</a>
    <a href="<?= SITE_URL ?>/pages/gestor/plano.php"   class="btn bp">🗂 Plano de Estudos</a>
    <a href="<?= SITE_URL ?>/pages/gestor/fichas.php"  class="btn bs">👤 Validar Fichas</a>
    <a href="<?= SITE_URL ?>/pages/funcionario/matriculas.php" class="btn bn">📝 Matrículas</a>
    <a href="<?= SITE_URL ?>/pages/funcionario/pautas.php"     class="btn bn">📊 Pautas</a>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
