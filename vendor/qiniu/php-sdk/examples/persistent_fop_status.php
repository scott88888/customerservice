<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Processing\PersistentFop;

$pfop = new Qiniu\Processing\PersistentFop(null, null);

// 触发持久化处理后返回的 Id
$persistentId = 'z1.5b8a48e5856db843bc24cfc3';

// 通過persistentId查詢该 触发持久化处理的狀態
list($ret, $err) = $pfop->status($persistentId);

if ($err) {
    print_r($err);
} else {
    print_r($ret);
}
