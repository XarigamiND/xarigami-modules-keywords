<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007,2008,2009 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author mikespub
 */
/**
 * utility function pass individual menu items to the Admin menu
 *
 * @author mikespub
 * @return array containing the menulinks
 */
function keywords_adminapi_getmenulinks()
{
    static $menulinks = array();
    if (isset($menulinks[0])) {
        return $menulinks;
    }
        /* Removing the view function due to usuability.  Seems over complicated, since most of the editing is done via the original item.
        $menulinks[] = Array('url'   => xarModURL('keywords','admin','view'),
                              'title' => xarML('Overview of the keyword assignments'),
                              'label' => xarML('View Keywords'));
        */
 if (xarSecurityCheck('AdminKeywords', 0)) {
        $menulinks[] = array( 'url'    => xarModURL('keywords','admin','hooks'),
                              'title'  => xarML('Configure keyword hooks'),
                              'label'  => xarML('Configure hooks'),
                              'active' => array('hooks')
        );

        $menulinks[] = array( 'url'    => xarModURL('keywords','admin','modifyconfig'),
                              'title'  => xarML('Modify the keywords configuration'),
                              'label'  => xarML('Modify Config'),
                              'active' => array('modifyconfig')
        );
    }

    return $menulinks;
}
?>