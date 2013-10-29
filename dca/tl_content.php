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
 * Add palettes to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['calendar_promotion'] = '{type_legend},type,headline;{calendar_promotion},calendar_promotion_archive,calendar_promotion_description,calendar_promotion_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['calendar_promotion_archive'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['calendar_promotion_archive'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'	     => array('CalendarPromotionHelpers', 'getCalendarPromotions'),
	'eval'			     => array('mandatory'=>true)
);

$GLOBALS['TL_DCA']['tl_content']['fields']['calendar_promotion_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['calendar_promotion_template'],
	'exclude'                 => true,
       'inputType'               => 'select',
       'options_callback'	       => array('CalendarPromotionHelpers', 'getTemplateList'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC']
);
