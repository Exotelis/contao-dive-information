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
class ModuleExportDiver extends Contao\BackendModule
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_exportdiver';

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
        $this->Template->action = ampersand(Contao\Environment::get('request'));
        $this->Template->submit = $GLOBALS['TL_LANG']['tl_exportmembers']['submit'];

        if (Contao\Input::post('FORM_SUBMIT') == 'tl_exportmembers') {
            $this->downloadFile($this->convertToCsv($this->getData()));
            $this->reload();
        }
    }

    /**
     * Gets the data from the database
     *
     * @return array Data Array with labels active and resigned members
     */
    private function getData()
    {
        Contao\System::loadLanguageFile('tl_member');
        $this->loadDataContainer('tl_member');

        // Define array to store labels and data
        $data = array('in' => array(), 'automaticout' => array(), 'out' => array(), 'interested' => array());

        // List of columns that should be selected (stop, disabled and interested must be part of it)
        $labels = array('id', 'lastname', 'firstname', 'gender', 'dateOfBirth', 'street', 'postal', 'city', 'phone', 'mobile', 'email', 'membershipStatus', 'brevet', 'nitrox', 'divecard', 'start', 'stop', 'disable', 'interested');

        // Add Label to field ID
        if(\in_array('id', $labels))
        {
            $GLOBALS['TL_DCA']['tl_member']['fields']['id']['label'] = $GLOBALS['TL_LANG']['tl_member']['memberId'];
        }

        // Get data from database
        $objRow = $this->Database->prepare("SELECT " . implode(",", $labels) . " FROM tl_member ORDER BY lastname, firstname")
            ->execute();
        $result = $objRow->fetchAllAssoc();

        // Parse data
        foreach($result as $r)
        {
            // Set labels and convert and translate data
            foreach ($labels as $label)
            {
                // Convert timestamps to date
                if(\array_key_exists('eval', $GLOBALS['TL_DCA']['tl_member']['fields'][$label]) && \array_key_exists('rgxp', $GLOBALS['TL_DCA']['tl_member']['fields'][$label]['eval']))
                {
                    if($GLOBALS['TL_DCA']['tl_member']['fields'][$label]['eval']['rgxp'] === 'date' ||  $GLOBALS['TL_DCA']['tl_member']['fields'][$label]['eval']['rgxp'] === 'datim')
                    {
                        $r[$label] = Contao\Date::parse(Contao\Config::get('dateFormat'), $r[$label]);
                    }
                }
                if(\array_key_exists('reference', $GLOBALS['TL_DCA']['tl_member']['fields'][$label]))
                {
                    $r[$label] = $GLOBALS['TL_DCA']['tl_member']['fields'][$label]['reference'][$r[$label]];
                }
            }

            // Push data
            if($r['interested'])
            {
                unset($r['disable'], $r['interested']);
                \array_push($data['interested'], $r);
            }
            elseif($r['disable'])
            {
                unset($r['disable'], $r['interested']);
                \array_push($data['out'], $r);
            }
            elseif(!empty($r['stop']) && (int)$r['stop'] < \time())
            {
                unset($r['disable'], $r['interested']);
                \array_push($data['automaticout'], $r);
            }
            else
            {
                unset($r['disable'], $r['interested']);
                \array_push($data['in'], $r);
            }
        }

        return $data;
    }

    /**
     * Convets the data to a csv format
     *
     * @param array $data
     * @param string $delimiter
     * @return array CSV conform Array
     */
    private function convertToCsv($data, $delimiter = ';')
    {
        $csv = array();
        $temp = array();

        // Set header
        foreach ($data['in'][0] as $k => $v) {
            if (\is_array($GLOBALS['TL_DCA']['tl_member']['fields'][$k]['label'])) {
                $temp['header'][$k] = $GLOBALS['TL_DCA']['tl_member']['fields'][$k]['label'][0];
            } else {
                $temp['header'][$k] = $GLOBALS['TL_DCA']['tl_member']['fields'][$k]['label'];
            }
        }

        // Alternativ translations
        $temp['header']['start'] = &$GLOBALS['TL_LANG']['tl_member']['diveInformationStart'];
        $temp['header']['stop'] = &$GLOBALS['TL_LANG']['tl_member']['diveInformationStop'];

        // Add header
        $csv['header'] = \implode($delimiter, $temp['header']);

        // Add Members
        foreach ($data['in'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        // add automatic resigned member
        \array_push($csv, $GLOBALS['TL_LANG']['tl_member']['automaticallyResigned']);
        foreach ($data['automaticout'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        // Add old Members
        \array_push($csv, $GLOBALS['TL_LANG']['tl_member']['diveInformationDisable']);
        foreach ($data['out'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        // Interested people
        \array_push($csv, $GLOBALS['TL_LANG']['tl_member']['interestedPeople']);
        foreach ($data['interested'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        return $csv;
    }

    /**
     * Creates a temporary file and downloads it
     *
     * @param array $data Data Array
     * @throws \Exception
     */
    private function downloadFile($data)
    {
        $objFile = new Contao\File('/files/memberlist.csv');
        foreach($data as $d)
        {
            $objFile->append(\utf8_decode($d));
        }
        $objFile->close();

        $filename = $GLOBALS['TL_LANG']['tl_exportmembers']['listOfMembers'] . ' ' . Contao\Date::parse(Contao\Config::get('dateFormat'), \time()) . '.csv';
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $objFile->getContent();

        $objFile->delete();

        exit;
    }
}