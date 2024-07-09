<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\console;

use think\Console;
use think\console\input\Argument;
use think\console\input\Definition;
use think\console\input\Option;

class Command
{

    /** @var  Console */
    private $console;
    private $name;
    private $aliases = [];
    private $definition;
    private $help;
    private $description;
    private $ignoreValidationErrors          = false;
    private $consoleDefinitionMerged         = false;
    private $consoleDefinitionMergedWithArgs = false;
    private $code;
    private $synopsis = [];
    private $usages   = [];

    /** @var  Input */
    protected $input;

    /** @var  Output */
    protected $output;

    /**
     * 构造方法
     * @param string|null $name 命令名稱,如果没有設定则比如在 configure() 里設定
     * @throws \LogicException
     * @api
     */
    public function __construct($name = null)
    {
        $this->definition = new Definition();

        if (null !== $name) {
            $this->setName($name);
        }

        $this->configure();

        if (!$this->name) {
            throw new \LogicException(sprintf('The command defined in "%s" cannot have an empty name.', get_class($this)));
        }
    }

    /**
     * 忽略驗證錯誤
     */
    public function ignoreValidationErrors()
    {
        $this->ignoreValidationErrors = true;
    }

    /**
     * 設定控制台
     * @param Console $console
     */
    public function setConsole(Console $console = null)
    {
        $this->console = $console;
    }

    /**
     * 取得控制台
     * @return Console
     * @api
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * 是否有效
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * 配置指令
     */
    protected function configure()
    {
    }

    /**
     * 执行指令
     * @param Input  $input
     * @param Output $output
     * @return null|int
     * @throws \LogicException
     * @see setCode()
     */
    protected function execute(Input $input, Output $output)
    {
        throw new \LogicException('You must override the execute() method in the concrete command class.');
    }

    /**
     * 使用者驗證
     * @param Input  $input
     * @param Output $output
     */
    protected function interact(Input $input, Output $output)
    {
    }

    /**
     * 初始化
     * @param Input  $input  An InputInterface instance
     * @param Output $output An OutputInterface instance
     */
    protected function initialize(Input $input, Output $output)
    {
    }

    /**
     * 执行
     * @param Input  $input
     * @param Output $output
     * @return int
     * @throws \Exception
     * @see setCode()
     * @see execute()
     */
    public function run(Input $input, Output $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $this->getSynopsis(true);
        $this->getSynopsis(false);

        $this->mergeConsoleDefinition();

        try {
            $input->bind($this->definition);
        } catch (\Exception $e) {
            if (!$this->ignoreValidationErrors) {
                throw $e;
            }
        }

        $this->initialize($input, $output);

        if ($input->isInteractive()) {
            $this->interact($input, $output);
        }

        $input->validate();

        if ($this->code) {
            $statusCode = call_user_func($this->code, $input, $output);
        } else {
            $statusCode = $this->execute($input, $output);
        }

        return is_numeric($statusCode) ? (int) $statusCode : 0;
    }

    /**
     * 設定执行程式碼
     * @param callable $code callable(InputInterface $input, OutputInterface $output)
     * @return Command
     * @throws \InvalidArgumentException
     * @see execute()
     */
    public function setCode(callable $code)
    {
        if (!is_callable($code)) {
            throw new \InvalidArgumentException('Invalid callable provided to Command::setCode.');
        }

        if (PHP_VERSION_ID >= 50400 && $code instanceof \Closure) {
            $r = new \ReflectionFunction($code);
            if (null === $r->getClosureThis()) {
                $code = \Closure::bind($code, $this);
            }
        }

        $this->code = $code;

        return $this;
    }

    /**
     * 合并参數定义
     * @param bool $mergeArgs
     */
    public function mergeConsoleDefinition($mergeArgs = true)
    {
        if (null === $this->console
            || (true === $this->consoleDefinitionMerged
                && ($this->consoleDefinitionMergedWithArgs || !$mergeArgs))
        ) {
            return;
        }

        if ($mergeArgs) {
            $currentArguments = $this->definition->getArguments();
            $this->definition->setArguments($this->console->getDefinition()->getArguments());
            $this->definition->addArguments($currentArguments);
        }

        $this->definition->addOptions($this->console->getDefinition()->getOptions());

        $this->consoleDefinitionMerged = true;
        if ($mergeArgs) {
            $this->consoleDefinitionMergedWithArgs = true;
        }
    }

    /**
     * 設定参數定义
     * @param array|Definition $definition
     * @return Command
     * @api
     */
    public function setDefinition($definition)
    {
        if ($definition instanceof Definition) {
            $this->definition = $definition;
        } else {
            $this->definition->setDefinition($definition);
        }

        $this->consoleDefinitionMerged = false;

        return $this;
    }

    /**
     * 取得参數定义
     * @return Definition
     * @api
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * 取得当前指令的参數定义
     * @return Definition
     */
    public function getNativeDefinition()
    {
        return $this->getDefinition();
    }

    /**
     * 新增参數
     * @param string $name        名稱
     * @param int    $mode        类型
     * @param string $description 描述
     * @param mixed  $default     默认值
     * @return Command
     */
    public function addArgument($name, $mode = null, $description = '', $default = null)
    {
        $this->definition->addArgument(new Argument($name, $mode, $description, $default));

        return $this;
    }

    /**
     * 新增选项
     * @param string $name        选项名稱
     * @param string $shortcut    别名
     * @param int    $mode        类型
     * @param string $description 描述
     * @param mixed  $default     默认值
     * @return Command
     */
    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        $this->definition->addOption(new Option($name, $shortcut, $mode, $description, $default));

        return $this;
    }

    /**
     * 設定指令名稱
     * @param string $name
     * @return Command
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        $this->validateName($name);

        $this->name = $name;

        return $this;
    }

    /**
     * 取得指令名稱
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 設定描述
     * @param string $description
     * @return Command
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     *  取得描述
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 設定帮助訊息
     * @param string $help
     * @return Command
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * 取得帮助訊息
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * 描述訊息
     * @return string
     */
    public function getProcessedHelp()
    {
        $name = $this->name;

        $placeholders = [
            '%command.name%',
            '%command.full_name%',
        ];
        $replacements = [
            $name,
            $_SERVER['PHP_SELF'] . ' ' . $name,
        ];

        return str_replace($placeholders, $replacements, $this->getHelp());
    }

    /**
     * 設定别名
     * @param string[] $aliases
     * @return Command
     * @throws \InvalidArgumentException
     */
    public function setAliases($aliases)
    {
        if (!is_array($aliases) && !$aliases instanceof \Traversable) {
            throw new \InvalidArgumentException('$aliases must be an array or an instance of \Traversable');
        }

        foreach ($aliases as $alias) {
            $this->validateName($alias);
        }

        $this->aliases = $aliases;

        return $this;
    }

    /**
     * 取得别名
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * 取得简介
     * @param bool $short 是否简單的
     * @return string
     */
    public function getSynopsis($short = false)
    {
        $key = $short ? 'short' : 'long';

        if (!isset($this->synopsis[$key])) {
            $this->synopsis[$key] = trim(sprintf('%s %s', $this->name, $this->definition->getSynopsis($short)));
        }

        return $this->synopsis[$key];
    }

    /**
     * 新增用法介绍
     * @param string $usage
     * @return $this
     */
    public function addUsage($usage)
    {
        if (0 !== strpos($usage, $this->name)) {
            $usage = sprintf('%s %s', $this->name, $usage);
        }

        $this->usages[] = $usage;

        return $this;
    }

    /**
     * 取得用法介绍
     * @return array
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * 驗證指令名稱
     * @param string $name
     * @throws \InvalidArgumentException
     */
    private function validateName($name)
    {
        if (!preg_match('/^[^\:]++(\:[^\:]++)*$/', $name)) {
            throw new \InvalidArgumentException(sprintf('Command name "%s" is invalid.', $name));
        }
    }
}
