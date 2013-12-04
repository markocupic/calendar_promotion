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

              // switch for testmode
              $testmode = ($objArchive->testmode && $objArchive->virtualTestdate > 0) ? true : false;

              // today 00:00 o'clock
              $today = ($testmode ?  $objArchive->virtualTestdate : mktime(0, 0, 0, date('n', time()), date('j', time()), date('Y', time())));
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
              $i = 0;
              $tolerance = $objArchive->tolerance * 24 * 3600;
              foreach ($arrBoxes as $box) {
                     $error = 1;
                     $arrCSS = deserialize($arrBoxes[$i]['cssID'], true);
                     // css id
                     $arrBoxes[$i]['cssID'] = $arrCSS[0] != '' ? $arrCSS[1] : null;
                     // css class
                     $arrCssClasses = $arrCSS[1] != '' ? explode(' ', $arrCSS[1]) : array();
                     $arrBoxes[$i]['allowed'] = null;
                     if ($today - intval($box['eventtstamp']) == 0) {
                            //just in time
                            $arrCssClasses[] = 'justintime';
                            $error = null;
                            $case = 'justintime';
                            $arrBoxes[$i]['allowed'] = 1;
                     } elseif ($today - intval($box['eventtstamp']) <= $tolerance && $today - intval($box['eventtstamp']) > 0) {
                            // still in time
                            $arrCssClasses[] = 'stillintime';
                            $error = null;
                            $case = 'stillintime';
                            $arrBoxes[$i]['allowed'] = 1;
                     } elseif (intval($box['eventtstamp']) + $tolerance < $today) {
                            // expired
                            $arrCssClasses[] = 'expired';
                            $error = 1;
                            $case = 'expired';
                            $arrBoxes[$i]['allowed'] = null;
                     } elseif ($today - intval($box['eventtstamp']) < 0) {
                            //toearly
                            $arrCssClasses[] = 'toearly';
                            $error = 1;
                            $case = 'toearly';
                            $arrBoxes[$i]['allowed'] = null;
                     } else {
                            // other error
                            $arrCssClasses[] = 'error';
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
                            unset($arrBoxes[$i]['href']);
                            $arrBoxes[$i]['title'] = 'Fehlermeldung';

                            if ($case == 'toearly')
                            {
                                   $arrBoxes[$i]['description'] = nl2br($objArchive->errormessageToEarly);
                                   $src = $objArchive->singleSRCToEarly;
                            } else {
                                   $arrBoxes[$i]['description'] = nl2br($objArchive->errormessageExpired);
                                   $src = $objArchive->singleSRCExpired;
                            }
                            if (is_file(TL_ROOT . '/' . $src)) {
                                   $objImg2 = new File($src);
                                   if ($objImg2->isGdImage) {
                                          $arrBoxes[$i]['singleSRC'] = $this->getImage($src, $objImg2->width * 0.999, $objImg2->height * 0.999);
                                   }
                            }
                     }
                     $arrBoxes[$i]['cssClass'] = implode(' ', $arrCssClasses);
                     $arrBoxes[$i]['mbsize'] = (!$arrBoxes[$i]['mbwidth'] && !$arrBoxes[$i]['mbheight'] ? '' : $arrBoxes[$i]['mbwidth'] . ' ' . $arrBoxes[$i]['mbheight']);

                     // Display Klick-Counter
                     //$arrBoxes[$i]['description' ] .= '<br><br>Anzahl Klicks: ' . $box['visits'];

                     $i++;
              }
              $this->Template->elementId = $this->id;
              $this->Template->arrBoxes = $arrBoxes;
              $this->Template->description = $this->calendar_promotion_description;
       }

       /**
        * Get all records and add them to an array
        */
       public function generateAjax()
       {
              if ($this->Input->get('isAjax') == 1 && $this->Input->get('do') == 'countClicks') {

                     //$objDb = $this->Database->execute("TRUNCATE TABLE tl_calendar_promotion_count_clicks"); die();

                     $objDb = $this->Database->prepare("SELECT * FROM tl_calendar_promotion_count_clicks WHERE pid=? AND ip=? LIMIT 0,1")
                                             ->execute($this->Input->get('windowId'), $_SERVER['REMOTE_ADDR']);

                     // register user if he opens the window for the first time
                     if ($objDb->numRows < 1) {
                            $set = array(
                                   'pid' => $this->Input->get('windowId'),
                                   'ip' => $_SERVER['REMOTE_ADDR']
                            );
                            $objDbInsert = $this->Database->prepare("INSERT INTO tl_calendar_promotion_count_clicks %s")
                                   ->set($set)
                                   ->execute();

                            $objVisits = $this->Database->prepare("SELECT * FROM tl_calendar_promotion WHERE id=? LIMIT 0,1")
                                                 ->execute($this->Input->get('windowId'));

                            $set = array(
                                   'visits' => $objVisits->visits + 1
                            );
                            $objUpdate = $this->Database->prepare("UPDATE tl_calendar_promotion %s WHERE id=?")
                                                    ->set($set)
                                                    ->execute($this->Input->get('windowId'));
                     }
              }
       }
}