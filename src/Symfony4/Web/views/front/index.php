<?php

/**
 * @var $docs Collection | DocEntity[]
 */

use Illuminate\Support\Collection;
use ZnLib\Rpc\Domain\Entities\DocEntity;

?>

<div class="jumbotron">
    <h1 class="display-4">API documentation</h1>
    <div class="list-group">
        <?php foreach ($docs as $docEntity): ?>
            <span class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                <a href="/json-rpc/view/<?= $docEntity->getName() ?>">
                    <?= $docEntity->getTitle() ?>
                </a>
                <a href="/json-rpc/download/<?= $docEntity->getName() ?>">
                    <i class="fas fa-download"></i>
                </a>
            </span>
        <?php endforeach; ?>
    </div>
</div>
