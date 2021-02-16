<?php

namespace CottaCush\Yii2\Template\Helpers;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\Template;

/**
 * Append a countable suffix to a number.
 *
 * Usage:
 * ```handlebars
 *   {{appendCountableSuffix value suffix suffixPluralForm}}
 * ```
 *
 * Arguments:
 *  - "value": must be valid numeric value
 *
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
 * @codeCoverageIgnore
 */
class AppendCountableSuffixHelper implements HelperInterface
{

    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $arguments = $template->parseArguments($args);
        if (count($arguments) != 3) {
            throw new \InvalidArgumentException(
                '"appendCountableSuffix" helper expects exactly 3 arguments.'
            );
        }

        return self::appendCountableSuffix($context->get($arguments[0]), $arguments[1], $arguments[2]);
    }

    /**
     * @param $text
     * @param $suffix
     * @param $pluralForm
     * @return string
     */
    public static function appendCountableSuffix($text, $suffix, $pluralForm): string
    {
        if (is_null($text) || !is_numeric($text)) {
            return $text;
        }

        return ($text > 1) ? $text . ' ' . $pluralForm : $text . ' ' . $suffix;
    }
}
