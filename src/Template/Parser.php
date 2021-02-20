<?php

namespace CottaCush\Yii2\Template;

use ArrayIterator;
use LogicException;

class Parser extends \Handlebars\Parser
{
    /**
     * Process array of tokens and convert them into parse tree
     *
     * @param array $tokens Set of
     *
     * @return array Token parse tree
     */
    public function parse(array $tokens = array()): array
    {
        return $this->_buildTree(new ArrayIterator($tokens));
    }

    /**
     * Helper method for recursively building a parse tree.
     * Trim right and trim left is a bit tricky here.
     * {{#begin~}}{{TOKEN}}, TOKEN.. {{LAST}}{{~/begin}} is translated to:
     * {{#begin}}{{~TOKEN}}, TOKEN.. {{LAST~}}{{/begin}}
     *
     * @param ArrayIterator $tokens Stream of tokens
     *
     * @throws LogicException when nesting errors or mismatched section tags
     * are encountered.
     * @return array Token parse tree
     */
    private function _buildTree(ArrayIterator $tokens): array
    {
        $stack = array();

        do {
            $token = $tokens->current();
            $tokens->next();

            if ($token !== null) {
                switch ($token[Tokenizer::TYPE]) {
                    case Tokenizer::T_END_SECTION:
                        $newNodes = array($token);
                        do {
                            $result = array_pop($stack);
                            if ($result === null) {
                                throw new LogicException(
                                    sprintf(
                                        'Unexpected closing tag: /%s',
                                        $token[Tokenizer::NAME]
                                    )
                                );
                            }

                            if (!array_key_exists(Tokenizer::NODES, $result)
                                && isset($result[Tokenizer::NAME])
                                && ($result[Tokenizer::TYPE] == Tokenizer::T_SECTION
                                    || $result[Tokenizer::TYPE] == Tokenizer::T_INVERTED)
                                && $result[Tokenizer::NAME] == $token[Tokenizer::NAME]
                            ) {
                                if (isset($result[Tokenizer::TRIM_RIGHT])
                                    && $result[Tokenizer::TRIM_RIGHT]
                                ) {
                                    // If the start node has trim right, then its equal
                                    //with the first item in the loop with
                                    // Trim left
                                    $newNodes[0][Tokenizer::TRIM_LEFT] = true;
                                }

                                if (isset($token[Tokenizer::TRIM_RIGHT])
                                    && $token[Tokenizer::TRIM_RIGHT]
                                ) {
                                    //OK, if we have trim right here, we should
                                    //pass it to the upper level.
                                    $result[Tokenizer::TRIM_RIGHT] = true;
                                }

                                $result[Tokenizer::NODES] = $newNodes;
                                $result[Tokenizer::END] = $token[Tokenizer::INDEX];
                                array_push($stack, $result);
                                break;
                            } else {
                                array_unshift($newNodes, $result);
                            }
                        } while (true);
                        break;
                    default:
                        array_push($stack, $token);
                }
            }

        } while ($tokens->valid());

        return $stack;
    }
}
