====================================================
  SISTEMA ACADÉMICO — INSTALAÇÃO NO XAMPP
====================================================

PASSO 1 — COPIAR FICHEIROS
---------------------------
Extraia este ZIP para a pasta htdocs do XAMPP:

  Windows:  C:\xampp\htdocs\sa_sistema\
  Mac/Linux: /opt/lampp/htdocs/sa_sistema/

ATENÇÃO: A pasta deve chamar-se "sa_sistema" e conter
directamente os ficheiros (index.php, database.sql, etc.)
NÃO deve existir uma pasta sa_sistema dentro de sa_sistema.

Estrutura correcta:
  htdocs\
    sa_sistema\
      index.php          ← deve existir aqui
      database.sql       ← deve existir aqui
      includes\
      pages\
        aluno\
        funcionario\
        gestor\
        admin\
      uploads\


PASSO 2 — INICIAR O XAMPP
--------------------------
Abra o XAMPP Control Panel e inicie:
  ✅ Apache  (botão Start)
  ✅ MySQL   (botão Start)

Ambos devem ficar com fundo VERDE.


PASSO 3 — IMPORTAR A BASE DE DADOS
------------------------------------
1. Abra o browser e aceda a:
   http://localhost/phpmyadmin

2. Clique em "Import" (no menu do topo)

3. Clique em "Choose File" e selecione:
   sa_sistema\database.sql

4. Clique em "Go" / "Executar"

5. Deve ver uma mensagem de sucesso a verde.


PASSO 4 — VERIFICAR INSTALAÇÃO
---------------------------------
Aceda a: http://localhost/sa_sistema/verificar.php

Deve ver todos os itens com ✅ OK.


PASSO 5 — USAR O SISTEMA
--------------------------
Aceda a: http://localhost/sa_sistema/

Login:   http://localhost/sa_sistema/academicologin.php
Registo: http://localhost/sa_sistema/academicoregisto.php


CONTAS DE DEMONSTRAÇÃO (password: password)
--------------------------------------------
  Admin:       admin@academico.pt
  Gestor:      gestor@academico.pt
  Funcionário: funcionario@academico.pt
  Aluno:       aluno@academico.pt


RESOLUÇÃO DE PROBLEMAS
-----------------------
❌ "Not Found" → Os ficheiros não foram extraídos correctamente.
   Verifique se a pasta pages\ existe dentro de sa_sistema\.

❌ "Erro de ligação BD" → MySQL não está a correr ou o
   database.sql não foi importado.

❌ "Acesso Negado" → Está a tentar aceder a uma página
   com um perfil que não tem permissão.

Para diagnóstico completo:
  http://localhost/sa_sistema/verificar.php

====================================================
