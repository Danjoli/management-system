<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sistema de Gestão</title><link rel="stylesheet" href="/assets/app.css"></head><body>
<aside class="sidebar"><a class="brand" href="/dashboard"><span>SG</span> Management</a><nav>
<a class="active" href="/dashboard">Visão geral</a><a href="/manage/departments">Departamentos</a><a href="/manage/employees">Colaboradores</a>
<a href="/manage/projects">Projetos</a><a href="/manage/transactions">Financeiro</a><a href="/reports">Relatórios</a></nav>
<form method="post" action="/logout"><input type="hidden" name="_token" value="<?= htmlspecialchars((string) ($token ?? '')) ?>"><button class="link-button">Sair</button></form></aside>
<main class="main"><?= $content ?></main></body></html>

