<?php
namespace Chatterbot\BasePack\Service;

use PrimPack\Container\Toolbar;
use Jarzon\Container\Localization;

class Container extends \Prim\Container
{
    use Localization, Toolbar;

    /**
     * @return \Prim\Controller
     */
    public function getController(string $obj) : object
    {
        $this->parameters["$obj.class"] = $obj;

        if($this->options['debug']) {
            $toolbar = $this->getToolbarService();

            $toolbar->addElement('Stats', function() {
                $local = $this->getLocalizationService();
                $route = $this->getRouter();
                return ': ' . count($local->messages) . ' messages - ' . $route->getRoutesCount() . ' routes ';
            });
        } else {
            $toolbar = (object) [];
        }

        $localization = $this->getLocalizationService();

        $localization->setLanguage('fr');

        return $this->init($obj, $this->getView(), $this, $this->options, $localization, $toolbar);
    }

    /**
     * @return \Prim\Controller
     */
    public function getErrorController() : object
    {
        $obj = 'errorController';

        $localization = $this->getLocalizationService();

        $localization->setLanguage('fr');

        return $this->init($obj, $this->getView(), $this, $this->options, $localization);
    }

    /**
     * @return \Chatterbot\ChatterbotPack\Service\SentenceHelper
     */
    public function getSentenceHelper() : object
    {
        $obj = 'sentenceHelper';

        $this->setDefaultParameter($obj, '\Chatterbot\ChatterbotPack\Service\SentenceHelper');

        return $this->init($obj);
    }
}