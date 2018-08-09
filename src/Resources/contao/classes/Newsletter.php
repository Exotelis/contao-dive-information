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
 * Class Newsletter to extend Contaos Newsletter
 *
 * @author Sebastian Krah <https://github.com/Exotelis>
 */
class Newsletter extends Contao\Newsletter
{
    /**
     * Update a particular member account
     */
    public function updateAccount()
    {
        $intUser = \Input::get('id');

        // Front end call
        if (TL_MODE == 'FE')
        {
            return;
        }

        // Return if there is no user (e.g. upon registration)
        if (!$intUser)
        {
            return;
        }

        // Delete account
        if (\Input::get('act') == 'delete')
        {
            $objUser = $this->Database->prepare("SELECT email FROM tl_diver WHERE id=?")
                ->limit(1)
                ->execute($intUser);

            if ($objUser->numRows)
            {
                $this->Database->prepare("DELETE FROM tl_newsletter_recipients WHERE email=?")
                    ->execute($objUser->email);
            }
        }
    }

    /**
     * Synchronize the newsletter subscriptions if the visibility is toggled
     *
     * @param boolean       $blnDisabled
     * @param Contao\DataContainer $dc
     *
     * @return boolean
     */
    public function onToggleVisibility($blnDisabled, Contao\DataContainer $dc)
    {
        if (!$dc->id)
        {
            return $blnDisabled;
        }

        $objUser = $this->Database->prepare("SELECT email FROM tl_diver WHERE id=?")
            ->limit(1)
            ->execute($dc->id);

        if ($objUser->numRows)
        {
            $this->Database->prepare("UPDATE tl_newsletter_recipients SET tstamp=?, active=? WHERE email=?")
                ->execute(\time(), ($blnDisabled ? '' : '1'), $objUser->email);
        }

        return $blnDisabled;
    }

    /**
     * Synchronize newsletter subscription of existing users
     *
     * @param mixed  $varValue
     * @param object $objUser
     * @param object $objModule
     *
     * @return mixed
     */
    public function synchronize($varValue, $objUser, $objModule=null)
    {
        // Return if there is no user (e.g. upon registration)
        if ($objUser === null)
        {
            return $varValue;
        }
        $blnIsFrontend = true;
        // If called from the back end, the second argument is a DataContainer object
        if ($objUser instanceof Contao\DataContainer)
        {
            $objUser = $this->Database->prepare("SELECT * FROM tl_diver WHERE id=?")
                ->limit(1)
                ->execute($objUser->id);
            if ($objUser->numRows < 1)
            {
                return $varValue;
            }
            $blnIsFrontend = false;
        }
        // Nothing has changed or e-mail address is empty
        if ($varValue == $objUser->newsletter || $objUser->email == '')
        {
            return $varValue;
        }
        $time = \time();
        $varValue = \StringUtil::deserialize($varValue, true);
        // Get all channel IDs (thanks to Andreas Schempp)
        if ($blnIsFrontend && $objModule instanceof Contao\Module)
        {
            $arrChannel = \StringUtil::deserialize($objModule->newsletters, true);
        }
        else
        {
            $arrChannel = $this->Database->query("SELECT id FROM tl_newsletter_channel")->fetchEach('id');
        }
        $arrDelete = \array_values(array_diff($arrChannel, $varValue));
        // Delete existing recipients
        if (!empty($arrDelete) && \is_array($arrDelete))
        {
            $this->Database->prepare("DELETE FROM tl_newsletter_recipients WHERE pid IN(" . \implode(',', array_map('\intval', $arrDelete)) . ") AND email=?")
                ->execute($objUser->email);
        }
        // Add recipients
        foreach ($varValue as $intId)
        {
            $intId = (int) $intId;
            if ($intId < 1)
            {
                continue;
            }
            $objRecipient = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_newsletter_recipients WHERE pid=? AND email=?")
                ->execute($intId, $objUser->email);
            if ($objRecipient->count < 1)
            {
                $this->Database->prepare("INSERT INTO tl_newsletter_recipients SET pid=?, tstamp=$time, email=?, active=?, addedOn=?, ip=?")
                    ->execute($intId, $objUser->email, ($objUser->disable ? '' : 1), ($blnIsFrontend ? $time : ''), ($blnIsFrontend ? \Environment::get('ip') : ''));
            }
        }
        return \serialize($varValue);
    }
}