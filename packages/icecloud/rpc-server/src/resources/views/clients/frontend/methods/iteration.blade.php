<?php
    /** @var array $tree */
    /** @var int $level */
    use IceCloud\RpcServer\Lib\Procedure;
?>
@foreach($tree as $name => $entity)
    @if ($entity instanceof Procedure)
        @include('rpc-server::clients.frontend.methods.call', ['proc' => $entity])
    @else
        @include('rpc-server::clients.frontend.methods.level', ['name' => $name, 'nested' => $entity, 'level' => $level])
    @endif
@endforeach
