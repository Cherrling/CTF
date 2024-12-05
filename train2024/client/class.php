<?
class Message {
    public $nickname;
    public $time;
    public $contentType;
    public $content;
    public $channel;

    public function __construct($nickname, $time, $contentType, $content, $channel) {
        $this->nickname = $nickname;
        $this->time = $time;
        $this->contentType = $contentType;
        $this->content = $content;
        $this->channel = $channel;
    }

    public function __toString() {
        $con = new $this->contentType($this->content);
        $res = "";
        $res .= "<div class='ui segment'>";
        $res .= "<div class='ui header'>";
        $res .= "<strong>" . htmlspecialchars($this->nickname) . "</strong>";
        $res .= "<div class='sub header'><em>" . htmlspecialchars($this->time) . "</em></div>";
        $res .= "</div>";
        $res .= "<div class='ui message'>" . $con . "</div>";
        $res .= "</div>";
        return $res;
    }
}

class Text {
    public $content;
    public function __construct($content) {
        $this->content = $content;
    }
    public function __toString() {
        return nl2br(htmlspecialchars($this->content));
    }
}

class Picture {
    public $content;
    public function __construct($content) {
        $this->content = $content;
    }
    public function __toString() {
        return '<img src="data:image/png;base64,'.$this->content.'"/>';
    }
}
?>
