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
 * @todo MichelV what is this?
 * @todo ? inserire controllo sicurezza
 */
function keywords_adminapi_resetlimited($args)
{
    //if (!xarSecurityCheck('AdminKeywords')) return;
    if (!xarSecurityCheck('AddKeywords')) return;
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $keywordstable = $xartable['keywords_restr'];
    $query = "DELETE FROM $keywordstable";
    $result = $dbconn->Execute($query);
    if (!$result) return;
    return true;
}
?>