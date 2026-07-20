<?php
$numero = (string) (session()->get('numero') ?? '');
$numeroAffiche = $numero !== '' ? $numero : 'Invité';
$avatar = $numero !== '' ? substr(preg_replace('/\D+/', '', $numero), -2) : 'NA';
$activePage = $activePage ?? '';

$navItems = [
    ['key' => 'dashboard', 'label' => 'Solde', 'icon' => 'bi-wallet2', 'href' => base_url('dashboard')],
    ['key' => 'retrait', 'label' => 'Retrait', 'icon' => 'bi-arrow-down-left', 'href' => base_url('operations/retrait')],
    ['key' => 'transfert', 'label' => 'Transfert', 'icon' => 'bi-arrow-left-right', 'href' => base_url('operations/transfert')],
    ['key' => 'transactions', 'label' => 'Transactions', 'icon' => 'bi-clock-history', 'href' => base_url('transactions')],
    ['key' => 'receipt', 'label' => 'Reçus', 'icon' => 'bi-receipt', 'href' => base_url('recu')],
];
?>
<aside class="sidebar">
    <div class="brand">
        <div class="brand-mark">C</div>
        <div class="brand-name">CASH</div>
        <div class="brand-sub">Caisse en ligne</div>
    </div>

    <ul class="nav-ledger">
        <?php foreach ($navItems as $item) : ?>
            <li>
                <a href="<?= esc($item['href'], 'attr') ?>" class="nav-link <?= $activePage === $item['key'] ? 'active' : '' ?>">
                    <i class="bi <?= esc($item['icon'], 'attr') ?>"></i>
                    <?= esc($item['label']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-footer">
        <div class="avatar"><?= esc($avatar) ?></div>
        <div class="sidebar-user">
            <div class="sidebar-user-number"><?= esc($numeroAffiche) ?></div>
            <div>Compte personnel</div>
        </div>
    </div>

    <div class="sidebar-actions">
        <a href="<?= esc(base_url('logout'), 'attr') ?>" class="btn-sidebar-logout">
            <i class="bi bi-box-arrow-right"></i>
            Déconnexion
        </a>
    </div>
</aside>