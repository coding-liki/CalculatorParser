<?php
declare(strict_types=1);

use CodingLiki\CalculatorParser\Parser\CalculatorParser;

include_once __DIR__."/vendor/CodingLiki/OoAutoloader/Autoloader.php";

$parser = new CalculatorParser(__DIR__.'/grammar/calculator.grt', __DIR__.'/grammar/calculator_new.grr.lrt', __DIR__.'/grammar/calculator_new.grr');

$script = '123+3423/323*92+(12312-232/(23123+53534))';
$result = $parser->parse($script);

echo "$script = $result\n";