<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool {
    return isset($_SESSION['uid']);
}

function uid(): int {
    return (int)($_SESSION['uid'] ?? 0);
}

function perfil(): string {
    return $_SESSION['perfil'] ?? '';
}

function nomeUser(): string {
    return $_SESSION['nome'] ?? '';
}

function emailUser(): string {
    return $_SESSION['email'] ?? '';
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return ['id'=>uid(),'nome'=>nomeUser(),'email'=>emailUser(),'perfil'=>perfil()];
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: '.SITE_URL.'/academicologin.php');
        exit;
    }
}

function requirePerfil(array $perfis): void {
    requireLogin();
    if (!in_array(perfil(), $perfis, true)) {
        header('Location: '.SITE_URL.'/acesso_negado.php');
        exit;
    }
}

function doLogin(string $email, string $pass): bool {
    $db = getDB();
    $st = $db->prepare('SELECT id,nome,email,password_hash,perfil,ativo FROM utilizadores WHERE email=? LIMIT 1');
    $st->execute([strtolower(trim($email))]);
    $u = $st->fetch();
    if (!$u || !$u['ativo'] || !password_verify($pass, $u['password_hash'])) return false;
    session_regenerate_id(true);
    $_SESSION['uid']    = $u['id'];
    $_SESSION['nome']   = $u['nome'];
    $_SESSION['email']  = $u['email'];
    $_SESSION['perfil'] = $u['perfil'];
    return true;
}

function doLogout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);
    }
    session_destroy();
}

function dashboardUrl(): string {
    $map = ['aluno'=>'aluno','funcionario'=>'funcionario','gestor'=>'gestor','admin'=>'admin'];
    $p   = perfil();
    return SITE_URL.'/pages/'.($map[$p] ?? 'aluno').'/dashboard.php';
}

/* Utilitários */
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
}

function flash(string $key, string $msg = null): ?string {
    if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return null; }
    $v = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $v;
}

function badgeEstado(string $estado): string {
    $map = [
        'rascunho'   => ['#64748b','Rascunho'],
        'submetida'  => ['#2563eb','Submetida'],
        'aprovada'   => ['#16a34a','Aprovada'],
        'rejeitada'  => ['#dc2626','Rejeitada'],
        'pendente'   => ['#d97706','Pendente'],
    ];
    [$c,$l] = $map[$estado] ?? ['#64748b',$estado];
    return "<span style='background:$c;color:#fff;padding:2px 10px;border-radius:20px;font-size:.78rem;font-weight:700'>$l</span>";
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}

function verifyCsrf(): void {
    $tok = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $tok)) {
        die('CSRF token inválido.');
    }
}
