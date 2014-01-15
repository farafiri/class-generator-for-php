<?php

namespace ClassGenerator;


class Autoloader
{
    protected static $instance;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @param string $cachePath
     */
    protected function __construct($cachePath = '')
    {
        $this->cachePath = $cachePath;
        $this->generator = new Generator();
        $this->register();
    }

    public function getInstance($cachePath = '')
    {
        if (empty(self::$instance)) {
            self::$instance = new self($cachePath);
        }

        return self::$instance;
    }

    public function getGenerator()
    {
        return $this->generator;
    }

    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    public function load($className)
    {
        //echo "\nload: $className\n";
        if ($this->cachePath) {
            $cacheFile = $this->getFileNameForClass($className);
            //if (!file_exists($cacheFile)) {
                file_put_contents($cacheFile, "<?php\n" . $this->generator->generateCode($className));
            //}

            require_once($cacheFile);
        } else {
            echo $this->generator->generateCode($className);
            eval($this->generator->generateCode($className));
        }
    }

    protected function getFileNameForClass($className)
    {
        return $this->cachePath . DIRECTORY_SEPARATOR . str_replace('\\', '__', $className);
    }

    protected function register()
    {
        spl_autoload_register(array($this, 'load'));
    }
} 