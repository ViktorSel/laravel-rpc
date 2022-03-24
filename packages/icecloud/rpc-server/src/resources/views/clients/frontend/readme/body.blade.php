@php
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsProcedure;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelsCompilingResult;
use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use Illuminate\Support\Str;

/**
 * @var JsClassFile $clientFile
 * @var JsClassFile $methodsFile
 * @var JsInputModelsCompilingResult $inputs
 * @var JsGenerator $generator
 */

$procedures = $inputs->getJsProcedures();
$fp = $procedures[array_key_first($procedures)] ?? null;

@endphp

### RPC клиент проекта "{{ucfirst($generator->getProjectSlug())}}"
###### Протокол
> {{$generator->getServer()->getProtocolVersion()}}
@if($generator->getScopeSlug())
###### Пространство
> {{$generator->getScopeSlug()}}
@endif
###### Версия
> {{$generator->getVersionStorage()->getVersion()}}
###### Пакет
> {{$generator->getPackageName()}}

@if($fp)
Подключение и использование

```javascript
// Выполнять единожды при инициализации фреймворка/страницы

{{$clientFile->getClassName()}}.setup('[url точки подключения к серверу]');

// Пример выполнения процедуры {{$fp->getProcedure()->getFullQualifiedName()}}
@if($fp->getProcedure()->description())
    "{{$fp->getProcedure()->description()}}"
@endif

const promise = new {{$clientFile->getClassName()}}().Api.{{$fp->getProcedure()->getFullQualifiedNameWithoutService()}}(...);

promise
    // Успех
    .done(result => {
        console.log(result);
    })
    // Критическая ошибка выполнения
    .critical(error => {
        console.error("Rpc [critical]: ", error.getMessage());
    })
    // Неправильные входные аргументы
    .bad(error => {
        error.eachFirstErrors((name, message) => {
            console.warn("Rpc [bad]: <" + name + "> = ", message)
        });
    })
    // Ошибка авторизации
    .unauthorized(error => {
        console.warn("Rpc [unauthorized]: ", error.getMessage());
    })
    // Не удалось установить соединение
    .catch((error) => {
        console.error("Break connection with : ", error.message);
    })
    // Работаем по старинке. then получает доступ к RpcResponse напрямую и не отменяет чейн
    .then(response => {
        console.warn("Rpc [response]: ", response);
    });
```
@endif

### Процедуры

@foreach($procedures as $procedure)
#### {{$procedure->getProcedure()->getFullQualifiedName()}}

@if($procedure->getProcedure()->description())
    > {{$procedure->getProcedure()->description()}}
@endif


Пример Js вызова

```javascript
new {{$clientFile->getClassName()}}().Api.{{$procedure->getProcedure()->getFullQualifiedNameWithoutService()}}(@foreach($procedure->getArguments() as $arg){!! $arg->camel() !!}@if(!$loop->last), @endif()@endforeach())
```

Аргументы

Имя | Описание | Тип
--- | -------- | ---
@foreach($procedure->getArguments() as $arg)
{{$arg->camel()}} | {{$arg->comment()}} | {{$arg->type()}}
@endforeach

###### Тело ответа

```JSON
{!! json_encode($procedure->getProcedure()->getPipelineResultSchema()->exampleData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
```

@endforeach

### Формы (входные данные)

@foreach ($inputs->getFiles() as $file)
#### Класс ```{{$file->getClassName()}}```

Свойство | Описание | Тип | Обязателен
-------- | -------- | --- | ----------
@foreach($file->getProperties() as $property)
{{$property->name()}} | {{$property->comment()}} | {{$property->type()}} | {{$property->getDefinition()->isRequired() ? 'Да' : 'Нет'}}
@endforeach

###### Файл ```{{$file->getFilename()}}```

@endforeach
