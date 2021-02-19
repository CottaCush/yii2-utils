<?php

namespace CottaCush\Yii2\Template;

use Handlebars\Handlebars as XaminHandlebars;

class Handlebars extends XaminHandlebars
{
    /**
     * Current tokenizer instance
     *
     * @var Tokenizer
     */
    protected $_tokenizer;
    protected $_parser;
    protected $_ttl;

    /**
     * @var string the class to use for the template
     */
    protected $_templateClass = 'CottaCush\\Yii2\\Template\\Template';

    protected function _tokenize($source)
    {
        $hash = md5(sprintf('version: %s, data : %s', self::VERSION, $source));
        $tree = $this->getCache()->get($hash);
        if ($tree === false) {
            $tokens = $this->getTokenizer()->scan($source);
            $tree = $this->getParser()->parse($tokens);
            $this->getCache()->set($hash, $tree, $this->_ttl);
        }

        return $tree;
    }

    /**
     * Get the current Handlebars Tokenizer instance.
     *
     * If no Tokenizer instance has been explicitly specified, this method will
     * instantiate and return a new one.
     *
     * @return Tokenizer
     */
    public function getTokenizer(): Tokenizer
    {
        if (!isset($this->_tokenizer)) {
            $this->_tokenizer = new Tokenizer();
        }

        return $this->_tokenizer;
    }

    /**
     * Get the current Handlebars Parser instance.
     *
     * If no Parser instance has been explicitly specified, this method will
     * instantiate and return a new one.
     *
     * @return Parser
     */
    public function getParser(): Parser
    {
        if (!isset($this->_parser)) {
            $this->_parser = new Parser();
        }

        return $this->_parser;
    }

    /**
     * Load a template by name with current template loader
     *
     * @param string $name template name
     *
     * @return Template
     */
    public function loadTemplate($name): Template
    {
        $source = $this->getLoader()->load($name);
        $tree = $this->_tokenize($source);

        return new $this->_templateClass($this, $tree, $source);
    }

    /**
     * Shortcut 'render' invocation.
     *
     * Equivalent to calling `$handlebars->loadTemplate($template)->render($data);`
     *
     * @param string $template template name
     * @param mixed  $data     data to use as context
     *
     * @return string Rendered template
     * @see    Handlebars::loadTemplate
     * @see    Template::render
     */
    public function render($template, $data): string
    {
        return $this->loadTemplate($template)->render($data);
    }
}
