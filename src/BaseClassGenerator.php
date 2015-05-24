<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 24.05.15
 * Time: 11:17
 */

namespace ClassGenerator;


abstract class BaseClassGenerator {
    protected $templateClassGenerator;
    protected $generatorAggregator = null;

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        if (!isset($this->template)) {
            $this->template = file_get_contents($this->getTemplateFile());
        }

        return $this->template;
    }

    public function setGeneratorAggregator($aggregator)
    {
        $this->generatorAggregator = $aggregator;
    }

    public function getGeneratorAggregator()
    {
        return $this->generatorAggregator;
    }

    /**
     * @param string $className class or interface name
     *
     * @return string|null
     */
    abstract public function generateCodeFor($className);
} 