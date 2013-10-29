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
 * Class CalendarPromotionHelpers
 *
 * @copyright  Marko Cupic, Oberkirch
 * @author     Marko Cupic <m.cupic@gmx.ch>
 * @package    calendar_promotion
 */
class CalendarPromotionHelpers extends Controller
{
       /**
        * Load database object
        */
       public function __construct()
       {
              parent::__construct();
              $this->import('Database');
       }


       /***********************************************************************************************************************/
       /* FRONTEND
       /***********************************************************************************************************************/

       /**
        * Compile InsertTags
        * @param string
        * @return mixed
        */
       public function replaceVideoBoxInsertTags($strTag)
       {
              // {{VIDEOBOX_NEWS::CONTAINERID}}
              if (strpos($strTag, 'VIDEOBOX_NEWS::') !== false) {
                     $arrData = explode('::', $strTag);
                     $strID = $arrData[1];

                     $objNews = $this->Database->prepare("SELECT videobox_video FROM tl_news WHERE videobox_addvideo=? AND id=?")
                            ->execute(1, $strID);

                     if ($objNews->numRows < 1) {
                            return '';
                     }

                     $objVideo = new VideoBoxElement((int)$objNews->videobox_video);
                     return $objVideo->generate();
              }

              // {{VIDEOBOX_EVENTS::CONTAINERID}}
              if (strpos($strTag, 'VIDEOBOX_EVENTS::') !== false) {
                     $arrData = explode('::', $strTag);
                     $strID = $arrData[1];

                     $objNews = $this->Database->prepare("SELECT videobox_video FROM tl_calendar_events WHERE videobox_addvideo=? AND id=?")
                            ->execute(1, $strID);

                     if ($objNews->numRows < 1) {
                            return '';
                     }

                     $objVideo = new VideoBoxElement((int)$objNews->videobox_video);
                     return $objVideo->generate();
              }

              // {{VIDEOBOX_STANDALONE::VIDEOID}}
              if (strpos($strTag, 'VIDEOBOX_STANDALONE::') !== false) {
                     $arrData = explode('::', $strTag);

                     $objVideo = new VideoBoxElement((int)$arrData[1]);
                     return $objVideo->generate();
              }

              return false;
       }


       /***********************************************************************************************************************/
       /* BACKEND
       /***********************************************************************************************************************/

       /**
        * List all the calendarPromotions in a dropdown (to choose from in the backend)
        * @return array
        */
       public function getCalendarPromotions()
       {
              $this->import('BackendUser', 'User');
              $objCalProm = $this->Database->execute("SELECT * FROM tl_calendar_promotion_archive");

              while ($objCalProm->next()) {
                     $groups[$objCalProm->id] = $objCalProm->eventtitle;
              }
              return $groups;
       }

       /**
        * Return all event templates as array
        * @param DataContainer
        * @return array
        */
       public function getTemplateList(DataContainer $dc)
       {
              $intPid = $dc->activeRecord->pid;

              // Override multiple
              if ($this->Input->get('act') == 'overrideAll')
              {
                     $intPid = $this->Input->get('id');
              }

              // Get the page ID
              $objArticle = $this->Database->prepare("SELECT pid FROM tl_article WHERE id=?")
                     ->limit(1)
                     ->execute($intPid);

              // Inherit the page settings
              $objPage = $this->getPageDetails($objArticle->pid);

              // Get the theme ID
              $objLayout = $this->Database->prepare("SELECT pid FROM tl_layout WHERE id=? OR fallback=1 ORDER BY fallback")
                     ->limit(1)
                     ->execute($objPage->layout);

              // Return all templates
              return $this->getTemplateGroup('ce_calendar_promotion', $objLayout->pid);
       }


       /**
        * Compile InsertTags
        * @param object
        * @param string
        * @param array
        */
       public function linkToSettings($dc, $strTable, $arrModule)
       {

              // check wheter there has already been created a settings entry
              $objCheck = $this->Database->prepare("SELECT id FROM tl_videobox_settings WHERE pid=?")
                     ->execute($dc->id);

              // no entry yet - redirect to the create page
              if ($objCheck->numRows < 1) {
                     $this->redirect('contao/main.php?do=videobox&table=tl_videobox_settings&act=create&mode=2&pid=' . $dc->id);
              }

              // else redirect to the existing entry
              $this->redirect('contao/main.php?do=videobox&table=tl_videobox_settings&act=edit&id=' . $objCheck->id);

       }


       /**
        * Prepare video template data
        * @param int video id
        * @param int jumpTo page
        * @return array
        */
       public function prepareVideoTemplateData($intVideoId, $intJumpTo = false)
       {
              $arrReturn = array();
              $objVideo = new VideoBoxElement($intVideoId);
              $arrReturn['video'] = $objVideo->generate();
              $arrReturn['videoData'] = $objVideo->getData();
              $arrReturn['title'] = $objVideo->videotitle;

              if ($intJumpTo) {
                     // jumpTo gets cached automatically
                     $objJumpTo = $this->Database->prepare('SELECT id,alias FROM tl_page WHERE id=?')->execute($intJumpTo);
                     $arrReturn['href'] = ampersand($this->generateFrontendUrl($objJumpTo->row(), '/video/' . ((!$GLOBALS['TL_CONFIG']['disableAlias'] && $objVideo->alias != '') ? $objVideo->alias : $objVideo->videoid)));
              }

              // thumb
              if ($objVideo->thumb) {
                     $objImgData = new stdClass();
                     $arrItem = array_merge($arrReturn['videoData'], array
                     (
                            'singleSRC' => $objVideo->thumb
                     ));

                     $this->addImageToTemplate($objImgData, $arrItem);
                     $arrReturn['imgData'] = $objImgData;
              }

              return $arrReturn;
       }
}