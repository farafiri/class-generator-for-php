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
     * @var GeneratorAggregator
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
        $this->generator = new GeneratorAggregator();
    }

    /**
     * @param string  $cachePath   if this path will be set with $enableCache = false then class will be saved in file
     * @param boolean $enableCache
     */
    public function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $cachePath
     *
     * @return self
     */
    public function setCachePatch($cachePath)
    {
        $this->cachePath = $cachePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getCachePatch()
    {
        return $this->cachePath;
    }

    /**
     * @param boolean $enableCache
     *
     * @return self
     */
    public function setEnabledCache($enableCache)
    {
        $this->enableCache = $enableCache;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabledCache()
    {
        return $this->enableCache;
    }

    /**
     * @return \ClassGenerator\GeneratorAggregator
     */
    public function getGenerator()
    {
        return $this->generator;
    }



    /**
     * @param \ClassGenerator\GeneratorAggregator $generator
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
        return $this->cachePath . DIRECTORY_SEPARATOR . str_replace('\\', '__', $className) . '.php';
    }

    /**
     * register this autoloader
     *
     * @return self
     */
    public function register()
    {
        $f = array($this, 'load');
        if (!in_array($f, spl_autoload_functions())) {
            spl_autoload_register($f);
        }

        return $this;
    }
}
