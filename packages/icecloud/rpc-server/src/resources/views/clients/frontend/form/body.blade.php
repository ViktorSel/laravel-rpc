@php
    use IceCloud\RpcServer\Lib\Generator\JsGenerator;
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelFile;
    use Illuminate\Support\Str;
    use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
    use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
    use IceCloud\RpcServer\Lib\Schema\Definitions\BaseDefinition;
    use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;

    /**
     * @var JsGenerator $generator
     * @var JsInputModelFile $file
     */

@endphp

import MutatedDataModel from "ice-lib-data-routine/src/MutatedDataModel.mjs";

@include('rpc-server::clients.frontend.class.uses', ['file' => $file])

@foreach ($file->getProperties() as $name => $property)
const {{$property->constant()}} = '{{$name}}';
@endforeach

class {{$file->getClassName()}} extends MutatedDataModel {
    /**
     * @inheritDoc
     * @param {Object} defaults Значения по умолчанию
     */
    constructor(defaults = {}) {
        super([
            {{implode(', ', $file->getConstants())}}
        ], defaults, true, true);
    }

    /**
     * @inheritDoc
     * @returns {Object}
     */
    mutators () {
        return {
@foreach($file->getRelations() as $relation)
            [{{ $relation->getProperty()->constant() }}] : {
                arrayable: {{$relation->isArrayable() ? 'true' : 'false'}},
                model: {{$relation->getModel()->getClassName()}}
            },
@endforeach
        };
    }

@foreach ($file->getProperties() as $property)

    /**
     * Задать {{$property->comment()}}
     *
     * @param { {{$property->type()}} } value
     * @returns { {{$file->getClassName()}} }
     */

    {{$property->getSetterName()}}(value) {
        this.setAttribute({{$property->constant()}}, value);
        return this;
    }

    /**
     * Получить {{$property->comment()}}
     *
     * @returns { {{$property->type()}} }
     */
    {{$property->getGetterName()}}() {
        return this.getAttribute({{$property->constant()}});
    }
@endforeach
}


export { {{$file->getClassName()}} };
