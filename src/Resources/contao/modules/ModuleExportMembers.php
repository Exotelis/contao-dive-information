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

namespace Contao;

/**
 * Back end module "Export Members".
 *
 * @author Sebastian Krah <https://github.com/Exotelis>
 */
class ModuleExportMembers extends BackendModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_exportmembers';

    /**
     * Initialize the object
     *
     * @param DataContainer $dc
     */
    public function __construct(DataContainer $dc = null)
    {
        parent::__construct($dc);
    }

    /**
     * Compile the current element
     */
    protected function compile()
    {
        $this->Template->content = 'Hello World';
        $this->Template->href = $this->getReferer(true);
        $this->Template->title = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
    }
}