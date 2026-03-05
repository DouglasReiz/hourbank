<!-- Adicione no topo do register.php, antes do <main> -->
<?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<main class="landing-card">
    <h1>Solicitar Acesso</h1>
    <p class="description">Preencha os dados para criar sua conta de colaborador.</p>

    <form class="form-grid" method="POST" action="/register/store">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Ex: Douglas Alves" required>
        </div>

        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
        </div>

        <div class="form-group full-width">
            <label for="email">E-mail Corporativo</label>
            <input type="email" id="email" name="email" placeholder="usuario@empresa.com.br" required>
        </div>

        <div class="form-group">
            <label for="cargo">Cargo/Função</label>
            <input type="text" id="cargo" name="cargo" placeholder="Ex: Desenvolvedor" required>
        </div>

        <div class="form-group">
            <label for="setor">Setor</label>
            <select id="setor" name="setor">
                <option value="ti">Tecnologia (TI)</option>
                <option value="rh">Recursos Humanos</option>
                <option value="fin">Financeiro</option>
                <option value="op">Operações</option>
            </select>
        </div>

        <div class="form-group">
            <label for="pass">Senha</label>
            <input type="password" id="pass" name="password" placeholder="Mínimo 8 caracteres" required>
        </div>

        <div class="form-group">
            <label for="confirm-pass">Confirmar Senha</label>
            <input type="password" id="confirm-pass" name="password_confirmation" required>
        </div>

        <div class="full-width">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Finalizar Cadastro</button>
            <a href="/login" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem; text-align: center;">Já tenho conta</a>
        </div>
    </form>
</main>