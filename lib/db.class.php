<?php

class EPDB {
	var $db_host = 'localhost';
	var $db_user = 'erpy';
	var $db_passwd = 'erpy000';
	var $db_name = 'social';
	var $conn;

	public function connect() {
		$this->conn = new mysqli($this->db_host, $this->db_user, $this->db_passwd, $this->db_name);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		$this->conn->autocommit(FALSE);
	}

	public function select($s) {
		$result = $this->conn->query($s);	
/*
		if (!$this->conn->error) {
			echo "===============================================\n";
			printf(">>> Mysql Error message: %s\n", $this->conn->error);
			die(">>> Process is terminated die!!\n");
		}
*/
		return $result;
	}

	public function data_exist($s) {
		$result = $this->conn->query($s);	
		if ($result->num_rows > 0)
			return 1;
		else
			return 0;
	}

	public function commit() {
		if (!$this->conn->commit()) {
			die("Transaction commit failed\n");
		}
	}

	public function close() {
		$this->conn->close();
	}
} // class

?>
