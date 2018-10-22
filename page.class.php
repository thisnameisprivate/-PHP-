<?php
class Page {
    public $content;
    public $title = "TLA Consulting Pty Ltd";
    public $keywords = "TLA Consulting, Three Letter Abbrevitaion,
    some of my best friends are search engines";
    public $buttons = array("Home" => "Home.php",
        "Content" => "contact.php",
        "Services" => "services.php",
        "Site Map" => "map.php");
    public function __set ($name, $value) {
        $this->$name = $value;
    }
    public function Display () {
        echo "<html>\n<head> \n";
        $this->DisplayTitle();
        $this->DisplayKeywords();
        $this->DisplayStyles();
        echo "</head>\n<body> \n";
        $this->DisplayHeader();
        $this->DisplayMenu($this->buttons);
        echo $this->content;
        $this->DisplayFooter();
        echo "</body>\n</html>";
    }
    public function DisplayTitle () {
        echo "<title>" . $this->title . "</title>";
    }
    public function DisplayKeywords () {
        echo "<meta name='keywords' content='" . $this->keywords . "'";
    }
    public function DisplayStyles () {
        ?>
<link  href="styles.css" type="text/css" rel="stylesheet">
<?php
    }
    public function DisplayHeader () {
        ?>
<!-- page header -->
<header>
    <img src="logo.gif" alt="TLA logo" height="70" width="70">
    <h1>TLA Consuliting</h1>
</header>
<?php
    }
    public function DisplayMenu () {
        echo "<!-- menu --><nav>";
        while (list ($name, $url) = @each($buttons)) {
            $this->DisplayButton($name, $url, !$this->IsURLCurrentPage($url));
        }
        echo "</nav>\n";
    }
    public function IsURLCurrentPage ($url) {
        if (strpos($_SERVER['PHP_SELF'], $url)) {
            return false;
        } else {
            return true;
        }
    }
    public function __call ($method, $p) {
        if ($method == 'display') {
            if (is_object($p[0])) {
                $this->displayObject($p[0]);
            } else if (is_array($p[0])) {
                $this->displayArray($p[0]);
            } else {
                $this->displayScalar($p[0]);
            }
        }
    }
}