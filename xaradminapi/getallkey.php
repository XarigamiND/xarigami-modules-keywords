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
 * get entries for a module item
 *
 * @param $args['modid'] module id
 * @returns array
 * @return array of keywords
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_getallkey($args)
{
    extract($args);

    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module id', 'user', 'getwordslimited', 'keywords');
        throw new BadParameterException($args, $msg);
    }

    if (!xarSecurityCheck('AdminKeywords')) return;
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $keywordstable = $xartable['keywords_restr'];
    // Get restricted keywords for this module item
    $query = "SELECT xar_id,
                     xar_keyword
              FROM $keywordstable
              WHERE xar_moduleid = ?
              OR xar_moduleid = '0'
              ORDER BY xar_keyword ASC";
    $result = $dbconn->Execute($query,array($moduleid));
    if (!$result) return;

    $keywords = array();

    //$keywords[''] = '';
    if ($result->EOF) {
        $result->Close();
        return $keywords;
    }

    while (!$result->EOF) {
        list($id,
             $word) = $result->fields;
        $keywords[$id] = $word;
        $result->MoveNext();
    }
    $result->Close();
    return $keywords;
}
?>