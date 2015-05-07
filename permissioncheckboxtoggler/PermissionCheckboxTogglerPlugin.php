<?php
namespace Craft;

/**
 * Class PermissionCheckboxToggler
 *
 * @package Craft
 */
class PermissionCheckboxTogglerPlugin extends BasePlugin
{
    protected $cp;

    public function init() {
        parent::init();

        if (craft()->userSession->isLoggedIn() && craft()->request->isCpRequest() && ! craft()->request->isAjaxRequest() ) {

            $jsT = json_encode([ craft()->request->getSegment(1) ]);
            craft()->templates->includeJs($jsT);

            $isGroupsPage = craft()->request->getSegment(3) == 'groups';
            $isUsersPage = craft()->request->getSegment(1) === 'myaccount' || (craft()->request->getSegment(1) === 'users' && craft()->request->getSegment(2));
            
            if ($isGroupsPage OR $isUsersPage)
            {
                $pageType = $isUsersPage ? 'users' : 'groups';

                craft()->templates->includeJsResource('permissioncheckboxtoggler/permissioncheckboxtoggler.js');
                craft()->templates->includeCssResource('permissioncheckboxtoggler/permissioncheckboxtoggler.css');

                $js = "$(function() { CraftPermissionCheckboxToggler.init('".$pageType."');  });";
                craft()->templates->includeJs($js);
            }
        }
    }

	/**
	 * @return mixed
	 */
	function getName()
	{
		return 'Permission Checkbox Toggler';
	}

	/**
	 * @return string
	 */
	function getVersion()
	{
		return '0.1';
	}

	/**
	 * @return string
	 */
	function getDeveloper()
	{
		return 'Fred Carlsen';
	}

	/**
	 * @return string
	 */
	function getDeveloperUrl()
	{
		return 'http://sjelfull.no';
	}
}
