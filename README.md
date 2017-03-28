# Shell-auto-help plugin for CakePHP

Small trait to autogenerate CakePHP3 console shells help. No need to write ```$parser->addArgument()``` and stuff, 
just add phpdoc to your shell methods.   


#### Installation
```
composer require zunderbolt/cakephp-shell-autohelp
```

#### Usage

```php
namespace App\Shell;

use Cake\Console\Shell;
use ZunderBolt\ShellAutoHelp\AutoHelpTrait;

class MyShell extends Shell
{
    use AutoHelpTrait;

    /**
     * Method description will be used in help
     * @param string $param1 Param1 description
     * @param mixed $param2 Param2 description
     */
    public function test($param1, $param2 = null)
    {
    }
}
```

`> bin/cake my`
```
Usage:
cake my [subcommand] [-h] [-q] [-v]

Subcommands:

test  Method description will be used in help
```

`> bin/cake my test --help`
```
Usage:
cake my test [-h] [-q] [-v] <param1> [<param2>]

Options:

--help, -h     Display this help.
--quiet, -q    Enable quiet output.
--verbose, -v  Enable verbose output.

Arguments:

param1  (string) Param1 description
param2  (mixed) Param2 description (optional)
```
