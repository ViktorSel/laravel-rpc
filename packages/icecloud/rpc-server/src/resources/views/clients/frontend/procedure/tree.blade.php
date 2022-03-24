@php
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsProcedure;
    /**
     * @var JsProcedure[]|array $tree
     */
@endphp

@foreach($tree as $name => $sub)
    @if ($sub instanceof JsProcedure)
        @include('rpc-server::clients.frontend.procedure.body', ['procedure' => $sub])
    @else
        {{$name}}: {
            @include('rpc-server::clients.frontend.procedure.tree', ['tree' => $sub])
        },
    @endif
@endforeach
