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
 * delete existing keywords assignment
 */
function keywords_admin_delete($args)
{
    extract($args);

    if (!xarVarFetch('itemid', 'id', $itemid)) return;
    if (!xarVarFetch('confirm',  'isset', $confirm,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminKeywords')) return xarResponseForbidden();

    $data = array();
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'keywords'));
    if (!isset($data['object'])) return;

    // Get current item
    $newid = $data['object']->getItem(array('itemid' => $itemid));
    if (empty($newid) || $newid != $itemid) return;

    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        // delete the item here
        $itemid = $data['object']->deleteItem();
        if (empty($itemid)) return; // throw back
        $msg = xarML('Keyword deleted.');
        xarTplSetMessage($msg,'status');
        // let's go back to the admin view
        xarResponseRedirect(xarModURL('keywords', 'admin', 'view'));
        return true;
    }
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('keywords','admin','getmenulinks');
    $data['itemid'] = $itemid;
    $data['authid'] = xarSecGenAuthKey();
    $data['confirm'] = xarML('Delete');
    return $data;
}

?>
