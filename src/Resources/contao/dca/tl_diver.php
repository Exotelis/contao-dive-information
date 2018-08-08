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

$GLOBALS['TL_DCA']['tl_diver'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'    => 'Table',
        'enableVersioning'  => true,
        'sql'               => array
        (
            'keys' => array
            (
                'id'    => 'primary',
                'email' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'          => 2,
            'fields'        => array('lastname', 'firstname'),
            'flag'          => 1,
            'panelLayout'   => 'filter;sort,search,limit'
        ),
        'label' => array
        (
            'fields'            => array('icon', 'lastname', 'firstname', 'status', 'start', 'stop'),
            'showColumns'       => true,
            'label_callback'    => array('tl_diver', 'updateLabel')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_diver']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.svg'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_diver']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_diver']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default'  => '{personal_legend},firstname,lastname,dateOfBirth,gender;{address_legend},street,postal,city;{contact_legend},phone,mobile,email;{dive_legend},status,brevet,nitrox,divecard;{account_legend},disable,interested,start,stop',
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                   => "int(10) unsigned NOT NULL default '0'"
        ),
        'lastname' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['lastname'],
            'exclude'               => true,
            'search'                => true,
            'sorting'               => true,
            'flag'                  => 1,
            'inputType'             => 'text',
            'eval'                  => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                   => "varchar(255) NOT NULL default ''"
        ),
        'firstname' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['firstname'],
            'exclude'               => true,
            'search'                => true,
            'sorting'               => true,
            'flag'                  => 1,
            'inputType'             => 'text',
            'eval'                  => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                   => "varchar(255) NOT NULL default ''"
        ),
        'gender' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['gender'],
            'exclude'               => true,
            'inputType'             => 'select',
            'options'               => array('male', 'female'),
            'reference'             => &$GLOBALS['TL_LANG']['tl_diver'],
            'eval'                  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(32) NOT NULL default ''"
        ),
        'dateOfBirth' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['dateOfBirth'],
            'exclude'               => true,
            'inputType'             => 'text',
            'eval'                  => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                   => "varchar(11) NOT NULL default ''"
        ),
        'street' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['street'],
            'exclude'               => true,
            'search'                => false,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                   => "varchar(255) NOT NULL default ''"
        ),
        'postal' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['postal'],
            'exclude'               => true,
            'search'                => false,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>32, 'tl_class'=>'w50'),
            'sql'                   => "varchar(32) NOT NULL default ''"
        ),
        'city' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['city'],
            'exclude'               => true,
            'search'                => true,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                   => "varchar(255) NOT NULL default ''"
        ),
        'phone' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['phone'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'mobile' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['mobile'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'email' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['email'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'rgxp'=>'email', 'unique'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'status' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['status'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'options'                 => array('active', 'passive', 'child'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_diver'],
            'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(10) NOT NULL default ''"
        ),
        'brevet' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['brevet'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>32, 'tl_class'=>'w50'),
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'nitrox' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['nitrox'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'options'                 => array('yes', 'no', 'trainer'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_diver'],
            'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(10) NOT NULL default ''"
        ),
        'divecard' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['divecard'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'select',
            'options'               => array('basic', 'family', 'professional'),
            'reference'             => &$GLOBALS['TL_LANG']['tl_diver'],
            'eval'                  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(32) NOT NULL default ''"

        ),
        'start' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['start'],
            'exclude'               => true,
            'sorting'               => true,
            'flag'                  => 9,
            'inputType'             => 'text',
            'eval'                  => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                   => "varchar(11) NOT NULL default ''"
        ),
        'stop' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['stop'],
            'exclude'               => true,
            'inputType'             => 'text',
            'eval'                  => array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                   => "varchar(11) NOT NULL default ''"
        ),
        'interested' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['interested'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'checkbox',
            'eval'                  => array('tl_class'=>'w50'),
            'sql'                   => "char(1) NOT NULL default ''"
        ),
        'disable' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['disable'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'checkbox',
            'eval'                  => array('tl_class'=>'w50'),
            'sql'                   => "char(1) NOT NULL default ''"
        )
    )
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Sebastian Krah <https://github.com/Exotelis>
 */
class tl_diver extends Contao\Backend
{
    /**
     * Add an image to each record
     * @param array         $row
     * @param string        $label
     * @param DataContainer $dc
     * @param array         $args
     *
     * @return array
     */
    public function updateLabel($row, $label, DataContainer $dc, $args)
    {
        /** Convert tstamp to label */
        if(!empty($row['start']))
        {
            $key = \array_search($row['start'], $args);
            if($key)
            {
                $args[$key] = Contao\Date::parse(Contao\Config::get('dateFormat'), $args[$key]);
            }
        }
        if(!empty($row['stop']))
        {
            $key = \array_search($row['stop'], $args);
            if($key)
            {
                $args[$key] = Contao\Date::parse(Contao\Config::get('dateFormat'), $args[$key]);
            }
        }

        /** Add icon */
        $image = 'member';
        $time = \Date::floorToMinute();
        $disabled = ($row['start'] !== '' && $row['start'] > $time) || ($row['stop'] !== '' && $row['stop'] < $time);
        if ($row['disable'] || $disabled)
        {
            $image .= '_';
        }
        $args[0] = sprintf('<div class="list_icon_new" style="background-image:url(\'%s\')" data-icon="%s.svg" data-icon-disabled="%s.svg">&nbsp;</div>', Image::getPath($image), $disabled ? $image : \rtrim($image, '_'), \rtrim($image, '_') . '_');

        return $args;
    }
}