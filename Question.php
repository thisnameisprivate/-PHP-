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