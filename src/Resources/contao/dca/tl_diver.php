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
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'onload_callback'   => array
        (
            array('Exotelis\Util', 'disableMembership'),
            array('Exotelis\Newsletter', 'updateAccount')
        ),
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
            'fields'            => array('icon', 'lastname', 'firstname', 'status', 'divecard', 'start', 'stop'),
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
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_diver']['toggle'],
                'icon'                => 'visible.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('tl_diver', 'toggleIcon')
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
        'default'  => '{personal_legend},firstname,lastname,dateOfBirth,gender;{address_legend},street,postal,city;{contact_legend},phone,mobile,email;{dive_legend},status,brevet,nitrox,divecard;{newsletter_legend},newsletter;{account_legend},disable,interested,start,stop,canceledTo',
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['id'],
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
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['phone'],
            'exclude'               => true,
            'search'                => true,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(64) NOT NULL default ''"
        ),
        'mobile' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['mobile'],
            'exclude'               => true,
            'search'                => true,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(64) NOT NULL default ''"
        ),
        'email' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['email'],
            'exclude'               => true,
            'search'                => true,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>255, 'rgxp'=>'email', 'unique'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(255) NOT NULL default ''",
            'save_callback'         => array
            (
                array('tl_diver', 'checkEmailNewsletter'),
                array('tl_diver', 'updateEmailOnChange')
            )
        ),
        'status' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['status'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'select',
            'options'               => array('active', 'partner', 'ptsactive', 'familypm', 'familysm', 'familye', 'passive', 'ptspassive', 'child', 'trial'),
            'reference'             => &$GLOBALS['TL_LANG']['tl_diver'],
            'eval'                  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(10) NOT NULL default ''"
        ),
        'brevet' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['brevet'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'text',
            'eval'                  => array('maxlength'=>32, 'tl_class'=>'w50'),
            'sql'                   => "varchar(32) NOT NULL default ''"
        ),
        'nitrox' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['nitrox'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'select',
            'options'               => array('yes', 'no', 'trainer'),
            'reference'             => &$GLOBALS['TL_LANG']['tl_diver'],
            'eval'                  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                   => "varchar(10) NOT NULL default ''"
        ),
        'divecard' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['divecard'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'select',
            'options'               => array('basic', 'familyp', 'familys', 'travel', 'professional'),
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
        'canceledTo' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['canceledTo'],
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
            'sql'                   => "char(1) NOT NULL default ''",
            'save_callback' => array
            (
                array('tl_diver', 'checkDisableInterested')
            )
        ),
        'disable' => array
        (
            'label'                 => &$GLOBALS['TL_LANG']['tl_diver']['disable'],
            'exclude'               => true,
            'filter'                => true,
            'inputType'             => 'checkbox',
            'eval'                  => array('tl_class'=>'w50'),
            'sql'                   => "char(1) NOT NULL default ''",
            'save_callback'         => array
            (

                array('tl_diver', 'checkDisableInterested'),
                array('Exotelis\Newsletter', 'onToggleVisibility')
            )
        ),
        'newsletter' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_diver']['newsletter'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'options_callback'        => array('Contao\Newsletter', 'getNewsletters'),
            'eval'                    => array('multiple'=>true),
            'save_callback' => array
            (
                array('Exotelis\Newsletter', 'synchronize'),
                array('tl_diver', 'checkEmailNewsletter')
            ),
            'sql'                     => "blob NULL"
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
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Throws and error if a newsletter is selected but the email is empty
     *
     * @param string $varValue      The value of the field
     * @param DataContainer $dc     The DataContainer object
     *
     * @throws Exception
     *
     * @return string               The value of the field
     */
    public function checkEmailNewsletter($varValue, DataContainer $dc)
    {
        // Get value of input field email and newsletter
        $email = $dc->Input->post('email');
        $newsletter = $dc->Input->post('newsletter');

        // If a newsletter is selected and and email is empty throw an error
        if(empty($email) && $newsletter != NULL)
        {
            throw new \Exception($GLOBALS['TL_LANG']['tl_diver']['ERR']['mailMandatory']);
        }

        return $varValue;
    }

    /**
     * Updates the email in the newsletter recipients if the email changes
     *
     * @param string $varValue      The value of the field
     * @param DataContainer $dc     The DataContainer object

     * @return string               The value of the field
     */
    public function updateEmailOnChange($varValue, DataContainer $dc)
    {
        $objRow = $this->Database->prepare("SELECT email, newsletter FROM tl_diver WHERE id=?")
            ->limit(1)
            ->execute($dc->id);

        // If email is empty and all newsletter have been disabled at the same time
        if(empty($varValue) && empty($dc->Input->post('newsletter')) && $objRow->newsletter !== NULL)
        {
            $this->Database->prepare("DELETE FROM tl_newsletter_recipients WHERE email=?")
                ->execute($objRow->email);
        }

        // If email has been changed update newsletter
        if($objRow->email !== $varValue)
        {
            $this->Database->prepare("UPDATE tl_newsletter_recipients SET email=? WHERE email=?")
                ->execute($varValue, $objRow->email);
        }

        return $varValue;
    }

    /**
     * Throws and error the diver is disable and interested at the same time
     *
     * @param string $varValue      The value of the field
     * @param DataContainer $dc     The DataContainer object
     *
     * @throws Exception
     *
     * @return string               The value of the field
     */
    public function checkdisableInterested($varValue, DataContainer $dc)
    {
        // Get value of input field email and newsletter
        $disable = $dc->Input->post('disable');
        $interested = $dc->Input->post('interested');

        // If a newsletter is selected and and email is empty throw an error
        if($disable && $interested)
        {
            throw new \Exception($GLOBALS['TL_LANG']['tl_diver']['ERR']['disableInterested']);
        }

        return $varValue;
    }

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
        if (!empty($row['start']))
        {
            $key = \array_search($row['start'], $args);
            if ($key)
            {
                $args[$key] = Contao\Date::parse(Contao\Config::get('dateFormat'), $args[$key]);
            }
        }
        if (!empty($row['stop']))
        {
            $key = \array_search($row['stop'], $args);
            if ($key)
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

    /**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (!empty(Input::get('tid')) && \strlen(Input::get('tid')))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }
        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->hasAccess('tl_diver::disable', 'alexf'))
        {
            return '';
        }
        $href .= '&amp;tid='.$row['id'].'&amp;state='.$row['disable'];
        if ($row['disable'])
        {
            $icon = 'invisible.svg';
        }
        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['disable'] ? 0 : 1) . '"').'</a> ';
    }

    /**
     * Disable/enable a diver
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     *
     * @throws Contao\CoreBundle\Exception\AccessDeniedException
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');
        if ($dc)
        {
            $dc->id = $intId; // see #8043
        }
        // Trigger the onload_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_diver']['config']['onload_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_diver']['config']['onload_callback'] as $callback)
            {
                if (\is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (\is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }
        // Check the field access
        if (!$this->User->hasAccess('tl_diver::disable', 'alexf'))
        {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to activate/deactivate diver ID ' . $intId . '.');
        }
        // Set the current record
        if ($dc)
        {
            $objRow = $this->Database->prepare("SELECT * FROM tl_diver WHERE id=?")
                ->limit(1)
                ->execute($intId);
            if ($objRow->numRows)
            {
                $dc->activeRecord = $objRow;
            }
        }
        $objVersions = new Versions('tl_diver', $intId);
        $objVersions->initialize();
        // Reverse the logic (diver have disabled=1)
        $blnVisible = !$blnVisible;
        // Trigger the save_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_diver']['fields']['disable']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_diver']['fields']['disable']['save_callback'] as $callback)
            {
                if (\is_array($callback))
                {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                }
                elseif (\is_callable($callback))
                {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }
        $time = time();
        // Update the database
        $this->Database->prepare("UPDATE tl_diver SET tstamp=$time, disable='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
            ->execute($intId);
        if ($dc)
        {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->disable = ($blnVisible ? '1' : '');
        }
        // Trigger the onsubmit_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_diver']['config']['onsubmit_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_diver']['config']['onsubmit_callback'] as $callback)
            {
                if (\is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (\is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }
        $objVersions->create();
    }
}