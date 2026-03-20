<?php
require_once __DIR__ . '/../../includes/auth.php';
requirePerfil(['aluno']);
$db  = getDB();
$uid = uid();

// Buscar ficha existente
$st = $db->prepare('SELECT f.*,c.nome as curso_nome FROM fichas_aluno f JOIN cursos c ON f.curso_id=c.id WHERE f.utilizador_id=? LIMIT 1');
$st->execute([$uid]); $ficha = $st->fetch();

// Cursos ativos
$cursos = $db->query('SELECT id,nome,codigo FROM cursos WHERE ativo=1 ORDER BY nome')->fetchAll();

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'submeter' && $ficha && $ficha['estado']==='rascunho') {
        $db->prepare('UPDATE fichas_aluno SET estado="submetida",atualizado_em=NOW() WHERE utilizador_id=?')->execute([$uid]);
        flash('ok','Ficha submetida para validação com sucesso!');
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }

    if (in_array($action,['guardar','criar']) && (!$ficha || in_array($ficha['estado'],['rascunho','rejeitada']))) {
        $curso  = (int)($_POST['curso_id'] ?? 0);
        $nasc   = $_POST['data_nascimento'] ?? '';
        $nif    = trim($_POST['nif'] ?? '');
        $tel    = trim($_POST['telefone'] ?? '');
        $mor    = trim($_POST['morada'] ?? '');

        if (!$curso || !$nasc) { flash('err','Curso e data de nascimento são obrigatórios.'); header('Location: '.$_SERVER['PHP_SELF']); exit; }

        // Upload foto
        $fotoNome = $ficha['foto'] ?? null;
        if (!empty($_FILES['foto']['name'])) {
            $tipo = mime_content_type($_FILES['foto']['tmp_name']);
            if (!in_array($tipo, ALLOWED_TYPES)) { flash('err','Formato inválido. Use JPG, PNG ou WebP.'); header('Location: '.$_SERVER['PHP_SELF']); exit; }
            if ($_FILES['foto']['size'] > MAX_FILE_SIZE)  { flash('err','Ficheiro demasiado grande (máx 2MB).'); header('Location: '.$_SERVER['PHP_SELF']); exit; }
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoNome = 'aluno_'.$uid.'_'.time().'.'.$ext;
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR.$fotoNome);
        }

        if ($ficha) {
            $estado = $ficha['estado']==='rejeitada' ? 'rascunho' : $ficha['estado'];
            $db->prepare('UPDATE fichas_aluno SET curso_id=?,data_nascimento=?,nif=?,telefone=?,morada=?,foto=COALESCE(?,foto),estado=?,atualizado_em=NOW() WHERE utilizador_id=?')
               ->execute([$curso,$nasc,$nif,$tel,$mor,$fotoNome,$estado,$uid]);
        } else {
            $db->prepare('INSERT INTO fichas_aluno(utilizador_id,curso_id,data_nascimento,nif,telefone,morada,foto) VALUES(?,?,?,?,?,?,?)')
               ->execute([$uid,$curso,$nasc,$nif,$tel,$mor,$fotoNome]);
        }
        flash('ok','Ficha guardada com sucesso!');
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
}

// Recarregar
$st->execute([$uid]); $ficha = $st->fetch();
$podeEditar = !$ficha || in_array($ficha['estado'],['rascunho','rejeitada']);

$pageTitle = 'Minha Ficha de Aluno';
include __DIR__ . '/../../includes/header.php';
?>

<?php if ($ficha): ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem">
  <div>Estado da ficha: <?= badgeEstado($ficha['estado']) ?></div>
  <?php if($ficha['estado']==='rascunho'): ?>
  <form method="post" onsubmit="return confirm('Submeter a ficha para validação? Não poderá editar depois.')">
    <input type="hidden" name="action" value="submeter">
    <button class="btn bs" type="submit">🚀 Submeter para Validação</button>
  </form>
  <?php endif; ?>
</div>
<?php if($ficha['estado']==='rejeitada' && $ficha['observacoes']): ?>
<div class="alert a-er">❌ <strong>Motivo da rejeição:</strong> <?= e($ficha['observacoes']) ?> — Pode corrigir e guardar novamente.</div>
<?php endif; ?>
<?php if($ficha['estado']==='aprovada'): ?>
<div class="alert a-ok">✅ A sua ficha foi aprovada! Já pode pedir a sua <a href="<?= SITE_URL ?>/pages/aluno/matricula.php" style="font-weight:700">matrícula</a>.</div>
<?php endif; ?>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="<?= $ficha ? 'guardar' : 'criar' ?>">
<div class="card">
  <div class="card-hd">👤 Dados Pessoais</div>
  <?php if(!$podeEditar): ?><div class="alert a-in">ℹ️ A ficha está em estado <?= $ficha['estado'] ?> e não pode ser editada.</div><?php endif; ?>
  <div class="g2">
    <div class="fg">
      <label>Nome Completo</label>
      <input type="text" value="<?= e(nomeUser()) ?>" disabled>
    </div>
    <div class="fg">
      <label>Email</label>
      <input type="email" value="<?= e(emailUser()) ?>" disabled>
    </div>
    <div class="fg">
      <label>Data de Nascimento *</label>
      <input type="date" name="data_nascimento" value="<?= e($ficha['data_nascimento']??'') ?>" <?= !$podeEditar?'disabled':'' ?> required>
    </div>
    <div class="fg">
      <label>NIF</label>
      <input type="text" name="nif" placeholder="123456789" value="<?= e($ficha['nif']??'') ?>" <?= !$podeEditar?'disabled':'' ?>>
    </div>
    <div class="fg">
      <label>Telefone</label>
      <input type="tel" name="telefone" placeholder="9XXXXXXXX" value="<?= e($ficha['telefone']??'') ?>" <?= !$podeEditar?'disabled':'' ?>>
    </div>
    <div class="fg">
      <label>Curso Pretendido *</label>
      <select name="curso_id" <?= !$podeEditar?'disabled':'' ?> required>
        <option value="">-- Selecionar Curso --</option>
        <?php foreach($cursos as $c): ?>
        <option value="<?= $c['id'] ?>" <?= (($ficha['curso_id']??'0')==$c['id'])?'selected':'' ?>>[<?= e($c['codigo']) ?>] <?= e($c['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="fg">
    <label>Morada</label>
    <input type="text" name="morada" placeholder="Rua, número, cidade" value="<?= e($ficha['morada']??'') ?>" <?= !$podeEditar?'disabled':'' ?>>
  </div>
</div>

<div class="card">
  <div class="card-hd">📷 Fotografia</div>
  <?php if($ficha && $ficha['foto']): ?>
  <div style="margin-bottom:1rem">
    <img src="<?= UPLOAD_URL.e($ficha['foto']) ?>" alt="Foto" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid #e2e8f0">
    <p style="color:#64748b;font-size:.82rem;margin-top:.5rem">Foto atual</p>
  </div>
  <?php endif; ?>
  <?php if($podeEditar): ?>
  <div class="fg">
    <label>Carregar Foto (JPG/PNG/WebP, máx. 2MB)</label>
    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
  </div>
  <?php endif; ?>
</div>

<?php if($podeEditar): ?>
<div style="display:flex;gap:.75rem">
  <button type="submit" class="btn bp">💾 Guardar Rascunho</button>
  <a href="<?= SITE_URL ?>/pages/aluno/dashboard.php" class="btn bn">Cancelar</a>
</div>
<?php endif; ?>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
