<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once('../ethereum.php');

$eth = new Ethereum('127.0.0.1', 8545);

echo $eth->web3_clientVersion().'<br>';
echo $eth->web3_sha3('0x68656c6c6f20776f726c64').'<br>';
echo $eth->net_version().'<br>';
echo ($eth->net_listening()===TRUE?'True':'False').'<br>';
echo $eth->net_peerCount().'<br>';
echo $eth->eth_protocolVersion().'<br>';
var_dump($eth->eth_coinbase()); echo '<br>';
echo ($eth->eth_mining()===TRUE?'True':'False').'<br>';
echo $eth->eth_hashrate().'<br>';
echo $eth->eth_gasPrice().'<br>';
print_r($eth->eth_accounts()); echo '<br>';
echo $eth->eth_blockNumber().'<br>';
echo $eth->eth_getBalance('0x407d73d8a49eeb85d32cf465507dd71d507100c1').'<br>';
echo $eth->eth_getStorageAt('0x407d73d8a49eeb85d32cf465507dd71d507100c1', '0x0', '0x2').'<br>';
echo $eth->eth_getTransactionCount('0x407d73d8a49eeb85d32cf465507dd71d507100c1').'<br>';
var_dump($eth->eth_getBlockTransactionCountByHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238')); echo '<br>';