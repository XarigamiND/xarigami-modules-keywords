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
 * @author Keywords Development Team
 * @author mikespub
*/
/**
 * Unknown
 */
function keywords_adminapi_limited($args)
{
    extract($args);
    if (!xarSecurityCheck('AdminKeywords')) return;
    $invalid = array();
    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $invalid[] = 'moduleid';
    }
    if (!isset($keyword) || !is_string($keyword)) {
        $invalid[] = 'keyword';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update limited', 'Keywords');
        throw new BadParameterException($args, $msg);
    }

    $key = xarModAPIFunc('keywords', 'admin','separekeywords',
                          array('keywords' => $keyword));

    foreach ($key as $keyres) {
    $keyres = trim($keyres);

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $keywordstable = $xartable['keywords_restr'];
    $nextId = $dbconn->GenId($keywordstable);
    $query = "INSERT INTO $keywordstable (
              xar_id,
              xar_keyword,
              xar_moduleid,
              xar_itemtype)
              VALUES (
              ?,
              ?,
              ?,
              ?)";
    $result = $dbconn->Execute($query,array($nextId, $keyres, $moduleid, $itemtype));
    if (!$result) return;
    }
    return;
}
?>
