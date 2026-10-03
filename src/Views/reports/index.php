<?php $esc = static fn (mixed $value): string => htmlspecialchars((string) $value); ?>
<header class="page-header"><div><p class="eyebrow">INTELIGÊNCIA GERENCIAL</p><h1>Relatório financeiro</h1><p class="muted">Consolidado por departamento com ranking de saldo.</p></div></header>
<section class="panel"><form class="filters" method="get"><label>De<input type="date" name="from" value="<?= $esc($from) ?>"></label><label>Até<input type="date" name="to" value="<?= $esc($to) ?>"></label><button>Aplicar filtros</button><a class="export" href="/reports/export?from=<?= $esc($from) ?>&to=<?= $esc($to) ?>">Exportar CSV</a></form></section>
<section class="panel table-panel"><table><thead><tr><th>#</th><th>Departamento</th><th>Receitas</th><th>Despesas</th><th>Saldo</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= (int)$row['ranking'] ?></td><td><?= $esc($row['department']) ?></td><td>R$ <?= number_format((float)$row['income'],2,',','.') ?></td><td>R$ <?= number_format((float)$row['expense'],2,',','.') ?></td><td><strong>R$ <?= number_format((float)$row['balance'],2,',','.') ?></strong></td></tr><?php endforeach; ?>
</tbody></table></section>

