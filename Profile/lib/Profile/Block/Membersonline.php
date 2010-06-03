<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c), Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: membersonline.php 100 2010-02-02 17:19:36Z yokav $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Profile
 */

// Ported from : i-Block [Members Online 2.5] - MagicX - Portalzine.de
class Profile_Block_Membersonline extends AbstractBlock
{
    /**
     * initialise block
     *
     * @author       The Zikula Development Team
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Profile:MembersOnlineblock:', 'Block title::');
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
                'text_type'       => $this->__('Users on-line'),
                'text_type_long'  => $this->__('Show which registered users are currently on-line'),
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
        // Check if the Profile module is available.
        if (!pnModAvailable('Profile')) {
            return false;
        }

        // Security check
        if (!SecurityUtil::checkPermission('Profile:MembersOnlineblock:', "$blockinfo[title]::", ACCESS_READ)) {
            return false;
        }

        // Get variables from content block
        $vars = pnBlockVarsFromContent($blockinfo['content']);

        // Defaults
        if (empty($vars['lengthmax'])) {
            $vars['lengthmax'] = 30;
        }

        $uid         = (int)pnUserGetVar('uid');
        $users       = pnModAPIFunc('Profile', 'memberslist', 'getallonline');
        $usersonline = array();

        if ($users) {
            foreach($users['unames'] as $user) {
                $usersonline[] = $user;
            }
        }

        $render = & pnRender::getInstance('Profile', false);
        $render->cache_id = $uid;

        // check which messaging module is available and add the necessary info
        $msgmodule = pnModAPIFunc('Profile', 'memberslist', 'getmessagingmodule');
        if (!empty($msgmodule) && pnUserLoggedIn()) {
            $render->assign('messages', pnModAPIFunc($msgmodule, 'user', 'getmessagecount'));
        }

        $render->assign('msgmodule',   $msgmodule);
        $render->assign('maxLength',   $vars['lengthmax']);
        $render->assign('usersonline', $usersonline);
        $render->assign('membonline',  $users['numusers']);
        $render->assign('anononline',  $users['numguests']);
        $render->assign('uid',         $uid);

        $blockinfo['content'] = $render->fetch('profile_block_membersonline.htm');

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
        if (empty($vars['lengthmax'])) {
            $vars['lengthmax'] = 30;
        }

        // Create output object
        $render = & pnRender::getInstance('Profile', false);

        // assign the approriate values
        $render->assign('lengthmax', $vars['lengthmax']);

        // Return the output that has been generated by this function
        return $render->fetch('profile_block_membersonline_modify.htm');
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
        $vars['lengthmax'] = (int)FormUtil::getPassedValue('lengthmax', null, 'REQUEST');

        // write back the new contents
        $blockinfo['content'] = pnBlockVarsToContent($vars);

        // clear the block cache
        $render = & pnRender::getInstance('Profile');
        $render->clear_cache('profile_block_membersonline.htm');

        return $blockinfo;
    }

    