<?php

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/tUtils.php';

$rssBridge = new RssBridge();

$rssBridge->main($argv ?? []);
