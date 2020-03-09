<?php

namespace WP_CAMOO\SMS\Controller;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use WP_CAMOO\SMS\Exception\AppException;
use CAMOO_SMS\Option;

/**
 * Class AppController
 * @author CamooSarl
 */
abstract class AppController
{
    abstract public static function addAssets();

	public function __construct()
	{
	}

    public function initialize()
    {
        $controller = self::getController();
        if ($controller !== 'AppController') {
            $this->loadModel($controller);
        }
    }

    protected function loadModel($sModel)
    {
        $model = '\\WP_CAMOO\\\SMS\Model\\' . $sModel. 'Model';
        if (!class_exists($model)) {
            throw new AppException(sprintf('Model %s not found', $sModel));
        }

        $this->{$sModel} = new $model();
    }

    /**
     * @return string
     */
    protected static function getAssetUrl($assertType)
    {
        return sprintf(WP_CAMOO_SMS_URL .'assets/%s/' . self::getController(). DIRECTORY_SEPARATOR, $assertType);
    }

    public function render()
    {
    }

    /**
     * @return string
     */
    private static function getController()
    {
        $controller_class = get_called_class();
        $asController = explode('\\', $controller_class);
        $controller = array_pop($asController);
        return substr($controller, 0, -10); // Controller length 10
    }

    /**
     * @param string $name
     * @return void
     */
    protected static function template($name)
    {
        $file = sprintf('%sincludes/src/Template/%s/%s.php', WP_CAMOO_SMS_DIR, self::getController(), $name);
        if (!file_exists($file)) {
            throw new AppException(sprintf('Template %s not found', $file));
        }
        include_once $file;
    }
}
