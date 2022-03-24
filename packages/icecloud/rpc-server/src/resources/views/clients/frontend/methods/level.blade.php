<?php
    /** @var int $level */
    /** @var array $tree */
    /** @var string $name */
?>
<?= $name ?>: {
    @include('rpc-server::clients.frontend.methods.iteration', ['tree' => $entity, 'level' => $level + 1])
}
