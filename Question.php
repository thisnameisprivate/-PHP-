<?php



abstract class Question {
    protected $prompt;
    protected $marker;
    public function __construct ($prompt, Marker $marker) {
        $this->prompt = $prompt;
        $this->marker = $marker;
    }
    public function mark ($response) {
        return $this->marker->mark($response);
    }
}
class TextQuestion extends Question {
    // 处理文本问题特有操作
}
class AVQuestion extends Question {
    // 处理语音问题特有操作
}
abstract class Marker {
    protected $test;
    public function __construct ($test) {
        $this->test = $test;
    }
    abstract function mark ($response);
}
class MarkLogicMarker extends Marker {
    private $engine;
    public function __construct ($test) {
        parent::__construct ($test);
    }
    public function mark ($response) {
        // return $this->engine->evaluate($response);
        return true;
    }
}
class MatchMarker extends Marker {
    public function mark ($response) {
        return ($this->test == $response);
    }
}
class RegexpMarker extends Marker {
    public function mark ($response) {
        return (preg_match($this->test, $response));
    }
}

$markers = array(new RegexpMarker("/f.ve/"), new MatchMarker("five"), new MarkLogicMarker("$input equals 'five'"));