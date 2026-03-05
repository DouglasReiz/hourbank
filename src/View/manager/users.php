<main class="landing-card dashboard-card">
    <header class="header-flex">
        <div>
            <h1>Gestão de Colaboradores</h1>
            <p class="description" style="margin-bottom:0;">
                <?= count($users) ?> colaborador(es) cadastrado(s)
            </p>
        </div>
        <div style="display:flex;gap:0.75rem;">
            <a href="/hour-bank/pending" class="btn btn-primary" style="margin-top:0;">
                Ver Pendentes
            </a>
            <a href="/dashboard" class="btn btn-secondary" style="margin-top:0;">
                ← Dashboard
            </a>
        </div>
    </header>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Colaborador</th>
                    <th>Cargo</th>
                    <th>Setor</th>
                    <th>Perfil</th>
                    <th>Saldo</th>
                    <th>Lançamentos</th>
                    <th>Pendentes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--text-muted);">
                            Nenhum colaborador encontrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $minutes  = (int) ($user['total_minutes'] ?? 0);
                        $balance  = sprintf('%dh %02dmin', intdiv($minutes, 60), $minutes % 60);
                        $pending  = (int) $user['total_pendentes'];
                        $roleMap  = [
                            'employee' => ['label' => 'Colaborador', 'color' => '#64748b'],
                            'manager'  => ['label' => 'Gestor',      'color' => '#6d00ef'],
                            'admin'    => ['label' => 'Admin',        'color' => '#ef4444'],
                        ];
                        $role = $roleMap[$user['role']] ?? ['label' => $user['role'], 'color' => '#64748b'];
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user['name']) ?></strong>
                                <br>
                                <small style="color:var(--text-muted);"><?= htmlspecialchars($user['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($user['cargo']) ?></td>
                            <td><?= strtoupper(htmlspecialchars($user['setor'])) ?></td>
                            <td>
                                <span style="color:<?= $role['color'] ?>;font-weight:600;">
                                    <?= $role['label'] ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color:var(--primary-color);"><?= $balance ?></strong>
                            </td>
                            <td style="text-align:center;">
                                <?= (int) $user['total_lancamentos'] ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if ($pending > 0): ?>
                                    <a href="/hour-bank/pending/user/<?= $user['id'] ?>"
                                        style="background:#fef3c7;color:#d97706;padding:0.3rem 0.8rem;border-radius:999px;font-size:0.8rem;font-weight:700;text-decoration:none;white-space: nowrap;">
                                        ⚠ <?= $pending ?> pendente(s)
                                    </a>
                                <?php else: ?>
                                    <span style="color:#22c55e;font-weight:600;">✔ Em dia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer style="margin-top:2rem;border-top:1px solid #e2e8f0;padding-top:1rem;">
        <a href="/logout" class="back-link" style="margin-top:0;">Sair do Sistema</a>
    </footer>
</main>