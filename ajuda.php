<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ajuda & FAQ — Sistema Académico</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--ink:#0d1117;--blue:#1d6fce;--gray:#6b7280;--light:#f5f7fa;--white:#fff;--brd:#e1e7ef;--font:'Plus Jakarta Sans',sans-serif;--serif:'Playfair Display',serif}
body{font-family:var(--font);background:var(--white);color:var(--ink);-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
nav{display:flex;align-items:center;justify-content:space-between;padding:0 2.5rem;height:64px;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid var(--brd);position:sticky;top:0;z-index:50}
.nbrand{display:flex;align-items:center;gap:.75rem;font-weight:800;font-size:1rem}
.nbrand-ico{width:36px;height:36px;background:var(--ink);border-radius:9px;display:flex;align-items:center;justify-content:center}
.nlinks{display:flex;align-items:center;gap:.25rem}
.nlinks a{padding:.45rem .9rem;border-radius:8px;font-size:.875rem;font-weight:500;color:var(--gray);transition:all .15s}
.nlinks a:hover{background:var(--light);color:var(--ink)}
.ncta{background:var(--ink)!important;color:#fff!important;font-weight:700!important}
.ncta:hover{background:var(--blue)!important}
.page-hero{background:var(--light);padding:4rem 2rem 3.5rem;text-align:center;border-bottom:1px solid var(--brd)}
.page-tag{display:inline-block;background:#e8f0fb;color:var(--blue);padding:.3rem .875rem;border-radius:50px;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;margin-bottom:1rem}
.page-hero h1{font-family:var(--serif);font-size:clamp(2rem,4vw,2.8rem);margin-bottom:.75rem}
.page-hero p{color:var(--gray);max-width:480px;margin:0 auto;font-size:.975rem;line-height:1.7}
.container{max-width:860px;margin:0 auto;padding:4rem 2rem}

/* Install Guide */
.install-steps{counter-reset:step;list-style:none;margin-top:1.5rem}
.install-steps li{counter-increment:step;display:flex;gap:1.1rem;align-items:flex-start;padding:1rem 0;border-bottom:1px solid var(--brd)}
.install-steps li:last-child{border:none}
.install-steps li::before{content:counter(step);display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--blue);color:#fff;font-weight:800;font-size:.85rem;flex-shrink:0;margin-top:2px}
.install-steps li strong{display:block;font-size:.95rem;font-weight:700;margin-bottom:.25rem}
.install-steps li p{font-size:.875rem;color:var(--gray);line-height:1.6}
.install-steps li code{font-family:monospace;background:#f1f5f9;padding:.1rem .4rem;border-radius:4px;font-size:.85rem;color:#0369a1}

/* FAQ */
.faq-section{margin-bottom:3rem}
.faq-section h2{font-family:var(--serif);font-size:1.5rem;margin-bottom:1.25rem;color:var(--ink);display:flex;align-items:center;gap:.625rem}
.faq-item{border:1px solid var(--brd);border-radius:10px;margin-bottom:.625rem;overflow:hidden}
.faq-q{width:100%;background:none;border:none;text-align:left;padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;cursor:pointer;font-family:var(--font);font-size:.9rem;font-weight:700;color:var(--ink);transition:background .15s}
.faq-q:hover{background:var(--light)}
.faq-q .icon{font-size:1.1rem;transition:transform .25s;flex-shrink:0}
.faq-q.open .icon{transform:rotate(45deg)}
.faq-a{max-height:0;overflow:hidden;transition:max-height .3s ease,padding .3s ease}
.faq-a.open{max-height:400px}
.faq-a-inner{padding:.25rem 1.25rem 1.1rem;font-size:.875rem;color:var(--gray);line-height:1.75}
.faq-a-inner code{font-family:monospace;background:#f1f5f9;padding:.1rem .4rem;border-radius:4px;font-size:.83rem;color:#0369a1}
.faq-a-inner strong{color:var(--ink)}
.faq-a-inner ul{margin:.5rem 0 .5rem 1.1rem}
.faq-a-inner ul li{margin-bottom:.25rem}

/* Demo box */
.demo-box{background:var(--light);border:1px solid var(--brd);border-radius:12px;padding:1.5rem;margin-top:1.5rem}
.demo-box h4{font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);margin-bottom:1rem}
.demo-table{width:100%;border-collapse:collapse;font-size:.875rem}
.demo-table th{text-align:left;padding:.5rem .75rem;background:#eef2f8;font-size:.75rem;font-weight:700;color:var(--gray);border-bottom:1px solid var(--brd)}
.demo-table td{padding:.6rem .75rem;border-bottom:1px solid var(--brd)}
.demo-table tr:last-child td{border:none}
.badge{display:inline-block;padding:.2rem .6rem;border-radius:4px;font-size:.72rem;font-weight:700}
.badge-admin{background:#fce7f3;color:#9d174d}
.badge-gestor{background:#ede9fe;color:#5b21b6}
.badge-func{background:#e0f2fe;color:#0369a1}
.badge-aluno{background:#dcfce7;color:#166534}

footer{background:var(--ink);padding:2rem 3rem;display:flex;align-items:center;justify-content:space-between;color:rgba(255,255,255,.35);font-size:.82rem;margin-top:3rem}
.flogo{font-weight:800;color:#fff;display:flex;align-items:center;gap:.5rem}
</style>
</head>
<body>

<nav>
  <a href="index.php" class="nbrand"><div class="nbrand-ico">📚</div>Sistema Académico</a>
  <div class="nlinks">
    <a href="index.php#funcionalidades">Funcionalidades</a>
    <a href="sobre.php">Sobre</a>
    <?php if(isLoggedIn()): ?>
    <a href="<?= dashboardUrl() ?>" class="ncta">Dashboard →</a>
    <?php else: ?>
    <a href="academicologin.php">Entrar</a>
    <a href="academicoregisto.php" class="ncta">Começar →</a>
    <?php endif; ?>
  </div>
</nav>

<div class="page-hero">
  <div class="page-tag">Ajuda & FAQ</div>
  <h1>Como posso ajudar?</h1>
  <p>Guia de instalação no XAMPP, contas de demonstração e respostas às perguntas mais frequentes.</p>
</div>

<div class="container">

  <!-- INSTALAÇÃO -->
  <div class="faq-section">
    <h2>⚙️ Guia de Instalação (XAMPP)</h2>
    <ol class="install-steps">
      <li>
        <div>
          <strong>Iniciar o XAMPP</strong>
          <p>Abra o XAMPP Control Panel e inicie os serviços <code>Apache</code> e <code>MySQL</code>.</p>
        </div>
      </li>
      <li>
        <div>
          <strong>Copiar os ficheiros</strong>
          <p>Extraia o ZIP para a pasta <code>C:\xampp\htdocs\sa_sistema\</code> (Windows) ou <code>/opt/lampp/htdocs/sa_sistema/</code> (Linux/Mac).</p>
        </div>
      </li>
      <li>
        <div>
          <strong>Importar a base de dados</strong>
          <p>Aceda ao <strong>phpMyAdmin</strong> em <code>http://localhost/phpmyadmin</code>, clique em <strong>Import</strong>, selecione o ficheiro <code>database.sql</code> e clique em <strong>Executar</strong>.</p>
        </div>
      </li>
      <li>
        <div>
          <strong>Verificar configuração (opcional)</strong>
          <p>Se necessário, edite <code>includes/config.php</code> para ajustar as credenciais da BD (por defeito: <code>root</code> sem password, como no XAMPP standard).</p>
        </div>
      </li>
      <li>
        <div>
          <strong>Aceder ao sistema</strong>
          <p>Abra o browser e aceda a <code>http://localhost/sa_sistema</code>. Use uma das contas de demonstração abaixo.</p>
        </div>
      </li>
    </ol>

    <div class="demo-box">
      <h4>🔑 Contas de Demonstração — password de todas: <code>password</code></h4>
      <table class="demo-table">
        <tr><th>Email</th><th>Perfil</th><th>Acesso</th></tr>
        <tr><td><code>admin@academico.pt</code></td><td><span class="badge badge-admin">Admin</span></td><td>Total — utilizadores, cursos, tudo</td></tr>
        <tr><td><code>gestor@academico.pt</code></td><td><span class="badge badge-gestor">Gestor</span></td><td>Cursos, UCs, plano, fichas, matrículas, pautas</td></tr>
        <tr><td><code>funcionario@academico.pt</code></td><td><span class="badge badge-func">Funcionário</span></td><td>Matrículas e pautas de avaliação</td></tr>
        <tr><td><code>aluno@academico.pt</code></td><td><span class="badge badge-aluno">Aluno</span></td><td>Ficha pessoal, matrícula e notas</td></tr>
      </table>
    </div>
  </div>

  <!-- FAQ ALUNOS -->
  <div class="faq-section">
    <h2>👨‍🎓 Perguntas dos Alunos</h2>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Como preencho e submeto a minha ficha? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Após fazer login, vá a <strong>Minha Ficha</strong> no menu lateral. Preencha os dados pessoais, selecione o curso pretendido e faça upload de uma fotografia (JPG/PNG/WebP, máx. 2MB). Clique em <strong>Guardar Rascunho</strong> para salvar sem submeter. Quando estiver pronto, clique em <strong>Submeter para Validação</strong>. Após submissão, não pode editar até receber feedback do Gestor.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">A minha ficha foi rejeitada. O que faço? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Se a sua ficha foi rejeitada, verá o <strong>motivo da rejeição</strong> na página da Ficha. Pode corrigir os dados e guardar novamente — a ficha volta ao estado Rascunho. Depois, submeta novamente para validação.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Porque não consigo pedir matrícula? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Para pedir matrícula precisa de ter a ficha de aluno <strong>aprovada</strong> pelo Gestor Pedagógico. Se a ficha ainda está em Rascunho, Submetida ou Rejeitada, o formulário de matrícula estará bloqueado. Verifique o estado da sua ficha no menu <strong>Minha Ficha</strong>.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Onde vejo as minhas notas? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Aceda ao menu <strong>Minhas Notas</strong> no painel lateral. Verá um histórico completo com todas as unidades curriculares, épocas e notas lançadas. As notas só aparecem depois de o Funcionário criar uma pauta e registar os valores.</div></div>
    </div>
  </div>

  <!-- FAQ FUNCIONÁRIOS -->
  <div class="faq-section">
    <h2>👨‍💼 Perguntas dos Funcionários</h2>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Como aprovo ou rejeito uma matrícula? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Aceda a <strong>Matrículas</strong> no menu. Verá os pedidos pendentes. Clique em <strong>Decidir</strong> junto à matrícula que quer tratar. Pode adicionar observações/justificação e depois clicar <strong>Aprovar</strong> ou <strong>Rejeitar</strong>. O sistema regista automaticamente o seu nome e a data/hora da decisão.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Como crio uma pauta e lanço notas? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Em <strong>Pautas</strong>, selecione a UC, o ano letivo (ex: 2024/2025) e a época (Normal, Recurso ou Especial). Clique em <strong>Criar Pauta</strong>. O sistema adiciona automaticamente os alunos com matrícula aprovada nessa UC/ano. Depois, clique em <strong>Editar Notas</strong> na pauta criada e insira as notas (0 a 20) para cada aluno.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">A pauta não tem alunos. Porquê? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Os alunos só são adicionados automaticamente se tiverem uma <strong>matrícula aprovada</strong> para o mesmo ano letivo e num curso que inclua a UC no plano de estudos. Verifique se: (1) o aluno tem matrícula aprovada, (2) o ano letivo da pauta coincide com o da matrícula, e (3) a UC está no plano de estudos do curso do aluno.</div></div>
    </div>
  </div>

  <!-- FAQ GESTORES -->
  <div class="faq-section">
    <h2>👩‍🏫 Perguntas dos Gestores</h2>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Como adiciono uma UC ao plano de estudos? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Vá a <strong>Plano de Estudos</strong>. Selecione o curso no topo. No formulário abaixo, escolha a UC, o ano (1, 2 ou 3) e o semestre (1 ou 2) e clique em Adicionar. Se tentar adicionar uma UC já existente no mesmo curso/ano/semestre, o sistema impedirá a duplicação.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Como valido a ficha de um aluno? <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Em <strong>Fichas Aluno</strong>, filtre por <em>Submetidas</em>. Clique em <strong>Ver</strong> na ficha do aluno. Veja os dados, a fotografia e o curso pretendido. Pode adicionar observações e clicar em <strong>Aprovar Ficha</strong> ou <strong>Rejeitar</strong>. O aluno verá o estado atualizado imediatamente.</div></div>
    </div>
  </div>

  <!-- PROBLEMAS TÉCNICOS -->
  <div class="faq-section">
    <h2>🔧 Problemas Técnicos</h2>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Erro "Erro de ligação à base de dados" <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">
        Verifique os seguintes pontos:
        <ul>
          <li>O serviço <strong>MySQL</strong> está ativo no XAMPP Control Panel</li>
          <li>Importou o ficheiro <code>database.sql</code> no phpMyAdmin</li>
          <li>Em <code>includes/config.php</code>, as credenciais estão corretas (<code>root</code> sem password por defeito no XAMPP)</li>
        </ul>
      </div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">O upload de foto não funciona <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Certifique-se que a pasta <code>uploads/fotos/</code> existe dentro da pasta do projeto e tem permissões de escrita. No Windows com XAMPP isso é automático. O ficheiro deve ser <strong>JPG, PNG ou WebP</strong> com menos de <strong>2MB</strong>.</div></div>
    </div>

    <div class="faq-item">
      <button class="faq-q" onclick="toggle(this)">Sou redirecionado para "Acesso Negado" <span class="icon">+</span></button>
      <div class="faq-a"><div class="faq-a-inner">Cada página está protegida por perfil. Por exemplo, um Aluno não pode aceder às páginas de Funcionário ou Gestor. Faça login com a conta do perfil correto. Se acabou de criar uma conta, verifique se o perfil selecionado no registo está correto.</div></div>
    </div>
  </div>

  <div style="display:flex;gap:.875rem;flex-wrap:wrap;padding-top:1rem;border-top:1px solid var(--brd)">
    <a href="index.php" style="display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;background:var(--ink);color:#fff;border-radius:8px;font-weight:700;font-size:.875rem">← Início</a>
    <a href="sobre.php"  style="display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;background:var(--light);color:var(--ink);border:1px solid var(--brd);border-radius:8px;font-weight:700;font-size:.875rem">ℹ️ Sobre o Sistema</a>
    <?php if(!isLoggedIn()): ?>
    <a href="academicologin.php"  style="display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;background:#1d6fce;color:#fff;border-radius:8px;font-weight:700;font-size:.875rem">⚡ Entrar</a>
    <?php endif; ?>
  </div>
</div>

<footer>
  <div class="flogo">📚 Sistema Académico</div>
  <span>© <?= date('Y') ?> — Instituição de Ensino Superior</span>
</footer>

<script>
function toggle(btn) {
  btn.classList.toggle('open');
  const answer = btn.nextElementSibling;
  answer.classList.toggle('open');
}
</script>
</body>
</html>
