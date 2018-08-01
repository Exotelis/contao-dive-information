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

// Add palette
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace('gender;', 'gender;{dive_legend},membershipStatus,brevet,nitrox,divecard;', $GLOBALS['TL_DCA']['tl_member']['palettes']['default']);

// Add field
$GLOBALS['TL_DCA']['tl_member']['fields']['membershipStatus'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['membershipStatus'],
    'exclude'                 => true,
    'search'                  => true,
    'sorting'                 => true,
    'filter'                  => true,
    'flag'                    => 1,
    'inputType'               => 'select',
    'options'                 => array('active', 'passive', 'child'),
    'reference'               => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                    => array('includeBlankOption'=>true, 'feEditable'=>false, 'feViewable'=>false, 'feGroup'=>'personal', 'tl_class'=>'w50'),
    'sql'                     => "varchar(10) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['brevet'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['brevet'],
    'exclude'                 => true,
    'search'                  => true,
    'sorting'                 => true,
    'filter'                  => true,
    'flag'                    => 1,
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>32, 'feEditable'=>false, 'feViewable'=>false, 'feGroup'=>'personal', 'tl_class'=>'w50'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['nitrox'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['nitrox'],
    'exclude'                 => true,
    'search'                  => true,
    'sorting'                 => true,
    'filter'                  => true,
    'flag'                    => 1,
    'inputType'               => 'select',
    'options'                 => array('yes', 'no'),
    'reference'               => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                    => array('includeBlankOption'=>true, 'feEditable'=>false, 'feViewable'=>false, 'feGroup'=>'personal', 'tl_class'=>'w50'),
    'sql'                     => "varchar(5) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['divecard'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['divecard'],
    'exclude'                 => true,
    'search'                  => true,
    'sorting'                 => true,
    'flag'                    => 1,
    'inputType'               => 'text',
    'eval'                    => array('maxlength'=>32, 'feEditable'=>false, 'feViewable'=>false, 'feGroup'=>'personal', 'tl_class'=>'w50'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);
