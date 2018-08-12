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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        Contao\System::loadLanguageFile('tl_exportdiver');

        // Fields to be exported
        $fields = array('id', 'lastname', 'firstname', 'gender', 'dateOfBirth', 'street', 'postal', 'city', 'phone', 'mobile', 'email', 'status', 'brevet', 'nitrox', 'divecard', 'start', 'stop');

        // Allowed filetypes
        $filetypes = array('xlsx' => 'Xlsx');

        // Define template variables
        $this->Template->href = $this->getReferer(true);
        $this->Template->title = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];

        $this->Template->headline = $GLOBALS['TL_LANG']['tl_exportdiver']['headline'];
        $this->Template->action = ampersand(Contao\Environment::get('request'));
        $this->Template->filetypes = $filetypes;
        $this->Template->filetypesLabel = $GLOBALS['TL_LANG']['tl_exportdiver']['filetypesLabel'];
        $this->Template->filetypesHelp = $GLOBALS['TL_LANG']['tl_exportdiver']['filetypesHelp'];
        $this->Template->submit = $GLOBALS['TL_LANG']['tl_exportdiver']['submit'];

        $this->Template->message = Contao\Message::generateUnwrapped(__CLASS__);

        // If the form has been sent
        if (Contao\Input::post('FORM_SUBMIT') == 'tl_exportdiver')
        {
            // Check for data in table
            $objRow = $this->Database->prepare("SELECT lastname FROM tl_diver")
                ->limit(1)
                ->execute();

            if($objRow->numRows < 1)
            {
                // Stop export if no data have been found
                Contao\Message::addError($GLOBALS['TL_LANG']['tl_exportdiver']['noRecords'], __CLASS__);
            }
            else
            {
                // Check the selected file type
                $filetype = Contao\Input::post('filetype');
                if ($filetype === 'xlsx')
                {
                    $data = $this->getData($fields);
                    $this->createXlsx($fields, $data);
                }
                else
                {
                    // Display error if the selected file type is invalid
                    Contao\Message::addError(\sprintf($GLOBALS['TL_LANG']['tl_exportdiver']['invalidType'], $filetype), __CLASS__);
                }
            }

            $this->reload();
        }
    }

    /**
     * Gets the data from the database and stores them in a multidimensional array separated by the activity status of the divers
     * in = still active members
     * automaticout = if the stop date is in the past
     * out = members who resigned
     * interested = people who are interested to join the club
     *
     * @param   array           Fields to be exported
     *
     * @return  array|boolean   Data Array with labels active and resigned members or false
     */
    protected function getData($fields)
    {
        Contao\System::loadLanguageFile('tl_diver');
        $this->loadDataContainer('tl_diver');

        // Define array to store the data in their categories
        $data = array('in' => array(), 'automaticout' => array(), 'out' => array(), 'interested' => array());

        // List of columns that should be selected (stop, disabled and interested must be part of it)
        if (!\in_array ( 'disable' , $fields))
        {
            \array_push($fields, 'disable');
        }
        if (!\in_array ( 'interested' , $fields))
        {
            \array_push($fields, 'interested');
        }

        // Get data from database
        $objRow = $this->Database->prepare("SELECT " . implode(",", $fields) . " FROM tl_diver ORDER BY lastname, firstname")
            ->execute();
        $result = $objRow->fetchAllAssoc();

        // Parse data
        foreach($result as $r)
        {
            // Set labels and convert and translate data
            foreach ($fields as $label)
            {
                // Convert timestamps to date
                if(\array_key_exists('eval', $GLOBALS['TL_DCA']['tl_diver']['fields'][$label]) && \array_key_exists('rgxp', $GLOBALS['TL_DCA']['tl_diver']['fields'][$label]['eval']))
                {
                    if($GLOBALS['TL_DCA']['tl_diver']['fields'][$label]['eval']['rgxp'] === 'date')
                    {
                        $r[$label] = Contao\Date::parse(Contao\Config::get('dateFormat'), $r[$label]);
                    }
                }
                if(\array_key_exists('reference', $GLOBALS['TL_DCA']['tl_diver']['fields'][$label]))
                {
                    $r[$label] = $GLOBALS['TL_DCA']['tl_diver']['fields'][$label]['reference'][$r[$label]];
                }
            }

            // Push data
            if($r['interested'])
            {
                \array_push($data['interested'], $r);
            }
            elseif($r['disable'])
            {
                \array_push($data['out'], $r);
            }
            elseif(!empty($r['stop']) && strtotime($r['stop']) < \time())
            {
                \array_push($data['automaticout'], $r);
            }
            else
            {
                \array_push($data['in'], $r);
            }
        }

        return $data;
    }

    /**
     * Creates a Xlsx file based on the data from the database
     *
     * @param array $header List of the headers
     * @param array $data   The data of the member
     */
    protected function createXlsx($header, $data)
    {

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

        // Set header
        if(count($data['in']) > 0)
        {
            $labels = $data['in'][0];
        }
        elseif(count($data['automaticout']) > 0)
        {
            $labels = $data['automaticout'][0];
        }
        elseif(count($data['out']) > 0)
        {
            $labels = $data['out'][0];
        }
        elseif(count($data['interested']) > 0)
        {
            $labels = $data['interested'][0];
        }
        else
        {
            die('No data in valid arrays found');
        }

        foreach ($labels as $k => $v) {
            if (\is_array($GLOBALS['TL_DCA']['tl_diver']['fields'][$k]['label'])) {
                $csv['header'][$k] = $GLOBALS['TL_DCA']['tl_diver']['fields'][$k]['label'][0];
            } else {
                $csv['header'][$k] = $GLOBALS['TL_DCA']['tl_diver']['fields'][$k]['label'];
            }
        }

        // Add header
        $csv['header'] = \implode($delimiter, $csv['header']);

        // Add Members
        foreach ($data['in'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        // add automatic resigned member
        \array_push($csv, $GLOBALS['TL_LANG']['tl_exportdiver']['automaticallyResigned']);
        foreach ($data['automaticout'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        // Add old Members
        \array_push($csv, $GLOBALS['TL_LANG']['tl_exportdiver']['resign']);
        foreach ($data['out'] as $d)
        {
            $d = \implode($delimiter, $d);
            \array_push($csv, $d);
        }

        // Interested people
        \array_push($csv, $GLOBALS['TL_LANG']['tl_exportdiver']['interested']);
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
        $objFile = new Contao\File('/files/di_exportdiver.csv');
        foreach($data as $d)
        {
            $objFile->append(\utf8_decode($d));
        }
        $objFile->close();

        $filename = $GLOBALS['TL_LANG']['tl_exportdiver']['listOfMembers'] . ' ' . Contao\Date::parse(Contao\Config::get('dateFormat'), \time()) . '.csv';
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $objFile->getContent();

        $objFile->delete();

        exit;
    }
}