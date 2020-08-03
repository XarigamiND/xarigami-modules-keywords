<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author Keywords Development Team
 * @author mikespub
 */

/**
 * modify existing keywords assignment
 *
 * @param int itemid
 * @param string confirm Confirm the modification
 * @return array of data
 */
function keywords_admin_modify($args)
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

        // check the input values for this object
        $isvalid = $data['object']->checkInput();
        if ($isvalid) {
            // update the item here
            $itemid = $data['object']->updateItem();
            if (empty($itemid)) return; // throw back
                    $msg = xarML('Keyword successfully updated.');
                    xarTplSetMessage($msg,'status');
            // let's go back to the admin view
            xarResponseRedirect(xarModURL('keywords', 'admin', 'view'));
            return true;
        }
    }

    $item = array();
    $item['module'] = 'keywords';
    $hooks = xarModCallHooks('item','modify',$itemid,$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('keywords','admin','getmenulinks');
    $data['itemid'] = $itemid;
    $data['authid'] = xarSecGenAuthKey('keywords');
    $data['confirm'] = xarML('Update');
    return $data;
}

?>
