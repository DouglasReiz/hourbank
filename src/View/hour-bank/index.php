<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $error): ?>
            <p style="color:#dc2626;font-size:0.9rem;">⚠ <?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main class="landing-card dashboard-card">
    <header class="header-flex">
        <div>
            <h1>Banco de Horas</h1>
            <p class="description" style="margin-bottom: 0;">
                Saldo atual: <strong style="color: var(--primary-color);"><?= htmlspecialchars($balance) ?></strong>
            </p>
        </div>
        <?php if (in_array($_SESSION['user_role'], ['manager', 'admin'])): ?>
            <a href="/hour-bank/pending" class="btn btn-secondary" style="margin-top:0;">
                Ver Pendentes
            </a>
        <?php endif; ?>
    </header>

    <!-- Formulário de Lançamento -->
    <section style="margin-bottom: 2.5rem;">
        <h2 style="text-align:left;font-size:1.2rem;margin-bottom:1rem;color:var(--primary-color);">
            Novo Lançamento
        </h2>

        <form method="POST" action="/hour-bank/store" class="form-grid">
            <div class="form-group">
                <label for="reference_date">Data do Lançamento</label>
                <input type="date" id="reference_date" name="reference_date"
                    max="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="type">Tipo</label>
                <select id="type" name="type">
                    <option value="credit">Crédito (Horas Extras)</option>
                    <option value="debit">Débito (Compensação)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="start_time">Início</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>

            <div class="form-group">
                <label for="end_time">Término</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>

            <div class="form-group full-width">
                <label>
                    <input type="checkbox" name="is_holiday" value="1">
                    &nbsp;Feriado
                </label>
            </div>

            <div class="form-group full-width">
                <label for="justification">Justificativa</label>
                <input type="text" id="justification" name="justification"
                    placeholder="Ex: Reunião após expediente" required>
            </div>

            <div class="full-width">
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Enviar para Aprovação
                </button>
            </div>
        </form>
    </section>

    <!-- Lista de lançamentos -->
    <section>
        <h2 style="text-align:left;font-size:1.2rem;margin-bottom:1rem;color:var(--primary-color);">
            Meus Lançamentos
        </h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Justificativa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;color:var(--text-muted);">
                                Nenhum lançamento encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($entry['reference_date'])) ?></td>
                                <td>
                                    <?php if ($entry['type'] === 'credit'): ?>
                                        <span style="color:#22c55e;font-weight:600;">+ Crédito</span>
                                    <?php else: ?>
                                        <span style="color:#ef4444;font-weight:600;">- Débito</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($entry['hours_formatted']) ?></strong></td>
                                <td><?= htmlspecialchars($entry['justification']) ?></td>
                                <td>
                                    <?php
                                    $statusMap = [
                                        'pending'  => ['label' => 'Pendente',  'color' => '#f59e0b'],
                                        'approved' => ['label' => 'Aprovado',  'color' => '#22c55e'],
                                        'rejected' => ['label' => 'Rejeitado', 'color' => '#ef4444'],
                                    ];
                                    $s = $statusMap[$entry['status']] ?? ['label' => $entry['status'], 'color' => '#64748b'];
                                    ?>
                                    <span style="color:<?= $s['color'] ?>;font-weight:600;">
                                        <?= $s['label'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer style="margin-top:2rem;border-top:1px solid #e2e8f0;padding-top:1rem;">
        <a href="/dashboard" class="back-link" style="margin-top:0;">← Voltar ao Dashboard</a>
    </footer>
</main>