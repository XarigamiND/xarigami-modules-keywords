<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2009-2012 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Keywords Module
 * @copyright (C) 2009-2012 2skies.com
 * @link http://xarigami.com/project/xarigami_keywords
 */
/**
 * Hooks shows the configuration of hooks for other modules
 * @return array $data containing template data
 */
function keywords_admin_hooks()
{
    // Security check
    if(!xarSecurityCheck('ReadKeywords')) return;

    $data = array();

    return $data;
}

?>
