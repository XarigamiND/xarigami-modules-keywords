<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author mikespub
 */
/**
 * Update configuration
 * @param int restricted
 * @param int useitemtype
 * @param array keywords (default = empty)
 * @return mixed. true on succes and redirect to URL
 */
function keywords_admin_updateconfig()
{

    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AdminKeywords')) return xarResponseForbidden();
    $deftablist = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
    $deftablistother = 'other';
    xarVarFetch('restricted','int:0:1',$restricted, 0);
    xarVarFetch('useitemtype','int:0:1',$useitemtype, 0);
    xarVarFetch('keywords','isset',$keywords,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);
    xarVarFetch('showsort','isset',$showsort,'', XARVAR_DONT_SET);
    xarVarFetch('displaycolumns','isset',$displaycolumns,'', XARVAR_DONT_SET);
    xarVarFetch('delimiters','isset',$delimiters,'', XARVAR_DONT_SET);
    xarVarFetch('allowadd','checkbox', $allowadd,FALSE, XARVAR_DONT_SET);
    xarVarFetch('tablist','str:1:', $tablist,$deftablist, XARVAR_DONT_SET);
    xarVarFetch('tablistother','str:1:', $tablistother,$deftablistother, XARVAR_DONT_SET);
   if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('keywords','restricted',$restricted);
    xarModSetVar('keywords','useitemtype',$useitemtype);
    xarModSetVar('keywords','allowadd',$allowadd);
    xarModSetVar('keywords','tablist',$tablist);
    xarModSetVar('keywords','tablistother',$tablistother);
    if (isset($keywords) && is_array($keywords)) {
        xarModAPIFunc('keywords', 'admin', 'resetlimited'
        );
        foreach ($keywords as $modname => $value) {
            if ($modname == 'default.0' || $modname == 'default') {
                $moduleid='0';
                $itemtype = '0';
            } else {
                $moduleitem = explode(".", $modname);
                $moduleid = xarModGetIDFromName($moduleitem[0],'module');
                if (isset($moduleitem[1]) && is_numeric($moduleitem[1])) {
                    $itemtype = $moduleitem[1];
                } else {
                    $itemtype = 0;
                }
            }
            if ($value <> '') {
                xarModAPIFunc('keywords', 'admin', 'limited',
                              array('moduleid' => $moduleid,
                                    'keyword'  => $value,
                                    'itemtype' => $itemtype)
                );
            }
        }
    }
    if (empty($isalias)) {
        xarModSetVar('keywords','SupportShortURLs',0);
    } else {
        xarModSetVar('keywords','SupportShortURLs',1);
    }
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('keywords', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('keywords', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('keywords','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('keywords','useModuleAlias');
    //<jojodee> we need to ensure any old aliases are deleted

    // if a new one is set or if there is an old one there and we don't want to use alias anymore
    if ($useAliasName && !empty($newalias)) {
         if ($aliasname != $currentalias)
         /* First check for old alias and delete it */
            if (isset($hasalias) && ($hasalias =='keywords')){
                xarModDelAlias($currentalias,'keywords');
            }
            /* now set the new alias if it's a new one */
            $newalias = xarModSetAlias($newalias,'keywords');
            if (!$newalias) { //name already taken so unset
                 xarModSetVar('keywords', 'aliasname', '');
                 xarModSetVar('keywords', 'useModuleAlias', false);
            } else { //it's ok to set the new alias name
                xarModSetVar('keywords', 'aliasname', $aliasname);
                xarModSetVar('keywords', 'useModuleAlias', $modulealias);
            }
    } else {
       //remove any existing alias and set the vars to none and false
            if (isset($hasalias) && ($hasalias =='keywords')){
                xarModDelAlias($currentalias,'keywords');
            }
            xarModSetVar('keywords', 'aliasname', '');
            xarModSetVar('keywords', 'useModuleAlias', false);
    }


    if (empty($showsort)) {
        xarModSetVar('keywords','showsort',0);
    } else {
        xarModSetVar('keywords','showsort',1);
    }
    if (empty($displaycolumns)) {
        xarModSetVar('keywords','displaycolumns',2);
    } else {
        xarModSetVar('keywords','displaycolumns',$displaycolumns);
    }
    if (isset($delimiters)) {
        xarModSetVar('keywords','delimiters',trim($delimiters));
    }
        $msg = xarML('Keyword configuration successfully updated.');
        xarTplSetMessage($msg,'status');
    xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));
    return true;
}
?>
