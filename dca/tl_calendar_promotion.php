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
 * Table tl_calendar_promotion
 */

$GLOBALS['TL_DCA']['tl_calendar_promotion'] = array(
       // Config
       'config' => array(
              'ptable' => 'tl_calendar_promotion_archive',
              'dataContainer' => 'Table',
              //'onload_callback' => array(	),
              //'ondelete_callback' => array( array('tl_gallery_creator_pictures','ondeleteCb')),
       ),

       //list
       'list' => array(
              'sorting' => array
              (
                     'mode' => 4,
                     'disableGrouping' => true,
                     'fields' => array('eventtstamp ASC'),
                     'headerFields' => array('eventtitle', 'eventtype', 'year'),
                     'panelLayout' => 'filter;sort,search,limit',
                     'child_record_callback' => array('tl_calendar_promotion', 'listEvents')
              ),

              'global_operations' => array
              (
                     'all' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                            'href' => 'act=select',
                            'class' => 'header_edit_all',
                            'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
                     )
              ),
              'operations' => array
              (
                     'edit' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['edit'],
                            'href' => 'act=edit',
                            'icon' => 'edit.gif'
                     ),
                     'copy' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['copy'],
                            'href' => 'act=paste&amp;mode=copy',
                            'icon' => 'copy.gif'
                     ),
                     'delete' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['delete'],
                            'href' => 'act=delete',
                            'icon' => 'delete.gif',
                            'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
                     ),
                     'show' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['show'],
                            'href' => 'act=show',
                            'icon' => 'show.gif'
                     ),
              )
       ),

       // Palettes
       'palettes' => array(
              //'default' => 'title,description,visits,href,openInNewWindow,eventtstamp,singleSRC,mbwidth,mbheight,cssID',
              'default' => 'title,description,href,openInNewWindow,eventtstamp,singleSRC,mbwidth,mbheight,cssID',
       ),

       // Fields
       'fields' => array(
              'eventtstamp' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['eventtstamp'],
                     'inputType' => 'text',
                     'eval' => array('rgxp' => 'date', 'doNotCopy' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard')
              ),


              'title' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['title'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'eval' => array(
                            'allowHtml' => false,
                            'decodeEntities' => true,
                            'tl_class' => 'clr'
                     )
              ),
              'description' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['description'],
                     'exclude' => true,
                     'inputType' => 'textarea',
                     'eval' => array(
                            'rte' => 'tinyMCE',
                            'tl_class' => 'clr'
                     )
              ),
              'visits' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['visits'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'eval' => array(
                            'rgxp' => 'alnum',
                            'tl_class' => 'w50'
                     )
              ),
              'mbwidth' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['mbwidth'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'eval' => array(
                            'rgxp' => 'alnum',
                            'tl_class' => 'w50'
                     )
              ),
              'mbheight' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['mbheight'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'eval' => array(
                            'rgxp' => 'alnum',
                            'tl_class' => 'w50'
                     )
              ),
              'href' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['href'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'eval' => array(
                            'allowHtml' => false,
                            'decodeEntities' => true,
                            'tl_class' => 'clr'
                     )
              ),
              'openInNewWindow' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['openInNewWindow'],
                     'exclude' => true,
                     'inputType' => 'checkbox',
                     'eval' => array(
                            'tl_class' => 'clr'
                     )
              ),
              'singleSRC' => array
              (
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['singleSRC'],
                     'exclude' => true,
                     'inputType' => 'fileTree',
                     'eval' => array('fieldType' => 'radio', 'files' => true, 'filesOnly' => true, 'mandatory' => false, 'tl_class' => 'clr')
              ),
              'cssID' => array
              (
                     'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_promotion']['cssID'],
                     'exclude'                 => true,
                     'inputType'               => 'text',
                     'eval'                    => array('multiple'=>true, 'size'=>2, 'tl_class'=>'w50 clr')
              )
       )
);

/**
 * Class tl_calendar_promotion
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Controller
 */
class tl_calendar_promotion extends Backend
{

       /**
        * Add the type of input field
        * @param array
        * @return string
        */
       public function listEvents($arrRow)
       {
              $time = time();
              $date = $this->parseDate('d.m.Y', $arrRow['eventtstamp']);

              return '
<div class="cte_type"><strong>' . $date . '</strong> ' . $arrRow['title'] . '</div>
<div class="limit_height' . (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : '') . '"></div>' . "\n";
       }
}