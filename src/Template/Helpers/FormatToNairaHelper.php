<?php

namespace CottaCush\Yii2\Template\Helpers;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\Template;

/**
 * Format number to Nigerian Naira.
 *
 * Usage:
 * ```handlebars
 *   {{formatToNaira value}}
 * ```
 *
 * Arguments:
 *  - "value": must be valid numeric value
 *
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
 * @codeCoverageIgnore
 */
class FormatToNairaHelper implements HelperInterface
{

    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $arguments = $template->parseArguments($args);
        if (count($arguments) != 1) {
            throw new \InvalidArgumentException(
                '"formatToNaira" helper expects an argument.'
            );
        }

        return self::formatToNaira($context->get($arguments[0]));
    }

    /**
     * @param $amount
     * @return string
     */
    public static function formatToNaira($amount): string
    {
        if (is_null($amount) || !is_numeric($amount)) {
            return $amount;
        }

        return number_format($amount, 2) . 'NGN';
    }
}
