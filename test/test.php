<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once('../ethereum.php');

$eth = new Ethereum('127.0.0.1', 8545);

echo $eth->web3_clientVersion().'<br>';
echo $eth->web3_sha3('0x68656c6c6f20776f726c64').'<br>';