<?php

class ParserFactory {

    private $MainParser;
    private $TagParser;
    private $ArgumentParser;

    public function MainParser () {
        return $MainParser;
    }
    public function TagParser () {
        return $TagParser;
    }
    public function ArgumentParser () {
        return $ArgumentParser;
    }
}