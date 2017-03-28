<?php
namespace ZunderBolt\ShellAutoHelp;

use Cake\Console\ConsoleInputArgument;
use Cake\Console\ConsoleInputSubcommand;
use Cake\TestSuite\TestCase;

class TestShell1
{
    use AutoHelpTrait;

    /**
     * Test description
     * @param string $param1 Param1 description
     * @param int $param2 Param2 description
     */
    public function test($param1, $param2 = null)
    {

    }
}

class TestShell2
{
    use AutoHelpTrait;

    /**
     * TestShell2 description
     *
     *
     * @param \DateTimeImmutable $date Date description
     */
    public function main($date)
    {

    }

    public function anotherMethod()
    {

    }

    public function initialize()
    {

    }
}

class AutoHelpTraitTest extends TestCase
{
    public function testShouldHelpShell1()
    {
        $shell = new TestShell1();

        $parser = $shell->getOptionParser();
        $this->assertNull($parser->getDescription());
        $this->assertCount(1, $parser->subcommands());

        /**
         * @var ConsoleInputSubcommand $command
         */
        $command = $parser->subcommands()['test'];
        $this->assertEquals('Test description', $command->getRawHelp());
        $this->assertEquals('test', $command->name());

        $commandParser = $command->parser();
        $this->assertCount(2, $commandParser->arguments());

        /**
         * @var ConsoleInputArgument $param1
         */
        $param1 = $commandParser->arguments()[0];
        $this->assertEquals('param1(string) Param1 description', $param1->help());
        $this->assertTrue($param1->isRequired());

        /**
         * @var ConsoleInputArgument $param2
         */
        $param2 = $commandParser->arguments()[1];
        $this->assertEquals('param2(int) Param2 description <comment>(optional)</comment>', $param2->help());
        $this->assertFalse($param2->isRequired());
    }

    public function testShouldHelpShell2()
    {
        $shell = new TestShell2();

        $parser = $shell->getOptionParser();
        $this->assertEquals('TestShell2 description', $parser->getDescription());
        $this->assertCount(1, $parser->subcommands());
        $this->assertCount(1, $parser->arguments());

        /**
         * @var ConsoleInputArgument $param1
         */
        $param1 = $parser->arguments()[0];
        $this->assertEquals('date(\DateTimeImmutable) Date description', $param1->help());
        $this->assertTrue($param1->isRequired());
    }
}
