<?php

namespace CottaCush\Yii2\Template;

use CottaCush\Yii2\Template\Helpers\HandleBarsHelpers;
use CottaCush\Yii2\Template\Helpers\TemplatingEngineInterface;
use Handlebars\Handlebars;

/**
 * Class HandlebarsTemplatingEngine
 * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
 * @package CottaCush\Yii2\Template
 * @codeCoverageIgnore
 */
class HandlebarsTemplatingEngine extends Handlebars implements TemplatingEngineInterface
{
    public function __construct(array $options = array())
    {
        if (!array_key_exists('helpers', $options)) {
            $options['helpers'] = new HandleBarsHelpers();
        }

        parent::__construct($options);
    }

    public function renderTemplate($template, array $data): string
    {
        return $this->render($template, $data);
    }
}
