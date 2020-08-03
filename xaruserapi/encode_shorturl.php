<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author mikespub
 */

/**
 * return the path for a short URL to xarModURL for this module
 *
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function keywords_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
    $path = array();
    $get = $args;
    $module = 'keywords';
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module, 'aliasname');

    if (!empty($aliasisset) && !empty($aliasname)) {
        $module_for_alias = xarModGetAlias($aliasname);
        if ($module_for_alias == $module) {
            $module = $aliasname;
        }
    }
    $path[] = $module;

    if ($func == 'main') {
        unset($get['func']);
        if (!empty($tab)) {
            $path[] = 'tab-'.$tab;
            unset($get['tab']);
        } elseif (!empty($keyword)) {
            $path[] = $keyword;
            unset($get['keyword']);
            if (!empty($id)) {
                $path[] = $id;
               unset($getp['id']);
            }
        }
    } else {

        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }
    return array('path'=>$path,'get'=>$get);
}

?>
