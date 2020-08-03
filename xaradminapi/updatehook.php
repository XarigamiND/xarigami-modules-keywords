<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2013 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author mikespub
 */
/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['keywords'] from arguments, or 'keywords' from input
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @return mixed true on success, false on failure. string keywords list
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        //$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
        //            'object id', 'admin', 'updatehook', 'keywords');
        //throw new BadParameterException($args, $msg);
        // we *must* return $extrainfo for now, or the next hook will fail
        return $extrainfo;
    }

    if (!isset($extrainfo) || !is_array($extrainfo)) {
        //$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
        //            'extrainfo', 'admin', 'updatehook', 'keywords');
        //throw new BadParameterException($args, $msg);
       $extrainfo= array();
    }

    // We can exit immediately if the status flag is set because we are just updating
    // the status in the articles or other content module that works on that principle
    // Bug 1960 and 3161
    if (xarVarIsCached('Hooks.all','noupdate') || !empty($extrainfo['statusflag'])){
        return $extrainfo;
    }

    // When called via hooks, the module name may be empty. Get it from current module.
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);

    if (empty($modid)) {
        //$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
        //            'module name', 'admin', 'updatehook', 'keywords');
        //throw new BadParameterException($args, $msg);
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        //$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
        //            'item id', 'admin', 'updatehook', 'keywords');
        //throw new BadParameterException($args, $msg);
        return $extrainfo;
    }

    if (!xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:$itemid")) {
        return $extrainfo;
    }

   //retrieve the list of allowed delimiters
    $delimiters = xarModGetVar('keywords','delimiters');
    //retrieve the list of allowed delimiters.  use the first one as the default.
    $delimiter = substr($delimiters,0,1);
    //is this restricted keywords - we need to update the restricted list
    $restricted = xarModGetVar('keywords','restricted');


    // check if we need to save some keywords here
    if (isset($extrainfo['keywords']) && is_string($extrainfo['keywords']) && !empty($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        xarVarFetch('keywords', 'str:1:', $keywords, NULL, XARVAR_NOT_REQUIRED);
    }

     // check if we need to save some keywords here
    if (isset($extrainfo['newkeywords']) && is_string($extrainfo['newkeywords']) && !empty($extrainfo['keywords'])) {
        $keywords = $extrainfo['newkeywords'];
    } else {
        xarVarFetch('newkeywords', 'str:1:', $newkeywords, NULL, XARVAR_NOT_REQUIRED);
         if (!is_null($newkeywords)) {
            $keywords = $newkeywords;
        }
    }

    $keywords = ltrim($keywords.$delimiter.$newkeywords,$delimiter);
    $words = xarModAPIFunc('keywords','admin','separekeywords',
                          array('keywords' => $keywords));


    // CHECK is this needed? separekeywords already trims the words
    $cleanwords = array();
    foreach ($words as $word) {
        $word = trim($word);
        if (empty($word)) continue;
        $cleanwords[] = $word;
    }
    $cleanwords = array_unique($cleanwords);
/* TODO: restrict to predefined keyword list
    $restricted = xarModGetVar('keywords','restricted');
    if (!empty($restricted)) {
        $wordlist = array();
        if (!empty($itemtype)) {
            $getlist = xarModGetVar('keywords',$modname.'.'.$itemtype);
        } else {
            $getlist = xarModGetVar('keywords',$modname);
        }
        if (!isset($getlist)) {
            $getlist = xarModGetVar('keywords','default');
        }
        if (!empty($getlist)) {
            $wordlist = explode(',',$getlist);
        }
        if (count($wordlist) > 0) {
            $acceptedwords = array();
            foreach ($cleanwords as $word) {
                if (!in_array($word, $wordlist)) continue;
                $acceptedwords[] = $word;
            }
            $cleanwords = $acceptedwords;
        }
    }
*/

    // get the current keywords for this item
    $oldwords = xarModAPIFunc('keywords','user','getwords',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    $delete = array();
    $keep = array();
    $new = array();

    // check what we need to delete, what we can keep, and what's new
    if (isset($oldwords) && count($oldwords) > 0) {
        foreach ($oldwords as $id => $word) {
            if (!in_array($word,$cleanwords)) {
                $delete[$id] = $word;
            } else {
                $keep[] = $word;
            }
        }
        foreach ($cleanwords as $word) {
            if (!in_array($word,$keep)) {
                $new[] = $word;
            }
        }
        if (count($delete) == 0 && count($new) == 0) {
            $extrainfo['keywords'] = join(' ',$cleanwords);

            return $extrainfo;
        }
    } else {
        $new = $cleanwords;
    }

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    if (count($delete) > 0) {
        // Delete old words for this module item
        $idlist = array_keys($delete);
        $query = "DELETE FROM $keywordstable
                  WHERE xar_id IN (" . join(', ',$idlist) . ")";

        $result = $dbconn->Execute($query);
        if (!$result) {
            return $extrainfo;
        }
    }

    if (count($new) > 0) {
        foreach ($new as $word) {

            // Get a new keywords ID
            $nextId = $dbconn->GenId($keywordstable);
            // Create new keywords
            $query = "INSERT INTO $keywordstable (xar_id,
                                               xar_keyword,
                                               xar_moduleid,
                                               xar_itemtype,
                                               xar_itemid)
                    VALUES (?,
                            ?,
                            ?,
                            ?,
                            ?)";

            $result = $dbconn->Execute($query,array($nextId, $word, $modid, $itemtype, $objectid));
            if (!$result) {
                return $extrainfo;
            }
            //$keywordsid = $dbconn->PO_Insert_ID($keywordstable, 'xar_id');
        }
    }

     $delimiternumber =  mb_strlen(trim($delimiters));
    $useitemtypes =   xarModGetVar('keywords','useitemtype');

     //ok now let's put each word into the appropriate restricted list
    if ($restricted && isset($newkeywords) && !empty($newkeywords)) {
       // xarModAPIFunc('keywords', 'admin', 'resetlimited');
        //first expand the newkeywords by delimiters
        $delimiterarray = array();
        $newkeywordarray = array();
        if ($newkeywords > 1) {
            for ($i = 0; $i < $delimiternumber; $i++) {
                $delimitertemp= substr($delimiters,$i,1);
                if (strpos($newkeywords, $delimitertemp)) {
                    $newkeywordarray = explode($delimitertemp,$newkeywords);
                    break;
                }
            }
        } else {
            $newkeywordarray[] = $newkeywords;
        }
        //now we have an array of new keywords
        //let's get the old ones as we need to ensure we dont' add duplicates
        if ($useitemtypes && isset($itemtype) && isset($modid)) {
            $itype = $itemtype;
            $moduleid = $modid;
            $modkeywords = xarModAPIFunc('keywords','admin','getwordslimited',
                                            array('moduleid' => $moduleid,
                                                  'itemtype' => $itype));
            $modkeywords = explode($delimiter,$modkeywords);
        } else {
            $itype= 0;
            $moduleid = $modid;
            $modkeywords= xarModAPIFunc('keywords', 'admin', 'getwordslimited',
                          array('moduleid' => $modid));
                $modkeywords = explode($delimiter,$modkeywords);
        }
        foreach($modkeywords as $key =>$word) {
            $modkeywords[$key] = trim($word);
        }
        //ok got all the existing keywords from the database
        //now only get the new ones to add
        $finalkeywords = array_diff($newkeywordarray,$modkeywords);
        foreach ($finalkeywords as $key=> $value) {
            if ($value <> '') {
                xarModAPIFunc('keywords', 'admin', 'limited',
                              array('moduleid' => $moduleid,
                                    'keyword'  => $value,
                                    'itemtype' => $itype)
                );
            }
        }
    }
    $extrainfo['keywords'] = join(' ',$cleanwords);

    // Return extrainfo or the next hook will fail
    return $extrainfo;
}
?>
