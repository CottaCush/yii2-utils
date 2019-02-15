<?php

namespace CottaCush\Yii2\Template\Helpers;

use JustBlackBird\HandlebarsHelpers\Helpers as JustBlackBirdHelpers;

/**
 * Class HandleBarsHelpers
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Kehinde Ladipo <kehinde.ladipo@cottacush.com>
 * @package CottaCush\Yii2\Template
 * @codeCoverageIgnore
 */
class HandleBarsHelpers extends JustBlackBirdHelpers
{
    protected function addDefaultHelpers()
    {
        parent::addDefaultHelpers();

        $this->add('formatToNaira', new FormatToNairaHelper());
        $this->add('appendCountableSuffix', new AppendCountableSuffixHelper());
    }
}
