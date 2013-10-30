<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * calendar_promotion
 *
 *
 * @copyright  Marko Cupic, Oberkirch
 * @author     Marko Cupic <m.cupic@gmx.ch>
 * @package    calendar_promotion
 * @license    LGPL
 * @filesource
 */


/**
 * Class CalendarPromotion
 *
 * @copyright  Marko Cupic, Oberkirch
 * @author     Marko Cupic <m.cupic@gmx.ch>
 * @package    calendar_promotion
 */
class CalendarPromotion extends ContentElement
{

       /**
        * Template
        * @var string
        */
       protected $strTemplate = 'ce_calendar_promotion';


       /**
        * Return if the highlighter plugin is not loaded
        * @return string
        */
       public function generate()
       {
              if (TL_MODE == 'BE') {
                     $return = '<pre>Calendar Promotion</pre>';

                     if ($this->headline != '') {
                            $return = '<' . $this->hl . '>' . $this->headline . '</' . $this->hl . '>' . $return;
                     }

                     return $return;
              }

              return parent::generate();
       }

       /**
        * Generate module
        */
       protected function compile()
       {
              $promId = $this->calendar_promotion_archive;
              $objArchive = $this->Database->prepare('SELECT * FROM tl_calendar_promotion_archive WHERE id=?')->execute($promId);
              $case = $objArchive->eventtype;
              switch ($case) {
                     case 'adventskalender':
                            $objChilds = $this->Database->prepare('SELECT * FROM tl_calendar_promotion WHERE pid=? ORDER BY displayorder')->execute($promId);
                            break;

                     case 'wochenkalender':
                            //tstamp Monday of current week
                            $tstampMonday = strtotime($this->parseDate('Y') . 'W' . $this->parseDate('W'));
                            $tstampSunday = strtotime('next Sunday', $tstampMonday);
                            $objChilds = $this->Database->prepare('SELECT * FROM tl_calendar_promotion WHERE pid=? AND eventtstamp >= ? AND eventtstamp <= ? ORDER BY displayorder')->execute($promId, $tstampMonday, $tstampSunday);
                            break;
              }
              $arrBoxes = $objChilds->fetchAllAssoc();

              // switch for testmode
              $testmode = null;
              $today = ($testmode ? mktime(0, 0, 0, 12, 6, 2013) : time());

              $i = 0;

              $tolerance = $objArchive->tolerance * 24 * 3600;

              foreach ($arrBoxes as $box) {
                     $error = 1;
                     $case = 'toearly';
                     if ($today - intval($box['eventtstamp']) == 0) {
                            //just in time
                            $arrBoxes[$i]['class'] = 'justintime';
                            $error = null;
                            $case = 'justintime';
                            $arrBoxes[$i]['allowed'] = 1;
                     } elseif ($today - intval($box['eventtstamp']) <= $tolerance && $today - intval($box['eventtstamp']) > 0) {
                            // still in time
                            $arrBoxes[$i]['class'] = 'stillintime';
                            $error = null;
                            $case = 'stillintime';
                            $arrBoxes[$i]['allowed'] = 1;
                     } elseif (intval($box['eventtstamp']) + $tolerance < $today) {
                            // expired
                            $arrBoxes[$i]['class'] = 'expired';
                            $error = 1;
                            $case = 'expired';
                            $arrBoxes[$i]['allowed'] = null;

                     } elseif ($today - intval($box['eventtstamp']) < 0) {
                            //toearly
                            $arrBoxes[$i]['class'] = 'toearly';
                            $error = 1;
                            $case = 'toearly';
                            $arrBoxes[$i]['allowed'] = null;

                     } else {
                            // other error
                            $arrBoxes[$i]['class'] = 'error';
                            $error = 1;
                            $case = 'error';
                            $arrBoxes[$i]['allowed'] = null;

                     }

                     // generate product image if all ok!
                     if (!error) {
                            if (is_file(TL_ROOT . '/' . $box['singleSRC'])) {
                                   $objImg = new File($box['singleSRC']);
                                   if ($objImg->isGdImage) {
                                          $arrBoxes[$i]['singleSRC'] = $this->getImage($box['singleSRC'], $objImg->width * 0.999, $objImg->height * 0.999);
                                   }
                            }
                     }

                     if ($error) {
                            $arrBoxes[$i]['class'] = $case;
                            unset($arrBoxes[$i]['href']);
                            $arrBoxes[$i]['title'] = 'Fehlermeldung';
                            $arrBoxes[$i]['description'] = nl2br($objArchive->errormessage);
                            if (is_file(TL_ROOT . '/' . $objArchive->singleSRC)) {
                                   $objImg2 = new File($objArchive->singleSRC);
                                   if ($objImg2->isGdImage) {
                                          $arrBoxes[$i]['singleSRC'] = $this->getImage($objArchive->singleSRC, $objImg2->width * 0.999, $objImg2->height * 0.999);
                                   }
                            }
                     }

                     $arrBoxes[$i]['mbsize'] = (!$arrBoxes[$i]['mbwidth'] && !$arrBoxes[$i]['mbheight'] ? '' : $arrBoxes[$i]['mbwidth'] . ' ' . $arrBoxes[$i]['mbheight']);
                     $i++;
              }
              $this->Template->arrBoxes = $arrBoxes;
              $this->Template->description = $this->calendar_promotion_description;
       }
}