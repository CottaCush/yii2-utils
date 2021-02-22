<?php

namespace CottaCush\Yii2\Template;

use Handlebars\Arguments;
use Handlebars\Context;
use Handlebars\SafeString;
use Handlebars\StringWrapper;
use RuntimeException;

class Template extends \Handlebars\Template
{
    /**
     * Process section
     *
     * @param Context $context current context
     * @param array $current section node data
     * @param boolean $escaped escape result or not
     *
     * @return string|SafeString the result
     */
    protected function _getSection(Context $context, array $current, bool $escaped): string|SafeString
    {
        $args = explode(' ', $current[Tokenizer::NAME], 2);
        $name = array_shift($args);
        $current[Tokenizer::NAME] = $name;
        $current[Tokenizer::ARGS] = implode(' ', $args);
        $result = $this->_section($context, $current);

        if ($escaped && !($result instanceof SafeString)) {
            $escape_args = $this->handlebars->getEscapeArgs();
            array_unshift($escape_args, $result);
            $result = call_user_func_array(
                $this->handlebars->getEscape(),
                array_values($escape_args)
            );
        }

        return $result;
    }

    /**
     * Process variable
     *
     * @param Context $context current context
     * @param array $current section node data
     * @param boolean $escaped escape result or not
     *
     * @return string the result
     */
    protected function _getVariable(Context $context, array $current, bool $escaped): string
    {
        $name = $current[Tokenizer::NAME];
        $value = $context->get($name);
        if (is_array($value)) {
            return 'Array';
        }
        if ($escaped && !($value instanceof SafeString)) {
            $args = $this->handlebars->getEscapeArgs();
            array_unshift($args, (string)$value);
            $value = call_user_func_array(
                $this->handlebars->getEscape(),
                array_values($args)
            );
        }

        return (string)$value;
    }

    /**
     * Prepare the arguments of a partial to actual array values to be used in a new context
     *
     * @param Context   $context   Current context
     * @param Arguments $arguments Arguments for partial
     *
     * @return array
     */
    protected function _preparePartialArguments(Context $context, Arguments $arguments): array
    {
        $positionalArgs = array();
        foreach ($arguments->getPositionalArguments() as $positionalArg) {
            $contextArg = $context->get($positionalArg);
            if (is_array($contextArg)) {
                foreach ($contextArg as $key => $value) {
                    $positionalArgs[$key] = $value;
                }
            } else {
                $positionalArgs[$positionalArg] = $contextArg;
            }
        }

        $namedArguments = array();
        foreach ($arguments->getNamedArguments() as $key => $value) {
            $namedArguments[$key] = $context->get($value);
        }

        return array_merge($positionalArgs, $namedArguments);
    }


    /**
     * Check if there is a helper with this variable name available or not.
     *
     * @param array $current current token
     *
     * @return boolean
     */
    protected function _isSection(array $current): bool
    {
        $helpers = $this->getEngine()->getHelpers();
        // Tokenizer doesn't process the args -if any- so be aware of that
        $name = explode(' ', $current[Tokenizer::NAME], 2);

        return $helpers->has(reset($name));
    }

    /**
     * Get replacing value of a tag
     *
     * Will process the tag as section, if a helper with the same name could be
     * found, so {{helper arg}} can be used instead of {{#helper arg}}.
     *
     * @param Context $context current context
     * @param array $current section node data
     * @param boolean $escaped escape result or not
     *
     * @return string|SafeString the string to be replaced with the tag
     */
    protected function _get(Context $context, array $current, bool $escaped): string|SafeString
    {
        if ($this->_isSection($current)) {
            return $this->_getSection($context, $current, $escaped);
        } else {
            return $this->_getVariable($context, $current, $escaped);
        }
    }

    /**
     * Process inverted section
     *
     * @param Context $context current context
     * @param array $current section node data
     *
     * @return string the result
     */
    protected function _inverted(Context $context, array $current): string
    {
        $sectionName = $current[Tokenizer::NAME];
        $data = $context->get($sectionName);
        if (!$data) {
            return $this->render($context);
        } else {
            //No need to discard here, since it has no else
            return '';
        }
    }

    /**
     * Process section nodes
     *
     * @param Context $context current context
     * @param array $current section node data
     *
     * @return string the result
     * @throws RuntimeException
     */
    protected function _section(Context $context, array $current): string
    {
        $helpers = $this->handlebars->getHelpers();
        $sectionName = $current[Tokenizer::NAME];
        if ($helpers->has($sectionName)) {
            return $this->_handlebarsStyleSection($context, $current);
        } elseif (trim($current[Tokenizer::ARGS]) == '') {
            return $this->_mustacheStyleSection($context, $current);
        } else {
            throw new RuntimeException(
                sprintf(
                    '"%s"" is not registered as a helper',
                    $sectionName
                )
            );
        }
    }

    /**
     * Process handlebars section style
     *
     * @param Context $context current context
     * @param array $current section node data
     *
     * @return mixed
     */
    protected function _handlebarsStyleSection(Context $context, array $current): mixed
    {
        $helpers = $this->handlebars->getHelpers();
        $sectionName = $current[Tokenizer::NAME];

        if (isset($current[Tokenizer::END])) {
            $source = substr(
                $this->getSource(),
                $current[Tokenizer::INDEX],
                $current[Tokenizer::END] - $current[Tokenizer::INDEX]
            );
        } else {
            $source = '';
        }

        // subexpression parsing loop
        // will contain all subexpressions
        // inside outermost brackets
        $subexprs = array();
        $insideOf = array( 'single' => false, 'double' => false );
        $lvl = 0;
        $cur_start = 0;
        for ($i=0; $i < strlen($current[Tokenizer::ARGS]); $i++) {
            $cur = substr($current[Tokenizer::ARGS], $i, 1);
            if ($cur == "'") {
                $insideOf['single'] = ! $insideOf['single'];
            }
            if ($cur == '"') {
                $insideOf['double'] = ! $insideOf['double'];
            }
            if ($cur == '(' && ! $insideOf['single'] && ! $insideOf['double']) {
                if ($lvl == 0) {
                    $cur_start = $i+1;
                }
                $lvl++;
                continue;
            }
            if ($cur == ')' && ! $insideOf['single'] && ! $insideOf['double']) {
                $lvl--;
                if ($lvl == 0) {
                    $subexprs[] = substr(
                        $current[Tokenizer::ARGS],
                        $cur_start,
                        $i - $cur_start
                    );
                }
            }
        }

        if (! empty($subexprs)) {
            foreach ($subexprs as $expr) {
                $cmd = explode(" ", $expr);
                $name = trim($cmd[0]);
                // construct artificial section node
                $section_node = array(
                    Tokenizer::TYPE => Tokenizer::T_ESCAPED,
                    Tokenizer::NAME => $name,
                    Tokenizer::OTAG => $current[Tokenizer::OTAG],
                    Tokenizer::CTAG => $current[Tokenizer::CTAG],
                    Tokenizer::INDEX => $current[Tokenizer::INDEX],
                    Tokenizer::ARGS => implode(" ", array_slice($cmd, 1))
                );

                // resolve the node recursively
                $resolved = $this->_handlebarsStyleSection(
                    $context,
                    $section_node
                );

                $resolved = addcslashes($resolved, '"');
                // replace original subexpression with result
                $current[Tokenizer::ARGS] = str_replace(
                    '('.$expr.')',
                    '"' . $resolved . '"',
                    $current[Tokenizer::ARGS]
                );
            }
        }

        $return = $helpers->call(
            $sectionName,
            $this,
            $context,
            $current[Tokenizer::ARGS],
            $source
        );

        if ($return instanceof StringWrapper) {
            return $this->handlebars->loadString($return)->render($context);
        } else {
            return $return;
        }
    }

    /**
     * Process Mustache section style
     *
     * @param Context $context current context
     * @param array $current section node data
     *
     * @return string
     */
    protected function _mustacheStyleSection(Context $context, array $current): string
    {
        $sectionName = $current[Tokenizer::NAME];

        // fallback to mustache style each/with/for just if there is
        // no argument at all.
        try {
            $sectionVar = $context->get($sectionName, false);
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException(
                sprintf(
                    '"%s" is not registered as a helper',
                    $sectionName
                )
            );
        }
        $buffer = '';
        if (is_array($sectionVar) || $sectionVar instanceof \Traversable) {
            $isList = is_array($sectionVar) &&
                (array_keys($sectionVar) === range(0, count($sectionVar) - 1));
            $index = 0;
            $lastIndex = $isList ? (count($sectionVar) - 1) : false;

            foreach ($sectionVar as $key => $d) {
                $specialVariables = array(
                    '@index' => $index,
                    '@first' => ($index === 0),
                    '@last' => ($index === $lastIndex),
                );
                if (!$isList) {
                    $specialVariables['@key'] = $key;
                }
                $context->pushSpecialVariables($specialVariables);
                $context->push($d);
                $buffer .= $this->render($context);
                $context->pop();
                $context->popSpecialVariables();
                $index++;
            }
        } elseif (is_object($sectionVar)) {
            //Act like with
            $context->push($sectionVar);
            $buffer = $this->render($context);
            $context->pop();
        } elseif ($sectionVar) {
            $buffer = $this->render($context);
        }

        return $buffer;
    }

    /**
     * Process partial section
     *
     * @param Context $context current context
     * @param array $current section node data
     *
     * @return string the result
     */
    protected function _partial(Context $context, array $current): string
    {
        $partial = $this->handlebars->loadPartial($current[Tokenizer::NAME]);

        if ($current[Tokenizer::ARGS]) {
            $arguments = new Arguments($current[Tokenizer::ARGS]);

            $context = new Context($this->_preparePartialArguments($context, $arguments));
        }

        return $partial->render($context);
    }

    /**
     * Render tokens base on type of tokens
     *
     * @param array $current current token
     * @param mixed $context current context
     *
     * @return string
     */
    protected function renderInternal($current, mixed $context): string
    {
        $result = '';
        switch ($current[Tokenizer::TYPE]) {
            case Tokenizer::T_END_SECTION:
                break; // Its here just for handling whitespace trim.
            case Tokenizer::T_SECTION:
                $newStack = isset($current[Tokenizer::NODES])
                    ? $current[Tokenizer::NODES] : array();
                array_push($this->stack, array(0, $newStack, false));
                $result = $this->_section($context, $current);
                array_pop($this->stack);
                break;
            case Tokenizer::T_INVERTED:
                $newStack = isset($current[Tokenizer::NODES]) ?
                    $current[Tokenizer::NODES] : array();
                array_push($this->stack, array(0, $newStack, false));
                $result = $this->_inverted($context, $current);
                array_pop($this->stack);
                break;
            case Tokenizer::T_COMMENT:
                $result = '';
                break;
            case Tokenizer::T_PARTIAL:
            case Tokenizer::T_PARTIAL_2:
                $result = $this->_partial($context, $current);
                break;
            case Tokenizer::T_UNESCAPED:
            case Tokenizer::T_UNESCAPED_2:
                $result = $this->_get($context, $current, false);
                break;
            case Tokenizer::T_ESCAPED:
                $result = $this->_get($context, $current, true);
                break;
            case Tokenizer::T_TEXT:
                $result = $current[Tokenizer::VALUE];
                break;
            /* How we could have another type of token? this part of code
            is not used at all.
            default:
                throw new \RuntimeException(
                    'Invalid node type : ' . json_encode($current)
                );
            */
        }

        return $result;
    }

    /**
     * Render top tree
     *
     * @param mixed $context current context
     *
     * @return string
     * @throws RuntimeException
     */
    public function render(mixed $context): string
    {
        if (!$context instanceof Context) {
            $context = new Context($context);
        }
        $topTree = end($this->stack); // never pop a value from stack
        list($index, $tree, $stop) = $topTree;

        $buffer = '';
        $rTrim = false;
        while (array_key_exists($index, $tree)) {
            $current = $tree[$index];
            $index++;
            //if the section is exactly like waitFor
            if (is_string($stop)
                && $current[Tokenizer::TYPE] == Tokenizer::T_ESCAPED
                && $current[Tokenizer::NAME] === $stop
            ) {
                break;
            }
            if (isset($current[Tokenizer::TRIM_LEFT])
                && $current[Tokenizer::TRIM_LEFT]
            ) {
                $buffer = rtrim($buffer);
            }

            $tmp = $this->renderInternal($current, $context);

            if (isset($current[Tokenizer::TRIM_LEFT])
                && $current[Tokenizer::TRIM_LEFT]
            ) {
                $tmp = rtrim($tmp);
            }

            if ($rTrim
                || (isset($current[Tokenizer::TRIM_RIGHT])
                    && $current[Tokenizer::TRIM_RIGHT])
            ) {
                $tmp = ltrim($tmp);
            }

            $buffer .= $tmp;
            // Some time, there is more than
            //one string token (first is empty),
            //so we need to trim all of them in one shot

            $rTrim = (empty($tmp) && $rTrim) ||
                isset($current[Tokenizer::TRIM_RIGHT])
                && $current[Tokenizer::TRIM_RIGHT];
        }
        if ($stop) {
            //Ok break here, the helper should be aware of this.
            $newStack = array_pop($this->stack);
            $newStack[0] = $index;
            $newStack[2] = false; //No stop token from now on
            array_push($this->stack, $newStack);
        }

        return $buffer;
    }
}
