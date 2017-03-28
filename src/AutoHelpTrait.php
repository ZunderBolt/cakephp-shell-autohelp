<?php
namespace ZunderBolt\ShellAutoHelp;

use Cake\Console\ConsoleOptionParser;

/**
 * Class AutoHelpTrait
 * @package ZunderBolt\ShellAutoHelp
 * Autogenerates CakePHP3 console shell help
 */
trait AutoHelpTrait
{
    protected $_autoHelpSkippedMethods = ['getOptionParser', 'initialize', 'startup'];

    /**
     * Override to return common shell options for all methods
     * @return array
     */
    protected function _getShellOptions()
    {
        return [];
    }

    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = method_exists(get_parent_class(), 'getOptionParser')
            ? parent::getOptionParser()
            : new ConsoleOptionParser();

        $methods = $this->_getMethods();

        if (!empty($methods)) {
            $options = $this->_getShellOptions();
            foreach ($methods as $name => $data) {
                if ($name == 'main') {
                    $parser->addOptions($options);
                    $parser->addArguments($data['arguments']);
                    $parser->setDescription($data['description']);
                } else {
                    $parser->addSubcommand($name, [
                        'help' => $data['description'],
                        'parser' => [
                            'options' => $options,
                            'arguments' => $data['arguments'],
                            'description' => $data['description']
                        ]
                    ]);
                }
            }
        }
        return $parser;
    }

    /**
     * @return array
     */
    private function _getMethods()
    {
        $class = new \ReflectionClass(get_called_class());
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($methods, function ($method) use ($class) {
            return $method->class == $class->name && !in_array($method->name, $this->_autoHelpSkippedMethods);
        });
        if (!$methods) {
            return [];
        }
        $res = [];
        foreach ($methods as $method) {
            $arguments = [];
            $comment = $method->getDocComment();
            $description = $this->_getMethodDescription($comment);
            $params = $method->getParameters();
            if (!empty($params)) {
                $argsDescriptions = $this->_getArgumentsDescriptions($comment);
                foreach ($params as $param) {
                    $arguments[$param->name] = ['required' => !$param->isOptional()];
                    if (isset($argsDescriptions[$param->name])) {
                        $arguments[$param->name]['help'] = $argsDescriptions[$param->name];
                    }
                }
            }
            $res[$method->name] = compact('description', 'arguments');
        }
        return $res;
    }

    /**
     * @param string $comment
     * @return string
     */
    private function _getMethodDescription($comment)
    {
        $comment = str_replace(['/', '*', "\t"], '', $comment);
        $lines = array_map('trim', explode(PHP_EOL, trim($comment)));
        $lines = array_values(array_filter($lines));
        if (empty($lines)) {
            return '';
        }
        $i = 0;
        $description = [];
        do {
            $description[] = $lines[$i++];
        } while (isset($lines[$i]) && $lines[$i][0] != '@');
        return implode(' ', $description);
    }

    /**
     * @param string $comment
     * @return array
     */
    private function _getArgumentsDescriptions($comment)
    {
        $res = [];
        $comment = trim(preg_replace('/[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?/', '$1', $comment), "\r\n");
        $lines = array_filter(explode(PHP_EOL, $comment));
        foreach ($lines as $line) {
            $words = array_values(array_filter(array_map('trim', explode(' ', $line))));
            if (reset($words) != "@param") {
                continue;
            }
            $type = next($words);
            $name = str_replace('$', '', next($words));
            $description = implode(' ', array_slice($words, key($words) + 1));
            $res[$name] = "($type) $description";
        }
        return $res;
    }
}
