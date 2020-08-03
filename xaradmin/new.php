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
 * create new keywords assignment
 * @param string confirm
 * @return array with data
 */
function keywords_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('confirm',  'isset', $confirm,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarSecurityCheck('AddKeywords')) return xarResponseForbidden();

    $data = array();
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'keywords'));
    if (!isset($data['object'])) return;
    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;
        // check the input values for this object
        $isvalid = $data['object']->checkInput();
        if ($isvalid) {
            // create the item here
            $itemid = $data['object']->createItem();
            if (empty($itemid)) return; // throw back
                    $msg = xarML('Keyword added successfully.');
                    xarTplSetMessage($msg,'status');
            // let's go back to the admin view
            xarResponseRedirect(xarModURL('keywords', 'admin', 'view'));
            return true;
        }
    }
    $item = array();
    $item['module'] = 'keywords';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('keywords','admin','getmenulinks');
    $data['authid'] = xarSecGenAuthKey('keywords');
    $data['confirm'] = xarML('Create');

    return $data;
}
?>
