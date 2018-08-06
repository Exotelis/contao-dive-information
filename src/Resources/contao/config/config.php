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

// Backend Module
array_insert($GLOBALS['BE_MOD']['accounts'], 2, array
(
    'diver' => array
    (
        'tables' => array('tl_diver')
    )
));
array_insert($GLOBALS['BE_MOD']['accounts'], 3, array
(
    'exportdiver' => array
    (
        'callback'   => 'Exotelis\ModuleExportDiver',
        'stylesheet' => 'bundles/exoteliscontaodiveinformation/diver.css'
    )
));