<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


use Twig_Environment;
use Twig_Loader_Filesystem;

class Renderer
{
    protected static $template = '404.twig';
    protected static $cacheEnabled = true;
    protected static $cacheDir;

    /**
     * @param array $context
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function render($context = []): void
    {
        ob_clean();
        $loader = new Twig_Loader_Filesystem(App::getTemplateDir());
        $options = [];
        if (self::isCacheEnabled()) {
            $options['cache'] = self::getCacheDir();
        }
        $twig = new Twig_Environment($loader, $options);

        echo $twig->render(self::$template, $context);
        ob_end_flush();
    }

    public static function renderAndStop(): void
    {
        self::render();
        exit;
    }

    /**
     * @param string $template
     */
    public static function setTemplate(string $template): void
    {
        self::$template = $template;
    }

    /**
     * @return string
     */
    public static function getCacheDir()
    {
        if (self::$cacheDir === null) {
            self::$cacheDir = App::getRootDir() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        }
        return self::$cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public static function setCacheDir($cacheDir): void
    {
        self::$cacheDir = $cacheDir;
    }

    /**
     * @return bool
     */
    public static function isCacheEnabled(): bool
    {
        return self::$cacheEnabled;
    }

    /**
     * @param bool $cacheEnabled
     */
    public static function setCacheEnabled(bool $cacheEnabled): void
    {
        self::$cacheEnabled = $cacheEnabled;
    }
}