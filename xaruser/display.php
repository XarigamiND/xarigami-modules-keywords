<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author Keywords Development Team
 * @author mikespub
 */
/**
 * display keywords entry
 *
 * @param $args['itemid'] item id of the keywords entry
 * @return array Item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_user_display($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return xarResponseForbidden();

    xarVarFetch('itemid','id',$itemid,'', XARVAR_DONT_SET);
    extract($args);

    if (empty($itemid)) {
        return array();
    }
    $items = xarModAPIFunc('keywords','user','getitems',
                          array('id' => $itemid));
    if (!isset($items)) return;
    if (!isset($items[$itemid])) return array();

    $item = $items[$itemid];
    if (count($item) == 0 || empty($item['moduleid'])) return array();

    $modinfo = xarModGetInfo($item['moduleid']);
    if (!isset($modinfo) || empty($modinfo['name'])) return array();

    if (!empty($item['itemtype'])) {
        // Get the list of all item types for this module (if any)
        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        if (isset($mytypes) && isset($mytypes[$item['itemtype']])) {
            $item['modname'] = $mytypes[$item['itemtype']]['label'];
        } else {
            $item['modname'] = ucwords($modinfo['name']);
        }
    } else {
        $item['modname'] = ucwords($modinfo['name']);
    }

    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $item['itemtype'],
                                     'itemids' => array($item['itemid'])),
                               0);

    if (isset($itemlinks[$item['itemid']]) && !empty($itemlinks[$item['itemid']]['url'])) {
        // normally we should have url, title and label here
        foreach ($itemlinks[$item['itemid']] as $field => $value) {
            $item[$field] = $value;
        }
    } else {
        $item['url'] = xarModURL($modinfo['name'],'user','display',
                                 array('itemtype' => $item['itemtype'],
                                       'itemid' => $item['itemid']));
    }
    //in case we are using an alias
    $useAliasName=xarModGetVar('keywords', 'useModuleAlias');
    $aliasname= xarModGetVar('keywords','aliasname');
    if (isset($useAliasName) && $useAliasName==1 && isset($aliasname) && !empty($aliasname)) {
        $label = ucfirst($aliasname);
    } else {
        $label=xarML('Keywords');
    }
    $item['label'] = $label;
    return $item;
}

?>
