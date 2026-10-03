<?php
$esc = static fn (mixed $value): string => htmlspecialchars((string) $value);
$labels = ['name'=>'Nome','cost_center'=>'Centro de custo','budget'=>'Orçamento','department_id'=>'Departamento','email'=>'E-mail','position'=>'Cargo','salary'=>'Salário','hired_at'=>'Admissão','description'=>'Descrição','status'=>'Status','starts_at'=>'Início','ends_at'=>'Término','project_id'=>'Projeto','type'=>'Tipo','category'=>'Categoria','amount'=>'Valor','occurred_on'=>'Data'];
$forms = [
'departments'=>['name'=>'text','cost_center'=>'text','budget'=>'number'],
'employees'=>['department_id'=>'department','name'=>'text','email'=>'email','position'=>'text','salary'=>'number','hired_at'=>'date'],
'projects'=>['department_id'=>'department','name'=>'text','description'=>'text','status'=>'status','budget'=>'number','starts_at'=>'date','ends_at'=>'date'],
'transactions'=>['department_id'=>'department','project_id'=>'project','type'=>'type','category'=>'text','description'=>'text','amount'=>'number','occurred_on'=>'date'],
]; ?>
<header class="page-header"><div><p class="eyebrow">MÓDULO GERENCIAL</p><h1><?= $esc($title) ?></h1></div></header>
<?php if ($message): ?><div class="notice"><?= $esc($message) ?></div><?php endif; ?><?php if ($error): ?><div class="alert"><?= $esc($error) ?></div><?php endif; ?>
<section class="panel"><h2>Novo registro</h2><form class="form-grid" method="post"><input type="hidden" name="_token" value="<?= $esc($token) ?>">
<?php foreach ($forms[$resource] as $field=>$type): ?><label><?= $esc($labels[$field]) ?>
<?php if ($type==='department'): ?><select name="<?= $field ?>" required><option value="">Selecione</option><?php foreach ($departments as $item): ?><option value="<?= (int)$item['id'] ?>"><?= $esc($item['name']) ?></option><?php endforeach; ?></select>
<?php elseif ($type==='project'): ?><select name="<?= $field ?>"><option value="">Sem projeto</option><?php foreach ($projects as $item): ?><option value="<?= (int)$item['id'] ?>"><?= $esc($item['name']) ?></option><?php endforeach; ?></select>
<?php elseif ($type==='status'): ?><select name="status"><option value="planned">Planejado</option><option value="active">Ativo</option><option value="paused">Pausado</option><option value="completed">Concluído</option></select>
<?php elseif ($type==='type'): ?><select name="type"><option value="income">Receita</option><option value="expense">Despesa</option></select>
<?php else: ?><input name="<?= $field ?>" type="<?= $type ?>" <?= $type==='number'?'step="0.01"':'' ?> <?= in_array($field,['description','starts_at','ends_at'],true)?'':'required' ?>><?php endif; ?></label><?php endforeach; ?>
<button type="submit">Salvar registro</button></form></section>
<section class="panel table-panel"><h2>Registros</h2><div class="table-wrap"><table><?php if ($rows): ?><thead><tr><?php foreach (array_keys($rows[0]) as $column): ?><th><?= $esc(ucwords(str_replace('_',' ',$column))) ?></th><?php endforeach; ?></tr></thead><?php endif; ?><tbody><?php foreach ($rows as $row): ?><tr><?php foreach ($row as $value): ?><td><?= $esc($value) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table><?php if (!$rows): ?><p class="muted">Nenhum registro cadastrado.</p><?php endif; ?></div></section>

