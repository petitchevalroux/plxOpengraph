<?php

class plxOpengraph extends plxPlugin
{

    /**
     * Constructeur de la classe
     * @param string $default_lang langue par defaut.
     * */
    public function __construct($default_lang)
    {
        parent::__construct($default_lang);
        $this->addHook('ThemeEndHead', 'ThemeEndHead');
    }

    /**
     * Retourne le contenu du titre opengraph
     * @param plxShow $plxShow
     * @return string
     */
    private function getTitle($plxShow)
    {
        ob_start();
        $plxShow->pageTitle();
        return ob_get_clean();
    }

    /**
     * Retourne le contenu de l'url opengraph
     * @param plxShow $plxShow
     * @return string
     */
    private function getImage($plxShow)
    {
        $image = '';
        ob_start();
        $plxShow->artContent();
        $artContent = ob_get_clean();
        if (preg_match('~<img[^>]*?src="(.*?)"[^>]+>~', $artContent, $match)) {
            $image = trim($match[1]);
            if (strpos($image, 'http') !== 0) {
                $image = 'http://' . $_SERVER['SERVER_NAME'] . '/' . trim($match[1]);
            }
        }
        return $image;
    }

    /**
     * Retourne le contenu de la description opengraph
     * @param plxShow $plxShow
     * @return string
     */
    private function getDescription($plxShow)
    {
        $description = trim($plxShow->plxMotor->plxRecord_arts->f('meta_description'));
        if (empty($description) && !empty($plxShow->plxMotor->aConf['meta_description'])) {
            $description = trim($plxShow->plxMotor->aConf['meta_description']);
        }
        return $description;
    }

    /**
     * Retourne le contenu de l'url opengraph
     * @param plxShow $plxShow
     * @return string
     */
    public function getUrl($plxShow)
    {
        ob_start();
        $plxShow->artUrl();
        return ob_get_clean();
    }

    /**
     * Retourne le contenu de l'url opengraph
     * @param plxShow $plxShow
     * @return string
     */
    public function getAuthor($plxShow)
    {
        return $plxShow->artAuthor(false);
    }

    /**
     * Hook exécuté à la fin de la balise head
     */
    public function ThemeEndHead()
    {
        $plxShow = plxShow::getInstance();
        if ($plxShow->plxMotor->mode === 'article') {
            $og = array(
                'type' => 'article',
                'url' => $this->getUrl($plxShow),
                'title' => $this->getTitle($plxShow),
                'description' => $this->getDescription($plxShow),
                'image' => $this->getImage($plxShow),
                'author:username' => $this->getAuthor($plxShow)
            );
            foreach ($og as $property => $content) {
                if (!empty($content)) {
                    echo '<meta property="og:' . plxUtils::strCheck($property) . '" content="' . plxUtils::strCheck($content) . '"/>';
                }
            }
        }
    }

}
