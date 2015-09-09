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
	
	function net_version()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function net_listening()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function net_peerCount()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_protocolVersion()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_coinbase()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_mining()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_hashrate()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_gasPrice()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_accounts()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_blockNumber()
	{
		return $this->ether_request(__FUNCTION__);
	}
	
	function eth_getBalance($address, $block='latest')
	{
		return $this->ether_request(__FUNCTION__, array($address, $block));
	}
	
	function eth_getStorageAt($address, $at, $block='latest')
	{
		return $this->ether_request(__FUNCTION__, array($address, $at, $block));
	}
	
	function eth_getTransactionCount($address, $block='latest')
	{
		return $this->ether_request(__FUNCTION__, array($address, $block));
	}
	
	function eth_getBlockTransactionCountByHash($tx_hash)
	{
		return $this->ether_request(__FUNCTION__, array($tx_hash));
	}
	
	function eth_getBlockTransactionCountByNumber($tx='latest')
	{
		return $this->ether_request(__FUNCTION__, array($tx));
	}
	
	function eth_eth_getUncleCountByBlockHash($block_hash)
	{
		return $this->ether_request(__FUNCTION__, array($block_hash));
	}
	
	function eth_getUncleCountByBlockNumber($block='latest')
	{
		return $this->ether_request(__FUNCTION__, array($block));
	}
	
	function eth_getCode($address, $block='latest')
	{
		return $this->ether_request(__FUNCTION__, array($address, $block));
	}
	
	function eth_sign($address, $input)
	{
		return $this->ether_request(__FUNCTION__, array($address, $input));
	}
	
	function eth_sendTransaction($transaction)
	{
		if(!is_a($transaction, 'Ethereum_Transaction')
		{
			throw new ErrorException('Transaction object expected');
		}
		else
		{
			return $this->ether_request(__FUNCTION__, $transaction->to_param_array());	
		}
	}
}

class Ethereum_Transaction
{
	private $to, $from, $gas, $gasPrice, $value, $data, $nonce;
	
	function __construct($from, $to, $gas, $gasPrice, $value, $data='', $nonce=NULL)
	{
		$this->from = $from;
		$this->to = $to;
		$this->gas = $gas;
		$this->gasPrice = $gasPrice;
		$this->value = $value;
		$this->data = $data;
		$this->nonce = $nonce;
	}
	
	function to_param_array()
	{
		return array(
			array
			(
				'from'=>$this->from,
				'to'=>$this->to,
				'gas'=>$this->gas,
				'gasPrice'=>$this->gasPrice,
				'value'=>$this->value,
				'data'=>$this->data,
				'nonce'=>$this->nonce
			)
		);
	}
}