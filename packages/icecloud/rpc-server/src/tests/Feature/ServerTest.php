<?php


namespace Tests\Unit;


use IceCloud\RpcServer\Lib\Exceptions\Server\InvalidProcedureInstanceException;
use IceCloud\RpcServer\Lib\Exceptions\Server\ProcedureNameConflictException;
use IceCloud\RpcServer\Lib\Mock\TestProcedure;
use IceCloud\RpcServer\Lib\Server;
use Tests\TestCase;

class ServerTest extends TestCase
{
    public function test_nameConflicts()
    {
        $p1 = new TestProcedure("MyService.V1.PersonalArea.Auth");
        $p2 = new TestProcedure("MyService.V1.PersonalArea");

        $server = new Server;

        try {
            $server->addProcedure(new \stdClass());
        } catch (InvalidProcedureInstanceException $exception) {
            $this->assertTrue(true);
        }

        try {
            $server->addProcedure($p1);
            $server->addProcedure($p2);
        } catch (ProcedureNameConflictException $exception) {
            $this->assertTrue(true);
        }

        $server = new Server;

        try {
            $server->addProcedure($p1);
            $server->addProcedure($p2);
        } catch (ProcedureNameConflictException $exception) {
            $this->assertTrue(true);
        }

        $server = new Server;
        $server->addProcedure($p1 = new TestProcedure('MyService.V1.PersonalArea.Auth'));
        $server->addProcedure($p2 = new TestProcedure('MyService.V1.Common.SendForm'));

        $true=$server->hasNamespaceExists('MyService.V1.PersonalArea');
        $this->assertTrue(
            $true
        );

        $false = $server->hasNamespaceExists('MyService.V1.PersonalArea.Auth');
        $this->assertFalse(
            $false
        );
    }
}
