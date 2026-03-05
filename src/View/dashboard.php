<main class="landing-card dashboard-card">
    <header class="header-flex">
        <div>
            <h1>Dashboard</h1>
            <p class="description" style="margin-bottom: 0;">Bem-vindo, <?= htmlspecialchars($user['name']) ?>!</p>
        </div>
        <a href="/hour-bank" class="btn btn-primary" style="margin-top: 0;">+ Novo Lançamento</a>

        <?php if (in_array($_SESSION['user_role'], ['manager', 'admin'])): ?>
            <a href="/manager/users" class="btn btn-secondary" style="margin-top:0;">
                👥 Colaboradores
            </a>
        <?php endif; ?>
    </header>

    <section class="stats-grid">
        <div class="stat-box">
            <span>Total de Lançamentos</span>
            <h3><?= count($entries) ?></h3>
        </div>
        <div class="stat-box">
            <span>Saldo Atual</span>
            <h3><?= htmlspecialchars($balance) ?></h3>
        </div>
        <div class="stat-box">
            <span>Pendentes de Aprovação</span>
            <h3 style="color: #f59e0b;">
                <?= count(array_filter($entries, fn($e) => $e['status'] === 'pending')) ?>
            </h3>
        </div>
    </section>

    <section>
        <h2 style="text-align: left; font-size: 1.2rem; margin-bottom: 1rem; color: var(--primary-color);">
            Lançamentos Recentes
        </h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--text-muted);">
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

    <footer style="margin-top: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1rem; display:flex; justify-content:space-between;">
        <a href="/hour-bank" class="back-link" style="margin-top:0;">Ver todos os lançamentos →</a>
        <a href="/logout" class="back-link" style="margin-top: 0;">Sair do Sistema</a>
    </footer>
</main>