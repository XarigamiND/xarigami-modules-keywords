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
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function keywords_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();
    $module = 'keywords';
    /* Check and see if we have a module alias */
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname =  xarModGetVar($module,'aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }
    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/roles

    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar('roles','aliasname');
    }
    if (empty($params[1])) {
          return array('main', $args);
    } elseif (preg_match('/^tab-/', $params[1])) {
        $args['tab'] = substr($params[1],4);

        return array('main',$args);
    } elseif (!empty($params[1])){
        //$args['keyword'] = rawurldecode($params[1]);
        $args['keyword'] = $params[1];
        if (!empty($params[2]) && is_numeric($params[2])) {
            $args['id'] = $params[2];
        }
        return array('main',$args);
    } else {

    // default : return nothing -> no short URL decoded
    }
}
?>