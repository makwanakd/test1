<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icecube\Extrafields\Model\Backend;

/**
 * Class \Magento\Backend\Model\UrlInterface
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Url extends \Magento\Backend\Model\Url
{
    public function getAdminUrlCustom($routePath = null, $customPath, $routeParams = null)
    {
    	//echo $objectManager->create('Magento\Backend\Model\Url')->getAdminUrlCustom('*',array('id'=>422));
        if (filter_var($routePath, FILTER_VALIDATE_URL)) {
            return $routePath;
        }

        $cacheSecretKey = false;
        if (is_array($routeParams) && isset($routeParams['_cache_secret_key'])) {
            unset($routeParams['_cache_secret_key']);
            $cacheSecretKey = true;
        }
        $result = parent::getUrl($routePath, $routeParams);
        if (!$this->useSecretKey()) {
            return $result;
        }
        $urlpara = explode('/',$customPath);
        $this->_setRoutePath($routePath);
        $routeName = $urlpara[0];
        $controllerName = $urlpara[1];
        $actionName = $urlpara[2];
        if ($cacheSecretKey) {
            $secret = [self::SECRET_KEY_PARAM_NAME => "\${$routeName}/{$controllerName}/{$actionName}\$"];
        } else {
            $secret = [
                self::SECRET_KEY_PARAM_NAME => $this->getSecretKey($routeName, $controllerName, $actionName),
            ];
        }
        if (is_array($routeParams)) {
            $routeParams = array_merge($secret, $routeParams);
        } else {
            $routeParams = $secret;
        }
        if (is_array($this->_getRouteParams())) {
            $routeParams = array_merge($this->_getRouteParams(), $routeParams);
        }
        return parent::getUrl("{$routeName}/{$controllerName}/{$actionName}", $routeParams);
    }
}
