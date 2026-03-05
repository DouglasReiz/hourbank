<main class="landing-card">
    <h1>Acessar Conta</h1>
    <p class="description">Entre com suas credenciais corporativas.</p>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="/login/store">
        <div class="form-group">
            <label for="email">E-mail Institucional</label>
            <input type="email" id="email" name="email" placeholder="nome@empresa.com.br" required>
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar no Portal</button>
    </form>

    <a href="/" class="back-link">← Voltar para o início</a>
</main>