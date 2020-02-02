<?php

namespace Logger;

class Logger {
    private $verbosity;
    const VERBOSITY_1 = 1;
    const VERBOSITY_2 = 2;
    const VERBOSITY_3 = 3;
    const VERBOSITY_4 = 4;

    public function __construct($verbosity = Logger::VERBOSITY_1) {
        $this->verbosity = $verbosity;
    }

    public function setVerbosity($verbosity) {
        $this->verbosity = $verbosity;
        return $this;
    }

    public function log($message, $verbosity = Logger::VERBOSITY_1) {
        if($this->verbosity >= $verbosity) {
            // no time to do anything more elaborate
            echo sprintf("%s\n", $message);
        }
    }

    public function error($message, $verbosity = Logger::VERBOSITY_1) {
        if($this->verbosity >= $verbosity) {
            echo sprintf("%s\n", $message);
        }
    }
}