<?php
/**
 * Copyright Zikula Foundation 2009 - Profile module for Zikula
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Profile
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * AJAX query and response functions.
 */
class Profile_Controller_Ajax extends Zikula_AbstractController
{
    /**
     * Change the weight of a profile item.
     * 
     * Parameters passed in via FormUtil:
     * ----------------------------------
     * array   profilelist An array of dud item ids for which the weight should be changed.
     * numeric startnum    The desired weight of the first item in the list minus 1 (e.g., if the weight of the first item should be 3 then startnum contains 2)
     *
     * @return mixed An AJAX result array containing a result equal to true, or an Ajax error.
     */
    public function changeprofileweight()
    {
        if (!SecurityUtil::checkPermission('Profile::', '::', ACCESS_ADMIN)) {
            AjaxUtil::error($this->__('Sorry! You do not have authorisation for this module.'));
        }

        if (!SecurityUtil::confirmAuthKey()) {
            AjaxUtil::error($this->__("Invalid authorisation key ('authkey'). This is probably either because you pressed the 'Back' button to return to a page which does not allow that, or else because the page's authorisation key expired due to prolonged inactivity. Please refresh the page and try again."));
        }

        $profilelist = FormUtil::getPassedValue('profilelist');
        $startnum    = FormUtil::getPassedValue('startnum');

        if ($startnum < 0) {
            AjaxUtil::error($this->__f("Error! Invalid '%s' passed.", 'startnum'));
        }

        // update the items with the new weights
        $items = array();
        $weight = $startnum + 1;
        foreach ($profilelist as $prop_id) {
            if (empty($prop_id)) {
                continue;
            }

            $items[] = array('prop_id' => $prop_id,
                    'prop_weight' => $weight);
            $weight++;
        }

        // update the db
        $res = DBUtil::updateObjectArray($items, 'user_property', 'prop_id');

        if (!$res) {
            AjaxUtil::error($this->__('Error! Could not save your changes.'));
        }

        return array('result' => true);
    }

    /**
     * Change the status of a profile item.
     *
     * Parameters passed in via FormUtil:
     * ----------------------------------
     * numeric dudid     Id of the property to update.
     * boolean oldstatus True to activate or false to deactivate the item.
     * 
     * @return mixed An AJAX result array containing a result equal to true along with the dud id and new status, or an Ajax error.
     */
    public function changeprofilestatus()
    {
        if (!SecurityUtil::checkPermission('Profile::', '::', ACCESS_ADMIN)) {
            AjaxUtil::error($this->__('Sorry! You do not have authorisation for this module.'));
        }
        
        //if (!SecurityUtil::confirmAuthKey()) {
        //    AjaxUtil::error($this->__("Invalid authorisation key ('authkey'). This is probably either because you pressed the 'Back' button to return to a page which does not allow that, or else because the page's authorisation key expired due to prolonged inactivity. Please refresh the page and try again."));
        //}
        
        $prop_id   = FormUtil::getPassedValue('dudid');
        $oldstatus = (bool)FormUtil::getPassedValue('oldstatus');

        if (!$prop_id) {
            return array('result' => false);
        }

        // update the item status
        $func = ($oldstatus ? 'deactivate' : 'activate');

        $res = ModUtil::apiFunc('Profile', 'admin', $func, array('dudid' => $prop_id));

        if (!$res) {
            AjaxUtil::error($this->__('Error! Could not save your changes.'));
        }

        return array('result' => true,
                'dudid' => $prop_id,
                'newstatus' => !$oldstatus);
    }

    /**
     * Get a profile section for a user.
     *
     * Parameters passed in via FormUtil:
     * ----------------------------------
     * numeric uid  Id of the user to query.
     * string  name Name of the section to retrieve.
     * array   args Optional arguments to the API.
     * 
     * @return mixed An AJAX result array containing a result equal to the rendered output along with the section name and uid, or an Ajax error.
     */
    public function profilesection()
    {
        if (!SecurityUtil::checkPermission('Profile::', '::', ACCESS_READ)) {
            AjaxUtil::error($this->__('Sorry! You do not have authorisation for this module.'));
        }

        $uid  = FormUtil::getPassedValue('uid');
        $name = FormUtil::getPassedValue('name');
        $args = FormUtil::getPassedValue('args');

        if (empty($uid) || !is_numeric($uid) || empty($name)) {
            return array('result' => false);
        }
        if (empty($args) || !is_array($args)) {
            $args = array();
        }

        // update the item status
        $section = ModUtil::apiFunc('Profile', 'section', $name, array_merge($args, array('uid' => $uid)));

        if (!$section) {
            AjaxUtil::error($this->__('Error! Could not load the section.'));
        }

        // build the output
        $this->view->setCaching(false)->add_core_data();

        // check the tmeplate existance
        $template = "sections/profile_section_{$name}.tpl";

        if (!$this->view->template_exists($template)) {
            return array('result' => false);
        }

        // assign and render the output
        $this->view->assign('section', $section);

        return array('result' => $this->view->fetch($template, $uid),
                'name'   => $name,
                'uid'    => $uid);
    }
}