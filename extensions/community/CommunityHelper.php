<?php

/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2010, {@link http://aksw.org AKSW}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * Helper class for the Community component.
 *
 * - register the tab for all navigations except the instances list
 *   (this should be undone if the community tab can be created from a Query2 too)
 *
 * @category OntoWiki
 * @package Extensions
 * @subpackage Community
 * @copyright Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class CommunityHelper extends OntoWiki_Component_Helper
{
    public function init()
    {
        // get current request info
        $request  = Zend_Controller_Front::getInstance()->getRequest();

        if(($request->getControllerName() == 'resource' 
                && $request->getActionName() == 'instances')
          || ($request->getControllerName() == 'resource'
                  && $request->getActionName() == 'instances'
                  && $request->getParam('mode') == 'multi')){
            OntoWiki_Navigation::register('community', array(
                'controller' => 'community',     // history controller
                'action'     => 'list',       // list action
                'name'       => 'Community',
                'mode'       => 'multi',
                'priority'   => 50));
        } else {
            OntoWiki_Navigation::register('community', array(
                'controller' => 'community',     // history controller
                'action'     => 'list',       // list action
                'name'       => 'Community',
                'mode'       => 'single',
                'priority'   => 50));
        }
        
    }
}

