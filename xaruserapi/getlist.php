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
 * get list of keywords (from the existing assignments for now)
 *
 * @param $args['count'] if you want to count items per keyword
 * @param $args['tab'] = int(1:5) returns keywords with initial withn
 *    a specific letter range (1=[A-F]; 2=[G-L]; etc...)
 * @return array of found keywords
 */
function keywords_userapi_getlist($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $keywordstable = $xartable['keywords'];
    $tablist = xarModGetVar('keywords','tablist');
    $tabarray = explode(',',$tablist);
    $wherearray = array();
    $tabother=    xarModGetVar('keywords','tablistother');
    $tablength = strlen($tab);
    //get an array of individual characters
    $tabvalues = array_reverse(explode('|',chunk_split($tab,1,'|')));
    //get rid of any empty value
    if(empty($tabvalues[0])) unset($tabvalues[0]);

    if (!isset($tab) || empty($tab) || $tab == xarML('all')){
             $wherearray[] = null;
    } elseif ($tab != xarML('other') && $tab != $tabother) {
        //we have a valid tab of x length
        foreach ($tabvalues as $t=>$tabv) {
            //check each value in the tabarray against what we have
            foreach($tabarray as $k=>$v) {
               $v = strtolower($v);
               if ($tabv == $v || stristr($v,$tabv)) {
                 $wherearray[] = "'$tabv' = ".$dbconn->substr."(LOWER(xar_keyword),1,1) ";
                 break;
                 //$found = 1;
               }

           }
        }

    } else {
        $tablist2 = '';

        foreach ($tabarray as $k =>$v) {
            $newv =  array_reverse(explode('|',chunk_split($v,1,'|')));
            if(empty($newv[0])) unset($newv[0]);
            foreach($newv as $vn) {
               $tablist2 .="'$vn',";
            }

        }
        $tablist2 = rtrim($tablist2,',');
         $wherearray[] = $dbconn->substr."(LOWER(xar_keyword),1,1)  NOT IN(" . $tablist2.")";
    }

    $where = '';
     if (count( $wherearray) > 0 && !is_null(current($wherearray))) $where = ' WHERE ' . join(' OR ',  $wherearray);

   /* if (!isset($tab)){
        $tab='0';
    }

    if ($tab == '0'){
        $where = null;
    } elseif ($tab == '1'){
        $where = " WHERE ("
        ."'A' <= ".$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) AND "
        .$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) <= 'F')";
    } elseif ($tab == '2'){
           $where = " WHERE ("
        ."'G' <= ".$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) AND "
        .$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) <= 'L')";
    } elseif ($tab == '3'){
           $where = " WHERE ("
        ."'M' <= ".$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) AND "
        .$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) <= 'R')";
    } elseif ($tab == '4'){
           $where = " WHERE ("
        ."'S' <= ".$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) AND "
        .$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) <= 'Z')";
    } elseif ($tab == '5'){
          $where = " WHERE ("
        .$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) < 'A' OR "
        .$dbconn->substr."(".$dbconn->upperCase."(xar_keyword),1,1) > 'Z')";
    }
    */


    // Get count per keyword from the database
    if (!empty($args['count'])) {
        $query = "SELECT xar_keyword, COUNT(xar_id)
                  FROM $keywordstable $where
                  GROUP BY xar_keyword
                  ORDER BY xar_keyword ASC";
        $result = $dbconn->Execute($query);
        if (!$result) return;

        $items = array();
        if ($result->EOF) {
            $result->Close();
            return $items;
        }
        while (!$result->EOF) {
            list($word,$count) = $result->fields;
            $items[$word] = $count;
            $result->MoveNext();
        }

        $result->Close();
        return $items;
    }

    // Get distinct keywords from the database
    $query = "SELECT DISTINCT xar_keyword
              FROM $keywordstable  $where
              ORDER BY xar_keyword ASC";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    $items = array();
    $items[''] = '';
    if ($result->EOF) {
        $result->Close();
        return $items;
    }
    while (!$result->EOF) {
        list($word) = $result->fields;
        $items[$word] = $word;
        $result->MoveNext();
    }
    $result->Close();
    return $items;
}

?>
