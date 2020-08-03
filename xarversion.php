<?php
/**
 * Keywords initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2007-2013 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 * @author Keywords Development Team
 */

$modversion['name']           = 'Keywords';
$modversion['id']             = '187';
$modversion['version']        = '1.1.2';
$modversion['displayname']    = 'Keywords';
$modversion['description']    = 'Assign keywords to module items (taxonomy) and look up items by keyword';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 1;
$modversion['author']         = 'mikespub,original author';
$modversion['contact']        = 'ttp://xarigami.com';
$modversion['homepage']        = 'http://xarigami.com/project/xarigami_keywords';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Utility';
$modversion['category']       = 'Utilities';
$modversion['dependencyinfo']   = array(
                                    0 => array(
                                            'name' => 'core',
                                            'version_ge' => '1.5.0'
                                         )
                                );
if (false) {
    xarML('Keywords');
    xarML('Assign keywords to module items (taxonomy) and look up items by keyword');
}

?>