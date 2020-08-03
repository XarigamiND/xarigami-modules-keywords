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
 * @copyright (C) 2007-2013 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author Keywords Development Team
 * @author mikespub
 */
/**
 * display keywords entry for a module item - hook for ('item','display','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return mixed Array with information for the template that is called.
 * @throws BAD_PARAM, NO_PERMISSION
 */
function keywords_user_displayhook($args)
{
    if (!xarSecurityCheck('ReadKeywords',0)) return '';

    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'user', 'displayhook', 'keywords');
        throw new BadParameterException($args, $msg);
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'user', 'displayhook', 'keywords');
        throw new BadParameterException($args, $msg);
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (is_array($extrainfo) && !empty($extrainfo['module']) && is_string($extrainfo['module'])) {
        $modname = $extrainfo['module'];
    } else {
        $modname = xarModGetName();
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name ' . $modname, 'user', 'displayhook', 'keywords');
        throw new BadParameterException($args, $msg);
    }

    if (is_array($extrainfo) && isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (is_array($extrainfo) && isset($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    //in case we are using an alias
    $useAliasName=xarModGetVar('keywords', 'useModuleAlias');
    $aliasname= xarModGetVar('keywords','aliasname');
    if (isset($useAliasName) && $useAliasName==1 && isset($aliasname) && !empty($aliasname)) {
        $label = ucfirst($aliasname);
    } else {
        $label=xarML('Keywords');
    }

    $words = xarModAPIFunc('keywords','user','getwords',
                           array('modid' => $modid,
                                 'itemtype' => $itemtype,
                                 'itemid' => $itemid));
    // return empty string here
    if (empty($words) || !is_array($words) || count($words) == 0) return '';

    $data = array();

    $data['words'] = array();
    foreach ($words as $id => $word) {
       $item = array();
       $item['id'] = $id;
       $item['url'] = xarModURL(
           'keywords','user','main',
           array('keyword' => $word)
       );
       $item['keyword'] = xarVarPrepForDisplay($word);
       $data['words'][$id] = $item;
    }

    $keys = implode(",",$words);
    xarVarSetCached('Blocks.keywords','keys',$keys);
 $data['label'] = $label;

    return xarTplModule('keywords', 'user', 'displayhook', $data);
}

?>