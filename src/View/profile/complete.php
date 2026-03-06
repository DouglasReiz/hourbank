<main class="landing-card">
    <h1>Complete seu Perfil</h1>
    <p class="description">
        Seu acesso foi autenticado pela Microsoft.<br>
        Preencha os dados abaixo para continuar.
    </p>

    <?php if (!empty($errors)): ?>
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:var(--radius);padding:1rem;margin-bottom:1.5rem;text-align:left;">
            <?php foreach ($errors as $error): ?>
                <p style="color:#dc2626;font-size:0.9rem;">⚠ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/profile/complete/store">
        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf"
                placeholder="000.000.000-00" maxlength="14" required>
        </div>

        <div class="form-group">
            <label for="cargo">Cargo / Função</label>
            <input type="text" id="cargo" name="cargo"
                placeholder="Ex: Desenvolvedor" required>
        </div>

        <div class="form-group">
            <label for="setor">Setor</label>
            <select id="setor" name="setor" required>
                <option value="">Selecione...</option>
                <option value="ti">Tecnologia (TI)</option>
                <option value="rh">Recursos Humanos</option>
                <option value="fin">Financeiro</option>
                <option value="op">Operações</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">
            Salvar e Continuar
        </button>
    </form>
</main>

<script src="/assets/js/profileComplete"></script>