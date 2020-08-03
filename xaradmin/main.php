<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
 * the main administration function
 *
 * Redirects to modifyconfig
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return bool true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function keywords_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminKeywords')) return xarResponseForbidden();
    $data = array();
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('keywords','admin','getmenulinks');  
    //redirect to default function    
    xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));
    // success
    return $data;//true;
}
?>