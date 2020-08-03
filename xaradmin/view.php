<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author mikespub
 */

/**
 * show the links for module items
 * @return array
 */
function keywords_admin_view($args)
{
    extract($args);

    if (!xarVarFetch('modid',    'id', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'id', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminKeywords')) return xarResponseForbidden();

    $data = array();
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('keywords','admin','getmenulinks');  
    return $data;
}

?>
