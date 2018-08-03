<?php

/**
 * This file is part of exotelis/contao-dive-information
 *
 * Copyright (c) 2018-2018 Sebastian Krah
 *
 * @package   exotelis/contao-dive-information
 * @author    Sebastian Krah <exotelis@mailbox.org>
 * @copyright 2018-2018 Sebastian Krah
 * @license   https://github.com/Exotelis/contao-dive-information/blob/master/LICENSE LGPL-3.0
 */

declare(strict_types=1);

namespace Exotelis;

use Contao;

/**
 * Back end module "Export Members".
 *
 * @author Sebastian Krah <https://github.com/Exotelis>
 */
class ModuleExportMembers extends Contao\BackendModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_exportmembers';

    /**
     * Compile the current element
     */
    protected function compile()
    {
        Contao\System::loadLanguageFile('tl_exportmembers');

        $this->Template->href = $this->getReferer(true);
        $this->Template->title = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];

        $this->Template->headline = $GLOBALS['TL_LANG']['tl_exportmembers']['headline'];
        $this->Template->test = 'Hello World';
    }
}