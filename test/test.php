<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
require_once('../ethereum.php');

/**
 * Test suite for the ethereum-php.
 * Make sure you have created an account before running or you will get TONS OF ERRORS
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
	private $account;
	
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
	
	function IsMining()
	{
		$mining = $this->eth->eth_mining();
		$this->assertIsBoolean($mining);
	}
	
	function HashRate()
	{
		$this->assertIsHex($this->eth->eth_hashrate());
	}
	
	function GasPrice()
	{
		$price = $this->eth->eth_gasPrice();
		$this->assertIsHex($price);
		
		// I assume gas will never be free?
		$this->assertNotEqual($price, '0x0');	
	}
	
	function Accounts()
	{
		$accounts = $this->eth->eth_accounts();
		
		$this->assertIsArray($accounts);
		$this->assertNotEqual(count($accounts), 0);
		$this->assertIsHex($accounts[0]);
		$this->assertLength($accounts[0], 42);
		
		// Save this account for later
		$this->account = $accounts[0];
	}
	
	function BlockNumber()
	{
		$blkNum = $this->eth->eth_blockNumber(TRUE);
		$blkHex = $this->eth->eth_blockNumber();
		
		$this->assertIsNumeric($blkNum);
		$this->assertNotEqual($blkNum, 0);
		$this->assertIsHex($blkHex);
		$this->assertEqual($blkHex, '0x'.dechex($blkNum));
	}
	
	function AccountBalance()
	{
		$balHex = $this->eth->eth_getBalance($this->account, 'latest');
		$balNum = $this->eth->eth_getBalance($this->account, 'latest', TRUE);
		
		$this->assertIsNumeric($balNum);
		$this->assertIsHex($balHex);
		$this->assertEqual($balHex, '0x'.dechex($balNum));
	}
	
	function AccountStorage()
	{
		$stor = $this->eth->eth_getStorageAt($this->account, '0x0', '0x1');
		
		$this->assertIsHex($stor);
	}
	
	function AddressTransactionCount()
	{
		$countHex = $this->eth->eth_getTransactionCount($this->account, 'latest');
		$countNum = $this->eth->eth_getTransactionCount($this->account, 'latest', TRUE);
		
		$this->assertIsNumeric($countNum);
		$this->assertIsHex($countHex);
		$this->assertEqual($countHex, '0x'.dechex($countNum));
	}
	
	function GetBlocks()
	{
		$block = $this->eth->eth_getBlockByNumber('latest');
		$blockByHash = $this->eth->eth_getBlockByHash($block->hash);
		
		$this->assertIsA($block, 'stdClass');
		$this->assertIsA($blockByHash, 'stdClass');
		$this->assertEqual($block->hash, $blockByHash->hash);
		
		$txCountByHash = $this->eth->eth_getBlockTransactionCountByHash($block->hash);
		$txCountByNum = $this->eth->eth_getBlockTransactionCountByNumber($block->number);
		
		$this->assertIsHex($txCountByHash);
		$this->assertIsHex($txCountByNum);
		$this->assertEqual($txCountByHash, $txCountByNum);
		$this->assertEqual($txCountByHash, '0x'.dechex(count($block->transactions)));
		
		$uncleCountByHash = $this->eth->eth_getUncleCountByBlockHash($block->hash);
		$uncleCountByNum = $this->eth->eth_getUncleCountByBlockNumber($block->number);
		
		$this->assertIsHex($uncleCountByHash);
		$this->assertIsHex($uncleCountByNum);
		$this->assertEqual($uncleCountByHash, $uncleCountByNum);
		$this->assertEqual($uncleCountByHash, '0x'.dechex(count($block->uncles)));
	}
	
	function GetTransactions()
	{
		// Get a recent block with some transactions
		$blockNum = $this->eth->eth_blockNumber(TRUE);
		do
		{
			$block = $this->eth->eth_getBlockByNumber('0x'.dechex(--$blockNum));
		}
		while(count($block->transactions) == 0);
		
		$tx = $block->transactions[0];
		
		$this->assertIsA($tx, 'stdClass');
		$this->assertIsHex($tx->hash);
		
		$txByBlock = $this->eth->eth_getTransactionByBlockHashAndIndex($block->hash, '0x0');
		
		$this->assertIsA($txByBlock, 'stdClass');
		$this->assertIsHex($txByBlock->hash);
		$this->assertEqual($tx->hash, $txByBlock->hash);
		
		$txByHash = $this->eth->eth_getTransactionByHash($tx->hash);
		
		$this->assertIsA($txByHash, 'stdClass');
		$this->assertIsHex($txByHash->hash);
		$this->assertEqual($tx->hash, $txByHash->hash);
		
		$receipt = $this->eth->eth_getTransactionReceipt($tx->hash);
		$this->assertIsA($receipt, 'stdClass');
		$this->assertEqual($receipt->blockHash, $block->hash);
		$this->assertEqual($receipt->blockNumber, '0x'.dechex($blockNum));
	}
	
	function DoTransaction()
	{
		// TODO: Test sending transactions. This requires mining, working on it.
	}
	
	function SendMessage()
	{
		// TODO: Message tests
	}
	
	function Compilers()
	{
		// TODO: Compiler tests
	}
	
	function Filters()
	{
		// TODO: Filter tests
	}
	
	function DB()
	{
		// TODO: DB Tests
	}
}

class TestWhisperFunctions extends TestBase
{
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
	
	function Version()
	{
		$this->assertIsNumeric($this->eth->shh_version());
	}
	
	function Post()
	{
		// TODO: Whisper post tests
	}
	
	function Filter()
	{
		// TODO: Whisper filter tests
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
				try
				{
					$this->$m();
				}
				catch(Exception $e)
				{
					trigger_error('Uncaught exception: '.$e->getMessage(), E_USER_ERROR);
				}
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
			trigger_error("Object is not $type", E_USER_ERROR);
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
	
	function assertIsBoolean($a)
	{
		$this->test_count++;
		
		if(!is_bool($a))
		{
			trigger_error("Object is not boolean", E_USER_ERROR);
		}
	}
	
	function assertIsArray($a)
	{
		$this->test_count++;
		
		if(!is_array($a))
		{
			trigger_error("Object is not an array", E_USER_ERROR);
		}
	}
	
	function assertError($message)
	{
		$this->test_count++;
		trigger_error($message, E_USER_ERROR);
	}
	
	function errorHandler($errorNumber, $message, $file, $line, $context)
	{
		$additional = 'on line '.$line;
		
		$trace = array_reverse(debug_backtrace());
		array_pop($trace);
		if(isset($trace[3]) && isset($trace[4]))
		{
			$class = $trace[3]['class'];
			$function = $trace[3]['function'];
			$line = $trace[4]['line'];
			$additional = 'in <code>'.$class.'.'.$function.'()</code> on line '.$line;
		}
		
		echo '<div style="background-color: #FF0000;"><strong>Error:</strong> <code>'.$message.'</code> '.$additional.'</div>';
		$this->error_count++;
	}
}

foreach(array("TestNetFunctions", "TestEthereumFunctions", "TestWhisperFunctions") as $c)
	$t = new $c();