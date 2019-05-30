<?php
/**
 * Created by PhpStorm.
 * User: kousakananako
 * Date: 2019/05/30
 * Time: 2:07
 */



class ROSRA implements \Iterator, \ArrayAccess, \Countable {
	public $parsed = [];
	public $raw = [];
	public $current;
	public $current_content;
	protected $socket;
	public function debug($str){
		var_dump($str);
	}
	public function __construct($raw) {
		$this->current = 0;
		$position = array_keys($raw,'!re');
		if (isset($position[1])){
			$length = $position[1] - $position[0];
		} else return [];
		$raw = array_chunk($raw, $length);
		array_pop($raw);
		$this->raw = $raw;
	}
	public function next(){
		++$this->current;
	}
	public function current() {
		if (isset($this->parsed[$this->current])){
			return $this->parsed[$this->current];
		} elseif (isset($this->raw[$this->current])){
			return $this->parseResponse($this->raw[$this->current])[0];
		} else {
			return FALSE;
		}
	}
	public function key() {
		return $this->current;
	}
	public function valid() {
		return isset($this->raw[$this->current]);
	}
	public function count() {
		return count($this->raw);
	}
	public function rewind() {
		$this->current = 0;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->parsed[] = $value;
			throw new \Exception('don\'t append to me It will be overwrite sometime then cause a Bug I PROMISE');
		} else {
			$this->parsed[$offset] = $value;
		}
	}
	public function offsetExists($offset) {
			return isset($this->raw[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->parsed[$offset]);
		unset($this->raw[$offset]);
	}
	public function offsetGet($offset) {
		if (isset($this->parsed[$offset])){
			return $this->parsed[$offset];
		} elseif(isset($this->raw[$offset])) {
			return $this->parsed[$offset] = $this->parseResponse($this->raw[$offset])[0];
		}
	}
	public function flush(){
		$this->parsed_all = FALSE;
		$this->read_all = FALSE;
		$this->raw = [];
		$this->parsed = [];
	}

	private function parseResponse(array $response): array
	{
		$result = [];
		$i      = -1;
		$lines  = \count($response);
		foreach ($response as $key => $value) {
			switch ($value) {
				case '!re':
					$i++;
					break;
				case '!fatal':
					$result = $response;
					break 2;
				case '!trap':
				case '!done':
					// Check for =ret=, .tag and any other following messages
					for ($j = $key + 1; $j <= $lines; $j++) {
						// If we have lines after current one
						if (isset($response[$j])) {
							$this->pregResponse($response[$j], $matches);
							if (isset($matches[1][0], $matches[2][0])) {
								$result['after'][$matches[1][0]] = $matches[2][0];
							}
						}
					}
					break 2;
				default:
					$this->pregResponse($value, $matches);
					if (isset($matches[1][0], $matches[2][0])) {
						$result[$i][$matches[1][0]] = $matches[2][0];
					}
					break;
			}
		}
		return $result;
	}
	private function pregResponse(string $value, &$matches)
	{
		preg_match_all('/^[=|\.](.*)=(.*)/', $value, $matches);
	}
}
