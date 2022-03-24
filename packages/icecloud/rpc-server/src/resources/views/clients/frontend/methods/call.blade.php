<?php
    /** @var \IceCloud\RpcServer\Lib\Procedure $proc */
?>

<?= $proc->getEndName()?>() {
    return client.call('<?= $proc->getFullQualifiedName()?>');
}
