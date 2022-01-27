<?php
require_once 'inc/config.php';

class Page
{
    private string $title;
    private string $content;
    private string $navigation;
    private string $script;

    public function __construct(string $title, string $navigation, string $content, string $script='')
    {
        $this->title = $title;
        $this->navigation = $navigation;
        $this->content = $content;
        $this->script = $script;
    }

    public function write_html()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title><?php echo FORUM_NAME . ' - ' . "$this->title"; ?></title>
            <meta charset="utf-8">
            <link rel="stylesheet" href="style.css">
        </head>
    <body>
    <div id="content">
        <header>
            <h1><?php echo FORUM_NAME . ' - ' . "$this->title"; ?></h1>
            <nav>
                <?php echo $this->navigation; ?>
            </nav>
        </header>
        <main>
        <?php
        echo $this->content;
        ?>
        </main>
    </div>
    </body>
        <?php
        if (!empty($this->script)) {
            echo '<script type="text/javascript" src="' . $this->script . '"></script></script>';
        }
        ?>
    </html>
        <?php
    }
}