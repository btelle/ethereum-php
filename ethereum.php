<?php

/**
 * Ethereum JSON-RPC interface
 */

require_once(dirname(__FILE__).'/json-rpc.php');

class Ethereum extends JSON_RPC
{
	private function ether_request($method, $params=array())
	{
		try 
		{
			$ret = $this->request($method, $params);
			return $ret->result;
		}
		catch(RPCException $e) 
		{
			throw $e;
		}
	}
	
	function web3_clientVersion()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function web3_sha3($input)
	{
		return $this->ether_request(__FUNCTION__, array($input));
	}
}