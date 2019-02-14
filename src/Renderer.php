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

    public static function render(): void
    {
        ob_clean();
        $loader = new Twig_Loader_Filesystem(App::getTemplateDir());
        $twig = new Twig_Environment($loader/*, [
            'cache' => '/path/to/compilation_cache',
        ]*/);

        echo $twig->render(self::$template, ['name' => 'Fabien']);
        ob_end_flush();
    }

    public static function renderAndStop() : void
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


}