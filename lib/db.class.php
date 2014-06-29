<?php
class EPDB {
	var $db_host = 'localhost';
	var $db_user = 'erpy';
	var $db_passwd = 'erpy000';
	var $db_name = 'social';

	public function connect() {
		$conn = new mysqli($this->db_host, $this->db_user, $this->db_passwd, $this->db_name);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		$conn->autocommit(FALSE);

		return $conn;
	}

	public function select($conn, $s) {
		$result = $conn->query($s);	
		return $result;
	}

	public function data_exist($conn, $s) {
		$result = $conn->query($s);	
		if ($result->num_rows > 0)
			return 1;
		else
			return 0;
	}

	public function commit($conn) {
		if (!$conn->commit()) {
			die("Transaction commit failed\n");
		}
	}

	public function close($conn) {
		$conn->close();
	}
} // class

?>
