@php
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsProcedure;

    /**
     * @var JsProcedure $procedure
     */

    $argumentsSlugs = [];

    if ($procedure->isDedicatedArgument()) {
        $argumentsSlugs[$procedure->getDedicatedArgument()->name()] = \Illuminate\Support\Str::camel($procedure->getDedicatedArgument()->name());
    } else {
        foreach ($procedure->getArguments() as $argument) {
            $argumentsSlugs[$argument->name()] = \Illuminate\Support\Str::camel($argument->name());
        }

    }

@endphp
@if($procedure->isDedicatedArgument())
    /**
    * {{$procedure->getProcedure()->description()}}
    * @param { {{$procedure->getDedicatedArgument()->type()}} } {{ \Illuminate\Support\Str::camel($procedure->getDedicatedArgument()->name()) }} {{$procedure->getDedicatedArgument()->comment()}}
    * @return {RpcRequestPromise}
    */
    {{$procedure->getProcedure()->getEndName()}}( {{ \Illuminate\Support\Str::camel($procedure->getDedicatedArgument()->name()) }} ) {
        return client.call("{{$procedure->getProcedure()->getFullQualifiedName()}}", {!! \Illuminate\Support\Str::camel($procedure->getDedicatedArgument()->name()) !!});
    },

@else
    /**
     * {{$procedure->getProcedure()->description()}}
     @foreach($procedure->getArguments() as $arg)
     * @param { {{$arg->type()}} } {{ $argumentsSlugs[$arg->name()] }} {{$arg->comment()}}
     @endforeach
     * @return {RpcRequestPromise}
     */
    {{$procedure->getProcedure()->getEndName()}}( {{ implode(', ', array_values($argumentsSlugs)) }} ) {
        return client.call("{{$procedure->getProcedure()->getFullQualifiedName()}}", {
    @foreach($argumentsSlugs as $name=>$slug)
            {{$name}}: {{$slug}},
    @endforeach
        });
    },

@endif

