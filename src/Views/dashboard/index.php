<?php $name = htmlspecialchars((string) ($user['name'] ?? 'Usuário')); ?>
<header class="page-header"><div><p class="eyebrow">PAINEL DE CONTROLE</p><h1>Olá, <?= $name ?></h1><p class="muted">Aqui está o resumo da sua operação.</p></div><span class="date"><?= date('d/m/Y') ?></span></header>
<section class="metrics"><article><span>Colaboradores ativos</span><strong><?= (int) ($metrics['employees'] ?? 0) ?></strong></article>
<article><span>Departamentos</span><strong><?= (int) ($metrics['departments'] ?? 0) ?></strong></article>
<article><span>Projetos em andamento</span><strong><?= (int) ($metrics['projects'] ?? 0) ?></strong></article>
<article><span>Saldo consolidado</span><strong>R$ <?= number_format((float) ($metrics['balance'] ?? 0), 2, ',', '.') ?></strong></article></section>
<section class="panel"><p class="eyebrow">OPERAÇÃO</p><h2>Gestão centralizada, decisões claras.</h2><p class="muted">Use o menu lateral para administrar equipes, projetos, lançamentos e relatórios.</p></section>

