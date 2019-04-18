<?php
declare(strict_types=1);

namespace Ixocreate\Package\Admin;

use Ixocreate\Application\Console\ConsoleConfigurator;

/** @var ConsoleConfigurator $console */

$console->addDirectory(__DIR__ . '/../src/Console', true);
