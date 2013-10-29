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

// BE MOD
$GLOBALS['BE_MOD']['content']['calendar_promotion'] = array
(
    'tables'               => array('tl_calendar_promotion_archive','tl_calendar_promotion'),
    'icon'         		   => 'system/modules/calendar_promotion/assets/promo.jpg',
);



// CE
array_insert($GLOBALS['TL_CTE'], 2, array
(
	'Verkaufsaktionen' => array
	(
		'calendar_promotion'     => 'CalendarPromotion'
	)
));

