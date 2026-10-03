<section class="auth-card"><div class="logo">SG</div><p class="eyebrow">AMBIENTE ADMINISTRATIVO</p><h1>Bem-vindo de volta</h1>
<p class="muted">Entre para acompanhar sua operação.</p><?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" action="/login"><input type="hidden" name="_token" value="<?= htmlspecialchars($token) ?>">
<label>E-mail<input type="email" name="email" autocomplete="email" required placeholder="admin@example.com"></label>
<label>Senha<input type="password" name="password" autocomplete="current-password" required placeholder="Sua senha"></label>
<button type="submit">Entrar no sistema</button></form><p class="hint">Acesso inicial: admin@example.com / Admin@123</p></section>

