<?php
/**
 * This file is part of exotelis/contao-dive-information
 *
 * Copyright (c) 2018-2019 Sebastian Krah
 *
 * @package   exotelis/contao-dive-information
 * @author    Sebatian Krah <exotelis@mailbox.org>
 * @copyright 2019 Sebastian Krah
 * @license   https://github.com/Exotelis/contao-dive-information/blob/master/LICENSE LGPL-3.0
 */
declare(strict_types=1);

namespace Exotelis;

/**
 * Class Util
 *
 * Util Class
 *
 * @author   Sebastian Krah <exotelis@mailbox.org>
 */
class Util
{
    /**
     * Disabled the membership if it has been canceled
     */
    public function disableMembership()
    {
        $db = \Database::getInstance();

        // Disable member if cancel date passed
        $db->prepare("UPDATE tl_diver SET tstamp=?, disable=? WHERE canceledTo<? AND canceledTo>? AND disable<>?")
            ->execute(\time(), 1, \time(), 0, 1);

        // Get "Rundschreiben Mitglieder" ID
        $result = $db->prepare("SELECT id FROM tl_newsletter_channel WHERE title=?")
            ->limit(1)
            ->execute("Rundschreiben Mitglieder");

        if ($result->numRows < 1) {
           return;
        }
        $channelId = $result->id;

        $result = $db->prepare("SELECT email FROM tl_diver WHERE disable=1")
            ->execute();

        // Disable Member for Newsletter "Rundschreiben Mitglieder"
        while($result->next()) {
            $db->prepare("UPDATE tl_newsletter_recipients SET tstamp=?, active=? WHERE pid=? AND email=?")
                ->execute(\time(), '', $channelId, $result->email);
        }
    }
}