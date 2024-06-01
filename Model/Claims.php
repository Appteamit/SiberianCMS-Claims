<?php

/**
 * Class Claims_Model_Claims
 * @package Claims\Model
 */
class Claims_Model_Claims extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;


    /**
     * @var string
     */
    protected $_db_table = Claims_Model_Db_Table_Claims::class;

    /**
     * @param $valueId
     * @return array|bool
     */
    public function getInappStates($valueId)
    {

        $inAppStates = [
            [
                "state" => "claims-home",
                "offline" => false,
                "params" => []
            ],
        ];

        return $inAppStates;
    }

    /**
     * @return null
     */
    public static function getCurrentValueId()
    {
        $app = self::getApplication();
        if ($app) {
            $options = $app->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() === "claims") {
                    return $option->getId();
                }
            }
        }
        return null;
    }

    /**
     * @return null
     */
    public static function getCurrent()
    {
        $app = self::getApplication();
        if ($app) {
            $options = $app->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() === "claims") {
                    return $option;
                }
            }
        }
        return null;
    }

    /**
     * @param $editorTree
     * @return mixed
     * @throws Exception
     * @throws \Zend_Exception
     */
    public static function dashboardNav($editorTree)
    {
        $app = (new self())->getApplication();
        $options = $app->getOptions();

        $useClaims = false;
        foreach ($options as $option) {
            if ($option->getCode() === 'claims') {
                $useClaims = true;
                break;
            }
        }

        if (!$useClaims) {
            return $editorTree;
        }

        $currentUrl = str_replace((new self())->getBaseUrl(), "", (new self())->getCurrentUrl());
        $editorTree["modules"]["childs"]["feature_claims"] = [
            "hasChilds" => false,
            "isVisible" => self::_canAccess("feature_claims"),
            "label" => p__("claims", "Claims dashboard"),
            "icon" => "fa fa-home",
            "url" => self::_getUrl("claims/application/claims"),
            "is_current" => ("/claims/application/claims" === $currentUrl),
        ];

        // Ensure the module is made visible is user has access!
        $editorTree["modules"]["isVisible"] = $editorTree["modules"]["isVisible"] || self::_canAccess("feature_claims");

        return $editorTree;
    }

    /**
     * @param $acl
     * @return bool
     */
    protected static function _canAccess($acl)
    {
        $aclList = \Admin_Controller_Default::_sGetAcl();
        if ($aclList) {
            return $aclList->isAllowed($acl);
        }

        return true;
    }

    /**
     * @param string $url
     * @param array $params
     * @param null $locale
     * @return array|mixed|string
     */
    public static function _getUrl($url = "", array $params = [], $locale = null)
    {
        return __url($url);
    }

}