<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


use Tracy\Debugger;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    protected static $template = '404.twig';
    protected static $cacheEnabled;
    protected static $cacheDir;
    protected static $headersSent = false;

    /**
     * @param array $context
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public static function render($context = []): void
    {
        ob_clean();
        self::sendHeaders();
        $loader = new FilesystemLoader(App::getTemplateDir());
        $options = [];
        if (self::isCacheEnabled()) {
            $options['cache'] = self::getCacheDir();
        }
        $twig = new Environment($loader, $options);

        echo $twig->render(self::$template, $context);
        ob_end_flush();
    }

    /**
     * @param array $context
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function renderAndStop($context = []): void
    {
        self::render($context);
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
        if (self::$cacheEnabled === null) {
            if (Debugger::detectDebugMode()) {
                self::$cacheEnabled = false;
            } else {
                self::$cacheEnabled = true;
            }
        }
        return self::$cacheEnabled;
    }

    /**
     * @param bool $cacheEnabled
     */
    public static function setCacheEnabled(bool $cacheEnabled): void
    {
        self::$cacheEnabled = $cacheEnabled;
    }

    private static function sendHeaders()
    {
        if (self::$headersSent === false) {
            ResponseHeaders::sendAllHeaders();
        }
        self::$headersSent = true;
    }

    /**
     * Minify the html for the outputbuffer
     *
     * @param $buffer
     * @return mixed
     */
    public static function sanitizeOutput($buffer)
    {
        if (!isset($_GET['unsanitized'])) {
            $search = array(
                '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                '/(\s)+/s',         // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // Remove HTML comments
            );
            $replace = array(
                '>',
                '<',
                '\\1',
                ''
            );
            $buffer = preg_replace($search, $replace, $buffer);
            return $buffer;
        }
        return $buffer;
    }
}