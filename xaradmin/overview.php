<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author Keywords Development Team
 * @author mikespub
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 */
function keywords_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminKeywords',0)) return xarResponseForbidden();

    $data=array();
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('keywords','admin','getmenulinks');  
    return xarTplModule('keywords', 'admin', 'main', $data,'main');
}

?>
