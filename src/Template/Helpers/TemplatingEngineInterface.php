<?php

namespace CottaCush\Yii2\Template\Helpers;

/**
 * Interface TemplatingEngineInterface
 * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
 * @package CottaCush\Yii2\Template
 * @codeCoverageIgnore
 */
interface TemplatingEngineInterface
{
    public function renderTemplate($template, array $data);
}
