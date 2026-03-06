<main class="landing-card dashboard-card">
    <header class="header-flex">
        <div>
            <h1>Pendentes — <?= htmlspecialchars($userName) ?></h1>
            <p class="description" style="margin-bottom:0;">
                <?= count($entries) ?> lançamento(s) aguardando aprovação
            </p>
        </div>
        <a href="/manager/users" class="btn btn-secondary" style="margin-top:0;">← Voltar</a>
    </header>

    <?php if (empty($entries)): ?>
        <div style="text-align:center;padding:2rem;color:var(--text-muted);">
            <p style="font-size:1.2rem;">✔ Nenhum lançamento pendente.</p>
            <a href="/manager/users" class="btn btn-primary" style="margin-top:1rem;">
                Voltar para Colaboradores
            </a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Início</th>
                        <th>Término</th>
                        <th>Total</th>
                        <th>Tipo</th>
                        <th>Adicional</th>
                        <th>Justificativa</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($entry['reference_date'])) ?></td>
                            <td><?= htmlspecialchars($entry['start_time']) ?></td>
                            <td><?= htmlspecialchars($entry['end_time']) ?></td>
                            <td><strong><?= htmlspecialchars($entry['hours_formatted']) ?></strong></td>
                            <td>
                                <?php if ($entry['type'] === 'credit'): ?>
                                    <span style="color:#22c55e;font-weight:600;">+ Crédito</span>
                                <?php else: ?>
                                    <span style="color:#ef4444;font-weight:600;">- Débito</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $rate = (float) $entry['additional_rate'];
                                $label = match (true) {
                                    $rate >= 2.40 => '100% + Noturno',
                                    $rate >= 2.00 => '100% (Dom/Feriado)',
                                    $rate >= 1.80 => '50% + Noturno',
                                    default       => '50% (Dia Útil)',
                                };
                                ?>
                                <span style="font-size:0.85rem;color:var(--text-muted);">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($entry['justification']) ?></td>
                            <td style="display:flex;gap:0.5rem;">
                                <form method="POST" action="/hour-bank/approve">
                                    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                    <input type="hidden" name="redirect" value="/hour-bank/pending/user/<?= $userId ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">✔ Aprovar</button>
                                </form>
                                <form method="POST" action="/hour-bank/reject">
                                    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                                    <input type="hidden" name="redirect" value="/hour-bank/pending/user/<?= $userId ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm"
                                        style="color:#ef4444;border-color:#ef4444;">
                                        ✘ Rejeitar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <footer style="margin-top:2rem;border-top:1px solid #e2e8f0;padding-top:1rem;">
        <a href="/logout" class="back-link" style="margin-top:0;">Sair do Sistema</a>
    </footer>
</main>