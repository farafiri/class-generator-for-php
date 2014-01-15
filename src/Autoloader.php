<?php

namespace ClassGenerator;


class Autoloader
{
    /**
     * @var \ClassGenerator\Autoloader
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var boolean
     */
    protected $enableCache;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @param string  $cachePath   if this path will be set with $enableCache = false then class will be saved in file
     * @param boolean $enableCache
     */
    protected function __construct($cachePath = '', $enableCache = false)
    {
        $this->cachePath = $cachePath;
        $this->enableCache = $enableCache;
        $this->generator = new Generator();
        $this->register();
    }

    /**
     * @param string  $cachePath   if this path will be set with $enableCache = false then class will be saved in file
     * @param boolean $enableCache
     */
    public function getInstance($cachePath = '', $enableCache = false)
    {
        if (empty(self::$instance)) {
            self::$instance = new self($cachePath, $enableCache);
        }

        return self::$instance;
    }

    /**
     * @return \ClassGenerator\Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @param \ClassGenerator\Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param string $className
     */
    public function load($className)
    {
        if ($this->cachePath) {
            $cacheFile = $this->getFileNameForClass($className);
            if (!file_exists($cacheFile) || !$this->enableCache) {
                file_put_contents($cacheFile, "<?php\n" . $this->generator->generateCode($className));
            }

            require_once($cacheFile);
        } else {
            eval($this->generator->generateCode($className));
        }
    }

    /**
     * returns path to cache file for given class
     *
     * @param string $className
     * @return string
     */
    protected function getFileNameForClass($className)
    {
        return $this->cachePath . DIRECTORY_SEPARATOR . str_replace('\\', '__', $className);
    }

    /**
     * register this autoloader
     */
    protected function register()
    {
        spl_autoload_register(array($this, 'load'));
    }
} 