<?php

class RelaisBoard
{
	private $_timeout = 2.5;

	private $_fb;
	private $_port;

	const NO_OPERATION = 0;
	const SETUP = 1;
	const GET_PORT = 2;
	const SET_PORT = 3;
	const GET_OPTION = 4;
	const SET_OPTION = 5;


	function __construct($port)
	{
		$this->_port = $port;
		exec("/bin/stty -F $port 19200 sane raw cs8 hupcl cread clocal -echo -onlcr ");
	}

	public function blackout()
	{
		$response = $this->send(RelaisBoard::SET_PORT,0,0);
		return $response && $response['code'] === 252;
	}

	public function toggleById($switchId)
	{
		$boardAddress = floor($switchId / 8)+1;
		$switchPos = $switchId % 8;

		$switchStates = $this->getSwitchStates(RelaisBoard::GET_PORT);

		$boardState = array_slice($switchStates,($boardAddress-1)*8,8);

		$switchPos = $switchId % 8;

		$oldState = 0;
		for ($i=0; $i < 8; $i++) {
			$oldState = $oldState | ((int)$boardState[$i] * pow(2,$i));
		}

		$newState = pow(2,$switchPos) ^ $oldState;

		$response = $this->send(RelaisBoard::SET_PORT,$boardAddress,$newState);
		return $response && $response['code'] === 252;
	}

	public function getBoardCount()
	{
		return ceil(count($this->getSwitchStates())/8);
	}

	public function getSwitchStates()
	{
		$currentAddress = 1;
		$switchStates = array();
		$response = $this->send(RelaisBoard::GET_PORT,$currentAddress);
		if (!$response) {
			return false;
		}
		$responseCode = $response['code'];
		do {
			$currentAddress++;

			$data = $response['data'];
			for ($i=0; $i < 8; $i++) {
				// pow(2,$i) goes through 00000001, 00000010, 00000100 etc,
				// which are used as masks for the data in the format 01100101
				array_push($switchStates,(pow(2, $i) & $data) > 0);
			}

			$response = $this->send(RelaisBoard::GET_PORT,$currentAddress);
			$responseCode = $response['code'];

		} while ($responseCode === 253);

		return $switchStates;
	}

	public function send($cmd,$address,$data = 0)
	{
		$xor = $cmd ^ $address ^ $data;

		$message = pack("C*",$cmd,$address,$data,$xor);
		if (!$this->_open()) {
			return false;
		};
		$this->_write($message);
		try {
			$response = unpack("Ccode/Caddr/Cdata/Cxor",$this->_read());
		} catch(Exception $e) {
			return false;
		}

		if ($address !== 0 && $response['code']===$cmd) {
			$this->_setup();
			if (!$this->_open()) {
				return false;
			};
			$this->_write($message);
			$response = unpack("Ccode/Caddr/Cdata/Cxor",$this->_read());
		}

		$this->_close();

		return $response;
	}

	private function _setup()
	{
		return $this->send(RelaisBoard::SETUP,1,0);
	}

	private function _open()
	{
		try {
			$this->_fp = fopen($this->_port,"w+");
		} catch(Exception $e) {
			return false;
		}
		if(!$this->_fp) return false;
		return true;
	}

	private function _write($data)
	{
		// Set blocking mode for writing
		stream_set_blocking($this->_fp,1);
		fwrite($this->_fp,$data);
	}

	private function _read()
	{
		$response = "";

		$breaktime = microtime(true)+$this->_timeout;
		// Set non blocking mode for reading
		stream_set_blocking($this->_fp,0);
		do{
		  // Try to read one character from the device
		  $c = fgetc($this->_fp);
		  // Wait for data to arive 
		  if($c === false){
		      usleep(50000); // sleep 50 ms (50000 microseconds)
		      continue;
		  }  
		  
		  $response.=$c;
		    
		}while(strlen(bin2hex($response))!==8 && microtime(true)<$breaktime); 
		return $response;
	}

	private function _close()
	{
		fclose($this->_fp);
	}
}
