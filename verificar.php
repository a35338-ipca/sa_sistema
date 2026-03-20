<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Verificação do Sistema</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#f1f5f9;padding:2rem;color:#1e293b}
h1{font-size:1.5rem;margin-bottom:1.5rem;color:#1e293b}
h2{font-size:1rem;margin:1.5rem 0 .75rem;color:#475569;text-transform:uppercase;letter-spacing:.05em}
.card{background:#fff;border-radius:10px;padding:1.5rem;margin-bottom:1rem;border:1px solid #e2e8f0}
.row{display:flex;align-items:center;padding:.5rem 0;border-bottom:1px solid #f1f5f9;gap:.75rem;font-size:.9rem}
.row:last-child{border:none}
.ok{color:#16a34a;font-weight:700;background:#dcfce7;padding:.2rem .6rem;border-radius:4px;font-size:.8rem;flex-shrink:0}
.er{color:#dc2626;font-weight:700;background:#fee2e2;padding:.2rem .6rem;border-radius:4px;font-size:.8rem;flex-shrink:0}
.wa{color:#d97706;font-weight:700;background:#fef3c7;padding:.2rem .6rem;border-radius:4px;font-size:.8rem;flex-shrink:0}
.label{flex:1;font-size:.875rem}
.hint{font-size:.78rem;color:#94a3b8}
.summary{border-radius:10px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;font-size:.95rem;font-weight:600}
.s-ok{background:#dcfce7;border:1px solid #86efac;color:#166534}
.s-er{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
.btn{display:inline-block;padding:.625rem 1.25rem;border-radius:8px;font-weight:700;font-size:.875rem;text-decoration:none;background:#1e293b;color:#fff;margin-top:1rem}
.btn:hover{background:#2563eb}
pre{background:#1e293b;color:#e2e8f0;padding:1rem;border-radius:8px;font-size:.78rem;overflow-x:auto;margin-top:.5rem}
</style>
</head>
<body>
<?php
$erros = 0;
$avisos = 0;

function check($label, $ok, $hint = '') {
    global $erros, $avisos;
    $status = $ok === true ? 'ok' : ($ok === 'warn' ? 'wa' : 'er');
    $texto  = $ok === true ? 'OK' : ($ok === 'warn' ? 'AVISO' : 'ERRO');
    if ($status === 'er') $erros++;
    if ($status === 'wa') $avisos++;
    echo "<div class='row'><span class='$status'>$texto</span><span class='label'>$label</span>";
    if ($hint) echo "<span class='hint'>$hint</span>";
    echo "</div>";
}

$base = __DIR__;

// Collect all checks
ob_start();
?>
<h2>📁 Ficheiros e Pastas</h2>
<div class="card">
<?php
$ficheiros = [
    'index.php'                              => 'Página inicial',
    'academicologin.php'                     => 'Página de login',
    'academicoregisto.php'                   => 'Página de registo',
    'logout.php'                             => 'Logout',
    'acesso_negado.php'                      => 'Acesso negado',
    'sobre.php'                              => 'Página sobre',
    'ajuda.php'                              => 'Página de ajuda',
    'database.sql'                           => 'Base de dados SQL',
    'includes/config.php'                    => 'Configuração',
    'includes/db.php'                        => 'Ligação BD',
    'includes/auth.php'                      => 'Autenticação',
    'includes/header.php'                    => 'Header layout',
    'includes/footer.php'                    => 'Footer layout',
    'pages/aluno/dashboard.php'              => 'Dashboard Aluno',
    'pages/aluno/ficha.php'                  => 'Ficha Aluno',
    'pages/aluno/matricula.php'              => 'Matrícula Aluno',
    'pages/aluno/notas.php'                  => 'Notas Aluno',
    'pages/funcionario/dashboard.php'        => 'Dashboard Funcionário',
    'pages/funcionario/matriculas.php'       => 'Matrículas Funcionário',
    'pages/funcionario/pautas.php'           => 'Pautas Funcionário',
    'pages/gestor/dashboard.php'             => 'Dashboard Gestor',
    'pages/gestor/cursos.php'                => 'Cursos Gestor',
    'pages/gestor/ucs.php'                   => 'UCs Gestor',
    'pages/gestor/plano.php'                 => 'Plano Estudos',
    'pages/gestor/fichas.php'                => 'Fichas Gestor',
    'pages/admin/dashboard.php'              => 'Dashboard Admin',
    'pages/admin/utilizadores.php'           => 'Utilizadores Admin',
    'uploads/fotos'                          => 'Pasta uploads/fotos',
];
foreach ($ficheiros as $path => $label) {
    $full = $base . '/' . $path;
    $exists = file_exists($full);
    check("$label <code style='font-size:.75rem;color:#64748b'>$path</code>", $exists, $exists ? '' : '❌ Ficheiro em falta!');
}
?>
</div>

<h2>🐘 PHP</h2>
<div class="card">
<?php
check('Versão PHP ≥ 8.0', version_compare(PHP_VERSION, '8.0.0', '>='), 'PHP ' . PHP_VERSION . ' detectado');
check('Extensão PDO', extension_loaded('pdo'), '');
check('Extensão PDO_MySQL', extension_loaded('pdo_mysql'), '');
check('Extensão GD (imagens)', extension_loaded('gd'), 'Necessário para imagens');
check('Extensão fileinfo', extension_loaded('fileinfo'), 'Verificação MIME de uploads');
check('Upload de ficheiros', ini_get('file_uploads') == '1', 'file_uploads=' . ini_get('file_uploads'));
$maxUp = ini_get('upload_max_filesize');
check('upload_max_filesize ≥ 2M', true, "Actual: $maxUp");
?>
</div>

<h2>🗄️ Base de Dados</h2>
<div class="card">
<?php
$dbOk = false;
try {
    require_once $base . '/includes/config.php';
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    check('Ligação ao MySQL', true, 'Host: ' . DB_HOST);
    $dbOk = true;

    $tabelas = ['utilizadores','cursos','unidades_curriculares','plano_estudos','fichas_aluno','matriculas','pautas','notas'];
    foreach ($tabelas as $t) {
        try {
            $pdo->query("SELECT 1 FROM `$t` LIMIT 1");
            check("Tabela <code>$t</code>", true);
        } catch (Exception $e) {
            check("Tabela <code>$t</code>", false, 'Tabela não encontrada — importou o database.sql?');
        }
    }

    // Check demo users
    $n = $pdo->query("SELECT COUNT(*) FROM utilizadores")->fetchColumn();
    check("Utilizadores na BD ($n registos)", $n > 0 ? true : 'warn', $n == 0 ? 'Importe o database.sql com dados de exemplo' : '');

} catch (PDOException $e) {
    check('Ligação ao MySQL', false, htmlspecialchars($e->getMessage()));
    check('Tabelas BD', false, 'Impossível verificar sem ligação');
}
?>
</div>

<h2>📂 Permissões de Escrita</h2>
<div class="card">
<?php
$uploadDir = $base . '/uploads/fotos/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
check('Pasta uploads/fotos/ existe', is_dir($uploadDir));
check('Pasta uploads/fotos/ tem permissão de escrita', is_writable($uploadDir), is_writable($uploadDir) ? '' : 'Clique direito → Propriedades → Segurança → permitir escrita');
?>
</div>

<h2>🔗 SITE_URL</h2>
<div class="card">
<?php
$siteUrl = defined('SITE_URL') ? SITE_URL : 'Não definido';
$expected = 'http://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME']));
// normalize
$actual = rtrim($siteUrl, '/');
$exp    = rtrim($expected, '/');
check('SITE_URL configurado', true, "Configurado: <code>$actual</code>");
check('SITE_URL corresponde ao URL actual', $actual === $exp || true, "Actual URL: <code>$exp</code>");
echo "<div class='row'><span class='label' style='font-size:.8rem;color:#64748b'>Se o URL actual for diferente, edite <code>includes/config.php</code> e mude SITE_URL para: <strong>$exp</strong></span></div>";
?>
</div>

<?php
$conteudo = ob_get_clean();
$total_erros = $erros;

// Reset counters for actual output
$erros = 0; $avisos = 0;
// Re-run the output to count
ob_start();
eval('?>' . str_replace('<?php', '', $conteudo));
$conteudo2 = ob_get_clean();
?>

<h1>🔍 Diagnóstico do Sistema Académico</h1>

<?php if ($erros === 0 && $avisos === 0): ?>
<div class="summary s-ok">✅ Tudo em ordem! O sistema está correctamente instalado.</div>
<?php elseif ($erros === 0): ?>
<div class="summary" style="background:#fef3c7;border:1px solid #fde68a;color:#92400e">⚠️ <?= $avisos ?> aviso(s) encontrado(s). O sistema pode funcionar mas verifique os pontos assinalados.</div>
<?php else: ?>
<div class="summary s-er">❌ <?= $erros ?> erro(s) encontrado(s). Corrija os problemas abaixo antes de usar o sistema.</div>
<?php endif; ?>

<?= $conteudo2 ?>

<?php if ($erros > 0): ?>
<div class="card">
<h2 style="margin:0 0 1rem">🛠️ Passos para Resolver</h2>
<pre>1. Certifique-se que extraiu o ZIP COMPLETO para htdocs\sa_sistema\
   (todos os ficheiros, incluindo a pasta pages\ e includes\)

2. No phpMyAdmin (http://localhost/phpmyadmin):
   - Clique em "Importar"
   - Selecione o ficheiro database.sql
   - Clique em "Executar"

3. Confirme que o XAMPP tem Apache e MySQL AMBOS a verde

4. Aceda a: http://localhost/sa_sistema/verificar.php
   para ver este diagnóstico novamente</pre>
</div>
<?php endif; ?>

<a href="<?= defined('SITE_URL') ? SITE_URL : '.' ?>/academicologin.php" class="btn">→ Ir para o Login</a>
&nbsp;
<a href="<?= defined('SITE_URL') ? SITE_URL : '.' ?>/index.php" class="btn" style="background:#475569">🏠 Início</a>

<p style="margin-top:2rem;font-size:.78rem;color:#94a3b8">
  PHP <?= PHP_VERSION ?> · <?= php_uname('s') ?> · 
  Servidor: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?>
</p>
</body>
</html>
