<?php

namespace Cyclonecode\Plugin\Logger;

interface LoggerInterface
{
    const LOG = 1;
    const DEBUG = 2;
    const NOTICE = 3;
    const WARNING = 4;
    const CRITICAL = 5;

    public function log($message);
}
