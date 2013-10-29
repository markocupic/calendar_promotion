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
 * Table tl_calendar_promotion_archive
 */
$GLOBALS['TL_DCA']['tl_calendar_promotion_archive'] = array
(
       // Config
       'config' => array
       (
              'dataContainer' => 'Table',
              'ctable' => array('tl_calendar_promotion'),
              'switchToEdit' => true,
              'enableVersioning' => true,
              'onload_callback' => array
              (
                     array('tl_calendar_promotion_archive', 'setPalette'),
                     //array('tl_news_archive', 'generateFeed')
              ),
              'onsubmit_callback' => array
              ( // array('tl_news_archive', 'autoGenerateChildRecords')
              )
       ),

       // List
       'list' => array
       (
              'sorting' => array
              (
                     'mode' => 1,
                     'fields' => array('eventtype', 'tstamp'),
                     'flag' => 1,
                     'panelLayout' => 'filter;search,limit'
              ),
              'label' => array
              (
                     'fields' => array('eventtitle', 'eventtype'),
                     'format' => '%s (%s)'
              ),
              'global_operations' => array
              (
                     'all' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                            'href' => 'act=select',
                            'class' => 'header_edit_all',
                            'attributes' => 'onclick="Backend.getScrollOffset();"'
                     )
              ),
              'operations' => array
              (
                     'edit' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['edit'],
                            'href' => 'table=tl_calendar_promotion',
                            'icon' => 'edit.gif',
                            'attributes' => 'class="contextmenu"'
                     ),
                     'autoGenerateChildRecords' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['autoGenerateChildRecords'],
                            'href' => 'action=autoGenerateChildRecords',
                            'icon' => 'copychilds.gif',
                     ),
                     'copy' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['copy'],
                            'href' => 'act=copy',
                            'icon' => 'copy.gif',
                     ),
                     'delete' => array
                     (
                            'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['delete'],
                            'href' => 'act=delete',
                            'icon' => 'delete.gif',
                            'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                     )

              )
       ),

       // Palettes
       'palettes' => array
       (
              //'__selector__'     => array('xxx'),
              'default' => '{title_legend},eventtitle,eventtype,year,tolerance,singleSRC,errormessage;',
              'annual' => '{title_legend},videotitle,alias,videotype;{youtube_legend},thumb,size,descr,youtube_id;'
       ),

       // Fields
       'fields' => array
       (
              'eventtitle' => array
              (
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['eventtitle'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'eval' => array('mandatory' => true, 'tl_class' => 'w50')
              ),

              'year' => array
              (
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['year'],
                     'exclude' => true,
                     'inputType' => 'select',
                     'options' => range(date('Y') - 2, date('Y') + 5),
                     'default' => date('Y'),
                     'eval' => array('mandatory' => true, 'tl_class' => 'w50')
              ),

              'eventtype' => array
              (
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['eventtype'],
                     'exclude' => true,
                     'default' => 'adventskalender',
                     'inputType' => 'select',
                     'default' => 'adventskalender',
                     'options' => array('adventskalender',
                            //'kalenderwochenevent'
                     ),
                     'eval' => array('mandatory' => true, 'submitOnChange' => true, 'includeBlankOption' => false, 'tl_class' => 'clr')
              ),
              'tolerance' => array
              (
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['tolerance'],
                     'exclude' => true,
                     'inputType' => 'text',
                     'default' => '0',
                     'eval' => array('mandatory' => true, 'maxlength' => 3, 'rgxp' => 'alnum', 'tl_class' => 'clr')
              ),
              'singleSRC' => array
              (
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['singleSRC'],
                     'exclude' => true,
                     'inputType' => 'fileTree',
                     'eval' => array('fieldType' => 'radio', 'files' => true, 'filesOnly' => true, 'mandatory' => false, 'tl_class' => 'clr')
              ),
              'errormessage' => array(
                     'label' => &$GLOBALS['TL_LANG']['tl_calendar_promotion_archive']['errormessage'],
                     'exclude' => true,
                     'inputType' => 'textarea',
                     'eval' => array(
                            'rte' => 'tinyMCE',
                            'tl_class' => 'clr'
                     )
              ),
       )
);

class tl_calendar_promotion_archive extends Backend
{
       public function __construct()
       {
              $this->import('Input');
              $this->import('Database');
              if ($this->Input->get('action') == 'autoGenerateChildRecords') {
                     $id = $this->Input->get('id');

                     $objEventArchive = $this->Database->prepare('SELECT * FROM tl_calendar_promotion_archive WHERE id=?')
                            ->execute($id);

                     // check for child Records
                     $objEvent = $this->Database->prepare('SELECT * FROM tl_calendar_promotion WHERE pid=?')
                            ->execute($id);
                     if (!$objEvent->numRows) {
                            // case Adventskalender
                            for ($day = 1; $day < 25; $day++) {
                                   $set = array(
                                          'pid' => $id,
                                          'tstamp' => time(),
                                          'eventtstamp' => mktime(0, 0, 0, 12, $day, $objEventArchive->year),
                                          'displayorder' => rand(1, 999999999)
                                   );

                                   $objInsert = $this->Database->prepare('INSERT INTO tl_calendar_promotion %s')
                                          ->set($set)
                                          ->execute();
                            }
                     }
                     $this->redirect('main.php?do=calendar_promotion&table=tl_calendar_promotion&id=' . $id);
              }
       }

       public function setPalette()
       {

       }

}