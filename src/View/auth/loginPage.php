<main class="landing-card">
    <h1>Acessar Conta</h1>
    <p class="description">Entre com suas credenciais corporativas.</p>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Login Microsoft -->
    <a href="/auth/microsoft" class="btn btn-primary" style="width:100%;display:flex;align-items:center;justify-content:center;gap:0.75rem;margin-bottom:1rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 21 21">
            <rect x="1" y="1" width="9" height="9" fill="#f25022" />
            <rect x="11" y="1" width="9" height="9" fill="#7fba00" />
            <rect x="1" y="11" width="9" height="9" fill="#00a4ef" />
            <rect x="11" y="11" width="9" height="9" fill="#ffb900" />
        </svg>
        Entrar com Microsoft 365
    </a>

    <a href="/" class="back-link">← Voltar para o início</a>
</main>