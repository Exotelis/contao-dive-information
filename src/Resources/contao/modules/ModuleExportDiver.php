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
        $fields = array('id', 'lastname', 'firstname', 'gender', 'dateOfBirth', 'street', 'postal', 'city', 'phone', 'mobile', 'email', 'status', 'brevet', 'nitrox', 'divecard', 'start', 'stop', 'canceledTo');

        // Allowed filetypes
        $filetypes = array('xlsx' => 'Excel - xlsx');

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
            // Update user that has been disabled
            (new Util())->disableMembership();

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

                // Get data
                $data = $this->getData($fields);

                if ($filetype === 'xlsx')
                {
                    $this->createXlsx($data);
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

        // Add extra fields (fee)
        $fee = "CASE status 
            WHEN 'familypm' THEN 21.00
            WHEN 'active' THEN 13.00
            WHEN 'partner' THEN 6.50
            WHEN 'ptsactive' THEN 6.50
            WHEN 'passive' THEN 5.00
            WHEN 'familye' THEN 1.25
            ELSE 0.00
          END AS feePerMonth,
          (SELECT feePerMonth) * 3 AS feePerQuarter,
          (SELECT feePerMonth) * 6 AS feePerHalfYear,
          (SELECT feePerMonth) * 12 AS feePerYear";

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
        $objRow = $this->Database->prepare("SELECT " . implode(",", $fields) . ", " . $fee . " FROM tl_diver ORDER BY lastname, firstname")
            ->execute();
        $result = $objRow->fetchAllAssoc();

        // Add temp column in fields
        \array_push($fields, 'feePerHalfYear');

        // Translate and push header/fields
        $header = array();
        foreach ($fields as $f)
        {
            if (\is_array($GLOBALS['TL_LANG']['tl_diver'][$f]))
            {
                $header[$f] = $GLOBALS['TL_LANG']['tl_diver'][$f][0];
            }
            else
            {
                $header[$f] = $GLOBALS['TL_LANG']['tl_diver'][$f];
            }
        }
        foreach ($data as $key => $value)
        {
            $data[$key]['header'] = $header;
        }

        // Parse data
        foreach($result as $r)
        {
            // Set labels and convert and translate data
            foreach ($fields as $label)
            {
                // Check if field is in DCA
                if(!\array_key_exists($label, $GLOBALS['TL_DCA']['tl_diver']['fields'])) {
                    continue;
                }

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

            // Push data, but only fields that are in the header
            if($r['interested'])
            {
                \array_push($data['interested'], \array_intersect_key($r, $header));
            }
            elseif($r['disable'])
            {
                \array_push($data['out'], \array_intersect_key($r, $header));
            }
            elseif(!empty($r['stop']) && strtotime($r['stop']) < \time())
            {
                \array_push($data['automaticout'], \array_intersect_key($r, $header));
            }
            else
            {
                \array_push($data['in'], \array_intersect_key($r, $header));
            }
        }

        return $data;
    }

    /**
     * Creates a Xlsx file based on the data from the database
     *
     * @param array $data   The data of the member
     *
     * @throws \Exception
     */
    protected function createXlsx($data)
    {
        // Import backend user
        $this->import('BackendUser', 'User');

        // Set title
        $title = \sprintf($GLOBALS['TL_LANG']['tl_exportdiver']['documentTitle'], Contao\Date::parse(Contao\Config::get('dateFormat'), \time()));

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Define Colors
        $colors = array('headerBg' => '256089', 'headerFont' => 'ffffff', 'evenRowBg' => '86bade', 'oddRowBg' => 'ffffff', 'border' => 'd9d9d9');

        // Style
        $globalStyle = array
        (
            'borders' => array
            (
                'outline' => array
                (
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array
                    (
                        'rgb' => $colors['border']
                    )
                ),
                'inside' => array
                (
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array
                    (
                        'rgb' => $colors['border']
                    )
                )
            ),
            'font' => array
            (
                'size' => 10
            )
        );
        $headerStyle = array
        (
            'font' => array
            (
                'bold' => true,
                'color' => array
                (
                    'rgb' => $colors['headerFont']
                )
            ),
            'fill' => array
            (
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array
                (
                    'rgb' => $colors['headerBg']
                )
            )
        );
        $evenRowStyle = array
        (
            'fill' => array
            (
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array
                (
                    'rgb' => $colors['evenRowBg']
                )
            )
        );
        $oddRowStyle = array
        (
            'fill' => array
            (
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array
                (
                    'rgb' => $colors['oddRowBg']
                )
            )
        );


        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator($this->User->name)
            ->setLastModifiedBy($this->User->name)
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($GLOBALS['TL_LANG']['tl_exportdiver']['documentDescription']);

        //Add sheets and set title
        $spreadsheet->getActiveSheet()->setTitle($GLOBALS['TL_LANG']['tl_exportdiver']['in']);

        $automaticoutWorksheet = clone $spreadsheet->getActiveSheet();
        $outWorksheet = clone $spreadsheet->getActiveSheet();
        $interestedWorksheet = clone $spreadsheet->getActiveSheet();
        $automaticoutWorksheet->setTitle($GLOBALS['TL_LANG']['tl_exportdiver']['automaticout']);
        $outWorksheet->setTitle($GLOBALS['TL_LANG']['tl_exportdiver']['out']);
        $interestedWorksheet->setTitle($GLOBALS['TL_LANG']['tl_exportdiver']['interested']);
        $spreadsheet->addSheet($automaticoutWorksheet);
        $spreadsheet->addSheet($outWorksheet);
        $spreadsheet->addSheet($interestedWorksheet);

        foreach ($data as $key => $value)
        {
            $spreadsheet->setActiveSheetIndexByName($GLOBALS['TL_LANG']['tl_exportdiver'][$key]);

            // Fill with data
            $spreadsheet->getActiveSheet()->fromArray($data[$key], NULL);

            // Add filter
            $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());

            // Global styling
            $spreadsheet->getActiveSheet()->getStyle($spreadsheet->getActiveSheet()->calculateWorksheetDimension())->applyFromArray($globalStyle);

            // Header styling
            $spreadsheet->getActiveSheet()->getStyle('A1:' . $spreadsheet->getActiveSheet()->getHighestColumn() . '1')->applyFromArray($headerStyle);

            // Row styling
            for ($i = 2; $i <= $spreadsheet->getActiveSheet()->getHighestRow(); $i++)
            {
                if ($i % 2)
                {
                    $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':' . $spreadsheet->getActiveSheet()->getHighestColumn() . $i)->applyFromArray($oddRowStyle);
                }
                else
                {
                    $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':' . $spreadsheet->getActiveSheet()->getHighestColumn() . $i)->applyFromArray($evenRowStyle);
                }
            }

            // Auto column width
            foreach (range('A',$spreadsheet->getActiveSheet()->getHighestColumn()) as $columnID)
            {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Set printer options
            $spreadsheet->getActiveSheet()->getPageSetup() ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.19685);
            $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.19685);
            $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.19685);
            $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.19685);
        }

        // Set first worksheet active
        $spreadsheet->setActiveSheetIndex(0);

        // Export file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}