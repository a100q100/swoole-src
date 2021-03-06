--TEST--
swoole_coroutine: while tick 1000  with opcache enable
--SKIPIF--
<?php 
require __DIR__ . '/../../include/skipif.inc';
if (!SWOOLE_CORO_SCHEDULE) {
    skip("coroutine schdule tick was not compliled");
}
 if (!ini_get("opcache.enable_cli")) 
 {
    skip("not loaded opcache");
 }
?>
--FILE--
<?php
declare(ticks=1000);

$max_msec = 10;
Swoole\Coroutine::set([
    'max_exec_msec' => $max_msec,
]);

$start = microtime(1);
echo "start\n";
$flag = 1;
go(function () use (&$flag, $max_msec){
    echo "coro 1 start to loop\n";
    $i = 0;
    while($flag) {
        $i ++;
    }
    echo "coro 1 can exit\n";
});
    
$end = microtime(1);
$msec = ($end-$start) * 1000;
assert(abs($msec-$max_msec) <= 2);
go(function () use (&$flag){
    echo "coro 2 set flag = false\n";
    $flag = false;
});
echo "end\n";
?>
--EXPECTF--
start
coro 1 start to loop
coro 2 set flag = false
end
coro 1 can exit
