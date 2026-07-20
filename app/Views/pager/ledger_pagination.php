<?php if ($pager->getPageCount() > 1) : ?>
<nav aria-label="Pagination de l'historique">
    <ul class="pagination-ledger">
        <li class="page-item <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">
            <?php if ($pager->hasPreviousPage()) : ?>
                <a class="page-link" href="<?= esc($pager->getPreviousPage()) ?>" aria-label="Précédent">
                    <i class="bi bi-chevron-left"></i>
                </a>
            <?php else : ?>
                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
            <?php endif; ?>
        </li>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= esc($link['uri']) ?>"><?= esc($link['title']) ?></a>
            </li>
        <?php endforeach; ?>

        <li class="page-item <?= $pager->hasNextPage() ? '' : 'disabled' ?>">
            <?php if ($pager->hasNextPage()) : ?>
                <a class="page-link" href="<?= esc($pager->getNextPage()) ?>" aria-label="Suivant">
                    <i class="bi bi-chevron-right"></i>
                </a>
            <?php else : ?>
                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
            <?php endif; ?>
        </li>
    </ul>
</nav>
<?php endif; ?>
