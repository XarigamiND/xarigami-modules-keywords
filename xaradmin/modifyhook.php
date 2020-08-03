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
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo']
 * @param string $args['extrainfo']['keywords'] or 'keywords' from input (optional)
 * @returns string
 * @return hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_admin_modifyhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
      $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
       return $extrainfo;
    }
   // We can exit immediately if the status flag is set because we are just updating
    // the status in the articles or other content module that works on that principle
    // Bug 1960 and 3161
    if (xarVarIsCached('Hooks.all','noupdate') || !empty($extrainfo['statusflag'])){
        return $extrainfo;
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

    $canadd  = xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:All");
    $canadd = $canadd ? TRUE: FALSE;
    $allowadd = xarModGetVar('keywords','allowadd');

    //Retrieve the list of allowed delimiters and use the first one as the default.
    $delimiters = xarModGetVar('keywords','delimiters');
    //use the first one as a default for our purpose
    $delimiter = substr($delimiters,0,1);

    $restricted = xarModGetVar('keywords','restricted');

    //get any existing keywords for this item saved in the db as array
    $oldwords = xarModAPIFunc('keywords','user','getwords',
                        array('modid'    => $modid,
                              'itemtype' => $itemtype,
                             'itemid'    => $itemid));

    if (isset($oldwords) && count($oldwords) > 0) {
       $oldwords = join($delimiter,$oldwords);
    }

    if (isset($extrainfo['keywords']) && !empty($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
          xarVarFetch('keywords', 'str', $keywords, NULL, XARVAR_NOT_REQUIRED);
          if(is_null($keywords)) {
            $keywords = $oldwords;
          }
    }
    if (!isset($keywords) || empty($keywords)) $keywords = ''; //sometimes it is an array ... should be string here
    //see if we have some newly captured keywords - when not restricted or admin allow input
    xarVarFetch('newkeywords', 'str', $newkeywords, NULL, XARVAR_NOT_REQUIRED);
    if (!is_null($newkeywords)) {
        if ($restricted == 0) {
        } else {
            $keywords =  $keywords.$delimiter.$newkeywords;
        }
    }


    //turn it into an array
    $keywords = xarModAPIFunc('keywords','admin','separekeywords',
                          array('keywords' => $keywords));


    $cleanwords = array();
    foreach ($keywords as $word) {
        $word = trim($word);
        if (empty($word)) continue;
        $cleanwords[] = $word;
    }
    $keywords = array_unique($cleanwords);
    $delimiternumber =  mb_strlen(trim($delimiters));
    $useitemtypes =   xarModGetVar('keywords','useitemtype');

    if ($restricted == 1) {
        //get the list of available words
        $availablewords = xarModAPIFunc('keywords', 'user', 'getwordslimited',
                                   array('moduleid' => $modid,
                                         'itemtype' => $itemtype));
        //ok got all the existing list of available keywords from the database
        //now only get the new ones to add
       // $newkeywords =implode($delimiter, array_diff($keywords, $availablewords));
       $newkeywords = '';
        $wordlist=array_diff($availablewords, $keywords);

    } else {
        $wordlist = array();
        if (is_null($newkeywords)) {
            $newkeywords = implode($delimiter,$keywords);
        } else {
            $newkeywords = explode($delimiter,$newkeywords);
            $newkeywords = trim(implode($delimiter,array_unique($newkeywords)),$delimiter);
        }
    }

    //wordlist is the list of words not selected to go into the array for selection
    //keywords are the selected words from the array
    //newkeywords are those in the input string
    //if not restricted then they all go into the newkeywords string

    return xarTplModule('keywords', 'admin', 'modifyhook',
                        array('keywords'    => $keywords,
                              'wordlist'    => $wordlist,
                              'delimiters'  => $delimiters,
                              'delimiter'   => $delimiter,
                              'canadd'      => $canadd,
                              'allowadd'    => $allowadd,
                              'newkeywords' => $newkeywords,
                              'restricted'  => $restricted));
}

?>
