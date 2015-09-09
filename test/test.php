<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once('../ethereum.php');


/*
$eth = new Ethereum('127.0.0.1', 8545);

echo ($eth->eth_mining()===TRUE?'True':'False').'<br>';
echo $eth->eth_hashrate().'<br>';
echo $eth->eth_gasPrice().'<br>';
print_r($eth->eth_accounts()); echo '<br>';
echo $eth->eth_blockNumber(TRUE).'<br>';
echo $eth->eth_getBalance('0xc5b9331c79c5784b83ba118acc7c0827ea3b26cf', 'latest', TRUE).'<br>';
echo $eth->eth_getStorageAt('0xc5b9331c79c5784b83ba118acc7c0827ea3b26cf', '0x0', '0x2').'<br>';
echo $eth->eth_getTransactionCount('0xc5b9331c79c5784b83ba118acc7c0827ea3b26cf').'<br>';
var_dump($eth->eth_getBlockTransactionCountByHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238')); echo '<br>';
echo $eth->eth_getBlockTransactionCountByNumber().'<br>';
var_dump($eth->eth_getUncleCountByBlockHash('0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238')); echo '<br>';
echo $eth->eth_getUncleCountByBlockNumber().'<br>';
echo $eth->eth_getCode('0x7011f3edc7fa43c81440f9f43a6458174113b162').'<br>';
try {
echo $eth->eth_sign('0xc5b9331c79c5784b83ba118acc7c0827ea3b26cf', 'hi').'<br>';
} catch(RPCException $e) {
echo "Oh gawd an exception";
}
var_dump($eth->eth_getCompilers()); echo '<br>';
*/

class TestNetFunctions extends TestBase
{
	private $eth;
	
	function __construct()
	{
		echo '<div><strong>Running '.__CLASS__.'</strong></div>';
		parent::__construct();
	}
	
	function CreateEthereum()
	{
		$this->eth = new Ethereum('127.0.0.1', 8545);
		$this->assertIsA($this->eth, 'Ethereum');
	}
	
	function ClientVersion()
	{
		$this->assertEqual($this->eth->web3_clientVersion(), 'Geth/v1.1.2-58766921/linux/go1.4.2');
	}
	
	function Sha3()
	{
		$this->assertEqual($this->eth->web3_sha3('0x68656c6c6f20776f726c64'), '0x47173285a8d7341e5e972fc677286384f802f8ef42a5ec5f03bbfa254cb01fad');
	}
	
	function NetVersion()
	{
		$this->assertEqual($this->eth->net_version(), "1");
	}
	
	function IsListening()
	{
		$this->assertEqual($this->eth->net_listening(), TRUE);
	}
	
	function HasPeers()
	{
		$this->assertMatch($this->eth->net_peerCount(), '/0x[a-f0-9]+/');
	}
	
	function ProtocolVersion()
	{
		$this->assertIsNumeric($this->eth->eth_protocolVersion());
	}
	
	function CoinBase()
	{
		$coinbase = $this->eth->eth_coinbase();
		$this->assertLength($coinbase, 42);
		$this->assertIsHex($coinbase);
	}
}

class TestEthereumFunctions extends TestBase
{
	function __construct()
	{
		echo '<div><strong>Running '.__CLASS__.'</strong></div>';
		parent::__construct();
	}
}

class TestWhisperFunctions extends TestBase
{
	function __construct()
	{
		echo '<div><strong>Running '.__CLASS__.'</strong></div>';
		parent::__construct();
	}
}

class TestBase
{
	private $test_count, $error_count;
	
	function __construct()
	{
		$this->test_count = $this->error_count = 0;
		set_error_handler(array($this, 'errorHandler'), E_USER_ERROR);
		$this->run();
	}
	
	function run()
	{
		foreach(get_class_methods($this) as $m)
		{
			if($m !== 'run' && $m !== '__construct' && $m != 'errorHandler' && !strstr($m, 'assert'))
			{
				$this->$m();
			}
		}
		
		if($this->error_count === 0)
		{
			echo '<div style="background-color: #00FF00;"><strong>Success:</strong> Ran '.$this->test_count.' tests successfully</div>';
		}
		else
		{
			echo '<div style="background-color: #FF0000;"><strong>Ran '.$this->test_count.' tests with '.$this->error_count.' errors</strong></div>';
		}
	}
	
	function assertEqual($a, $b)
	{
		$this->test_count++;
		
		if($a !== $b)
		{
			trigger_error("$a !== $b", E_USER_ERROR);
		}
	}
	
	function assertNotEqual($a, $b)
	{
		$this->test_count++;
		
		if($a === $b)
		{
			trigger_error("$a === $b", E_USER_ERROR);
		}
	}
	
	function assertIsA($a, $type)
	{
		$this->test_count++;
		
		if(!is_a($a, $type))
		{
			trigger_error("$a is not $type", E_USER_ERROR);
		}
	}
	
	function assertIsNumeric($a)
	{
		$this->test_count++;
		
		if(!is_numeric($a))
		{
			trigger_error("$a is not numeric", E_USER_ERROR);
		}
	}
	
	function assertMatch($a, $pattern)
	{
		$this->test_count++;
		
		if(!preg_match($pattern, $a))
		{
			trigger_error("$a does not match pattern '$pattern'", E_USER_ERROR);
		}
	}
	
	function assertLength($a, $len)
	{
		$this->test_count++;
		
		if(strlen($a) !== $len)
		{
			trigger_error("$a is not $len characters long", E_USER_ERROR);
		}
	}
	
	function assertIsHex($a)
	{
		$this->test_count++;
		
		if(!preg_match('/[0-9a-fx]+/', $a))
		{
			trigger_error("$a is not hex", E_USER_ERROR);
		}
	}
	
	function assertError($message)
	{
		$this->test_count++;
		trigger_error($message, E_USER_ERROR);
	}
	
	function errorHandler($errorNumber, $message)
	{
		echo '<div style="background-color: #FF0000;"><strong>Error:</strong> <code>'.$message.'</code></div>';
		$this->error_count++;
	}
}

foreach(array("TestNetFunctions", "TestEthereumFunctions", "TestWhisperFunctions") as $c)
	$t = new $c();