<?php
$numero = (string) (session()->get('numero') ?? '');
$numeroAffiche = $numero !== '' ? $numero : 'Invité';
$avatar = $numero !== '' ? substr(preg_replace('/\D+/', '', $numero), -2) : 'NA';
$activePage = $activePage ?? '';

$navItems = [
    ['key' => 'dashboard', 'label' => 'Solde', 'icon' => 'bi-wallet2', 'href' => base_url('dashboard')],
    ['key' => 'depot', 'label' => 'Dépôt', 'icon' => 'bi-plus-lg', 'href' => base_url('operations/depot')],
    ['key' => 'retrait', 'label' => 'Retrait', 'icon' => 'bi-arrow-down-left', 'href' => base_url('operations/retrait')],
    ['key' => 'transfert', 'label' => 'Transfert', 'icon' => 'bi-arrow-left-right', 'href' => base_url('operations/transfert')],
    ['key' => 'transactions', 'label' => 'Transactions', 'icon' => 'bi-clock-history', 'href' => base_url('transactions')],
    ['key' => 'rapports', 'label' => 'Rapports', 'icon' => 'bi-graph-up', 'href' => base_url('rapports')],
    ['key' => 'receipt', 'label' => 'Reçus', 'icon' => 'bi-receipt', 'href' => base_url('recu')],
];
?>
<button type="button" class="sidebar-burger" id="sidebarBurger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="sidebar">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar" id="sidebar">
    <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" aria-label="Réduire le menu">
        <i class="bi bi-chevron-left"></i>
    </button>

    <div class="brand">
        <div class="brand-mark">C</div>
        <div class="brand-name">CASH</div>
        <div class="brand-sub">Caisse en ligne</div>
    </div>

    <ul class="nav-ledger">
        <?php foreach ($navItems as $item): ?>
            <li>
                <a href="<?= esc($item['href'], 'attr') ?>"
                    class="nav-link <?= $activePage === $item['key'] ? 'active' : '' ?>">
                    <i class="bi <?= esc($item['icon'], 'attr') ?>"></i>
                    <span class="nav-label"><?= esc($item['label']) ?></span>
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
            <span class="nav-label">Déconnexion</span>
        </a>
    </div>
</aside>

<script src="<?= base_url('script/sidebar.js') ?>"></script>
