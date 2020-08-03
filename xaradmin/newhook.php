<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2013 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author Keywords Development Team
 */
/**
 * Create an entry for a module item - hook for ('item','new','GUI')
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @return string hook output in HTML
 */
function keywords_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
      $extrainfo = array();
    }
     // new item won't have an id yet
    if (!isset($objectid)) {
        $objectid = null;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
         return $extrainfo;//return so next hook doesn't fail
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0; //default when no itemtypes
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $itemid = 0; //new item default
    }
    //get configuration options up front
    $canadd  = xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:All");
    $canadd = $canadd ? TRUE: FALSE;
    $allowadd = xarModGetVar('keywords','allowadd');


    //Retrieve the list of allowed delimiters and use the first one as the default.
    $delimiters = xarModGetVar('keywords','delimiters');
    //use the first one as a default for our purpose
    $delimiter = substr($delimiters,0,1);

    $restricted = xarModGetVar('keywords','restricted');

    //grab the keywords passed in via the listing selected by the user
    if (isset($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        xarVarFetch('keywords', 'str', $keywords, '', XARVAR_NOT_REQUIRED);
    }

    //see if we have some newly captured keywords - when not restricted or admin allow input
    if (isset($extrainfo['newkeywords']) && !empty($extrainfo['newkeywords'])) {
        $newkeywords = $extrainfo['newkeywords'];
    } else {
        //see if we have some newly captured keywords - when not restricted or admin allow input
        xarVarFetch('newkeywords', 'str', $newkeywords, NULL, XARVAR_NOT_REQUIRED);
        if (!is_null($newkeywords)) {
            if ($restricted == 0) {
            } else {
                $keywords =  $keywords.$delimiter.$newkeywords;
            }
        }
    }
     //make sure they are put into an array - now have all selected keywords and those added too
    $keywords = xarModAPIFunc('keywords','admin','separekeywords',
                          array('keywords' => $keywords));

    $keywords = array_unique($keywords);
    $cleanwords = array();
    //clean up the array and trim in case of spaces
    foreach ($keywords as $word) {
        $word = trim($word);
        if (empty($word)) continue;
        $cleanwords[] = $word;
    }

   $keywords = $cleanwords;
    if ($restricted == '0') {
        // $keywords is delivered as string typed in by the user and delimited - there is no set wordlist to choose from
         $wordlist = array();
        if (is_null($newkeywords)) {
            $newkeywords = implode($delimiter,$keywords);
        } else {
            $newkeywords = explode($delimiter,$newkeywords);
            $newkeywords = trim(implode($delimiter,array_unique($newkeywords)),$delimiter);
        }
    } else  {
        //we already have the selected keywords in an array
        // NowGet array of predefined words that are allowed for this mod and itemtype
        $availablewords = xarModAPIFunc('keywords', 'user', 'getwordslimited',
                                   array('moduleid' => $modid,
                                         'itemtype' => $itemtype)
        );
        //now get the difference between what we selected and what is allowed
        $wordlist=array_diff( $availablewords, $keywords);
        $newkeywords = '';
    }

    //wordlist is the list of words not selected to go into the array for selection
    //keywords are the selected words from the array
    //newkeywords are those in the input string
    return xarTplModule('keywords','admin','newhook',
                        array('keywords'   => $keywords, // list of restricted keywords in selection box - retaining old template vars to minimize template rework
                              'wordlist'   => $wordlist,
                              'delimiters' => $delimiters,
                              'delimiter'  => $delimiter,
                              'allowadd'   => $allowadd,
                              'newkeywords'=> $newkeywords,
                              'canadd'     => $canadd,
                              'restricted' => $restricted));

}

?>
