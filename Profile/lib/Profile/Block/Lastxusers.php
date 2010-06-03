<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c), Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: lastxusers.php 90 2010-01-25 08:31:41Z mateo $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Profile
 */

// Ported from : i-Block [Last x Reg Users 2.4] - Alexander Graef aka MagicX - http://www.portalzine.de
class Profile_Block_Lastxusers extends AbstractApi
{
    /**
     * initialise block
     *
     * @author       The Zikula Development Team
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Profile:LastXUsersblock:', 'Block title::');
    }

    /**
     * get information on block
     *
     * @author       The Zikula Development Team
     * @return       array       The block information
     */
    public function info()
    {
        return array('module'          => 'Profile',
                'text_type'       => $this->__('Last X registered users'),
                'text_type_long'  => $this->__('Show last X registered users'),
                'allow_multiple'  => true,
                'form_content'    => false,
                'form_refresh'    => false,
                'show_preview'    => true,
                'admin_tableless' => true);
    }

    /**
     * display block
     *
     * @author       The Zikula Development Team
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the rendered bock
     */
    public function display($blockinfo)
    {
        // Check if the Profile module is available
        if (!pnModAvailable('Profile')) {
            return false;
        }

        // Security check
        if (!SecurityUtil::checkPermission('Profile:LastXUsersblock:', "$blockinfo[title]::", ACCESS_READ)) {
            return false;
        }

        // Get variables from content block
        $vars = pnBlockVarsFromContent($blockinfo['content']);

        $render = & pnRender::getInstance('Profile', false);

        // get last x logged in user id's
        $users = pnModAPIFunc('Profile', 'memberslist', 'getall',
                array('sortby' => 'user_regdate',
                'numitems' => $vars['amount'],
                'sortorder' => 'DESC'));

        $render->assign('users', $users);
        $blockinfo['content'] = $render->fetch('profile_block_lastxusers.htm');

        return pnBlockThemeBlock($blockinfo);
    }

    /**
     * modify block settings
     *
     * @author       The Zikula Development Team
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the bock form
     */
    public function modify($blockinfo)
    {
        // Get current content
        $vars = pnBlockVarsFromContent($blockinfo['content']);

        // Defaults
        if (empty($vars['amount'])) {
            $vars['amount'] = 5;
        }

        // Create output object
        $render = & pnRender::getInstance('Profile', false);

        // assign the approriate values
        $render->assign('amount', $vars['amount']);
        $render->assign('savelastlogindate', pnModGetVar('Users','savelastlogindate'));

        // Return the output that has been generated by this function
        return $render->fetch('profile_block_lastxusers_modify.htm');
    }

    /**
     * update block settings
     *
     * @author       The Zikula Development Team
     * @param        array       $blockinfo     a blockinfo structure
     * @return       $blockinfo  the modified blockinfo structure
     */
    public function update($blockinfo)
    {
        // Get current content
        $vars = pnBlockVarsFromContent($blockinfo['content']);

        // alter the corresponding variable
        $vars['amount'] = (int)FormUtil::getPassedValue('amount', null, 'REQUEST');

        // write back the new contents
        $blockinfo['content'] = pnBlockVarsToContent($vars);

        // clear the block cache
        $render = & pnRender::getInstance('Profile');
        $render->clear_cache('profile_block_lastxusers.htm');

        return $blockinfo;
    }
}