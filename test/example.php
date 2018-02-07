<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once(dirname(__DIR__) . '/ethereum.php');

$eth = new Ethereum('127.0.0.1', 8545);

function xlog($x, $prefix = '')
{
    $s = var_export($x, true);
    printf("%s%s\n", $prefix ? $prefix . ': ' : '', $s);
}

xlog($eth->web3_clientVersion(), 'version');
xlog($eth->web3_sha3('0x68656c6c6f20776f726c64'), 'sha3');

$coinbase = $eth->eth_coinbase();
xlog($coinbase, 'coinbase');

$accounts = $eth->eth_accounts();
xlog($accounts, 'accounts');

$balNum = $eth->eth_getBalance($accounts[0], 'latest');
xlog($balNum, 'balance');
		
$balHex = $eth->eth_getBalance($accounts[0], 'latest', Ethereum::NO_DECODE_HEX);
xlog($balHex, 'balance in hex');

$blkNum = $eth->eth_blockNumber();
xlog($blkNum, 'block number');

$blkHex = $eth->eth_blockNumber(Ethereum::NO_DECODE_HEX);
xlog($blkHex, 'block number in hex');

$block = $eth->eth_getBlockByNumber(6); #can be 'latest'
xlog($block, 'block');
xlog(Ethereum::decode_hex($block->number), 'block number');
xlog(Ethereum::decode_hex($block->gasLimit), 'gas limit');

$blockByHash = $eth->eth_getBlockByHash($block->hash);
$txCountByHash = $eth->eth_getBlockTransactionCountByHash($block->hash);
xlog($txCountByHash, 'txCountByHash');

$txCountByNum = $eth->eth_getBlockTransactionCountByNumber($block->number);
xlog($txCountByNum, 'txCountByNum');

$arr_tx = $block->transactions;
if (count($arr_tx) > 0) {
    $tx = $arr_tx[0];
    $txByHash = $eth->eth_getTransactionByHash($tx->hash);
    xlog($txByHash, 'tx');
} else {
    xlog('no transactions');
}

