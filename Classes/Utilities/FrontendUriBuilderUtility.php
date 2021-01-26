<?php
namespace BoergenerWebdesign\BwFeAdmin\Utilities;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendUriBuilder for creating a frontend link in the backend
 * @package PDVSysteme\Pdvsupportbase\Util
 * @see https://stackoverflow.com/questions/33751570/typo3-with-extbase-uribuilder-or-something-similar-in-backend-hook
 * @license Public Domain
 */
class FrontendUriBuilderUtility
{
    private $pageId = 1;

    private $extensionName = null;

    private $pluginName = null;

    private $actionName = null;

    private $controllerName = null;

    private $arguments = null;

    private $host = null;

    /**
     * FrontendUriBuilder constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param int $pageId the target pageId
     * @return $this FrontendUriBuilder
     */
    public function setTargetPageUid($pageId = 1){
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param string $extensionName
     * @return $this FrontendUriBuilder
     */
    public function setExtensionName($extensionName){
        $this->extensionName = strtolower($extensionName);

        return $this;
    }

    /**
     * @param string $pluginName
     * @return $this FrontendUriBuilder
     */
    public function setPlugin($pluginName){
        $this->pluginName = strtolower($pluginName);

        return $this;
    }

    /**
     * @param string $actionName
     * @return $this FrontendUriBuilder
     */
    public function setAction($actionName){
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * @param string $controllerName
     * @return $this FrontendUriBuilder
     */
    public function setController($controllerName){
        $this->controllerName = $controllerName;

        return $this;
    }

    /**
     * @param array $arguments like array('nameOfTheClass' => $instance)
     * @return $this FrontendUriBuilder
     */
    public function setArguments($arguments){
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param string $host
     * @return $this FrontendUriBuilder
     */
    public function setHost($host){
        $this->host = $host;

        return $this;
    }

    /**
     * Build the URL
     * @return string the url
     * @throws \Exception
     */
    public function build(){

        //set base
        $url = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

        //set pageId
        $url = $url . 'index.php?id=' . $this->pageId;

        //set action
        if(!is_null($this->actionName)){
            $this->checkExtensionName();
            $this->checkPluginName();

            $url = $url . '&tx_' . $this->extensionName . '_' . $this->pluginName . '[action]=' . $this->actionName;
        }

        //set controller
        if(!is_null($this->controllerName)){
            $this->checkExtensionName();
            $this->checkPluginName();

            $url = $url . '&tx_' . $this->extensionName . '_' . $this->pluginName . '[controller]=' . ucfirst($this->controllerName);
        }

        //set arguments
        if(!is_null($this->arguments)) {
            $this->checkExtensionName();
            $this->checkPluginName();

            /**
             * @var $argument AbstractEntity
             */
            foreach ($this->arguments as $key => $argument) {
                $value  = $argument;
                if (is_object($value))
                    $value = $value->getUid();

                $url = $url . '&tx_' . $this->extensionName . '_' . $this->pluginName . '[' . $key . ']=' . $value;
            }
        }

        return $url;
    }

    private function checkExtensionName(){
        if(is_null($this->extensionName)){
            throw new \Exception("Extension name for FrontendUriBuilder not set!");
        }
    }

    private function checkPluginName(){
        if(is_null($this->pluginName)){
            throw new \Exception("Plugin name for FrontendUriBuilder not set!");
        }
    }
}
