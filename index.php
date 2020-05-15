<?php
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Nano\Factory\AppFactory;
use App\Table;

require_once __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$class = Table::class;

$app->addProcess(function() use ($class){
    $this->get(StdoutLoggerInterface::class)->info('start...' . date('Y-m-d H:i:s'));
    $table = $this->get($class);
    $this->get(StdoutLoggerInterface::class)->info($table->uri());
    sleep(10);
    $this->get(StdoutLoggerInterface::class)->info('done...' . date('Y-m-d H:i:s'));
});

$app->run();
