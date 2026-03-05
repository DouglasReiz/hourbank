<main class="landing-card dashboard-card">
    <header class="header-flex">
        <div>
            <h1>Aprovações Pendentes</h1>
            <p class="description" style="margin-bottom:0;">
                <?= count($entries) ?> lançamento(s) aguardando aprovação
            </p>
        </div>
        <a href="/hour-bank" class="btn btn-secondary" style="margin-top:0;">← Voltar</a>
    </header>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Colaborador</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Total</th>
                    <th>Justificativa</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($entries)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--text-muted);">
                            Nenhum lançamento pendente.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($entry['user_name']) ?></strong></td>
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
                            <td style="display:flex;gap:0.5rem;">
                                <form method="POST" action="/hour-bank/approve">
                                    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">✔ Aprovar</button>
                                </form>
                                <form method="POST" action="/hour-bank/reject">
                                    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm"
                                            style="color:#ef4444;border-color:#ef4444;">
                                        ✘ Rejeitar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer style="margin-top:2rem;border-top:1px solid #e2e8f0;padding-top:1rem;">
        <a href="/dashboard" class="back-link" style="margin-top:0;">← Voltar ao Dashboard</a>
    </footer>
</main>
