<?php

class FtpUpdate {
	private $pathToLocalRoot = "";
	private $pathToServerRoot = "";
	private $ftpHost = "";
	private $ftpPort = "";
	private $ftpUser = "";
	private $ftpPass = "";
	
	public function __construct(){
		$this->pathToLocalRoot = '';
		$this->pathToServerRoot = "";
		$this->ftpHost = "";
		$this->ftpPort = "";
		$this->ftpUser = "";
		$this->ftpPass = "";
	}
	
	public function update(){
		// teste FTP-Verbindung
		$this->testFtpConnection();
		$this->copyProjectToServer();
	}
	
	public function testFtpConnection(){
		// Verbindung aufbauen
		echo "* Anmeldeversuch als $this->ftpUser@$this->ftpHost\n";
		$conn_id = ftp_connect($this->ftpHost) or die("Couldn't connect to $this->ftpHost");
		if (@ftp_login($conn_id, $this->ftpUser, $this->ftpPass)) {
			echo "* Angemeldet als $this->ftpUser@$this->ftpHost\n";
			echo "* Aktuelles Verzeichnis: " . ftp_pwd($conn_id) . "\n";
		} else {
			echo "* Anmeldung als $this->ftpUser nicht m?glich\n";
		}
		ftp_close($conn_id);
	}
	
	private function copyProjectToServer(){
		$conn_id = ftp_connect($this->ftpHost);
		if (@ftp_login($conn_id, $this->ftpUser, $this->ftpPass)) {
			echo "* Kopiere Projekt auf den Server ... \n";
			$this->copyFilesToServer($conn_id);
		} else {
			die("* Kopieren des Projekts auf den Server nicht moeglich.\n");
		}
		ftp_close($conn_id);
	}
	
	private function copyFilesToServer($conn_id){
		
		// Verzeichniswechsel in ServerRoot 
		ftp_chdir($conn_id, $this->pathToServerRoot);
		$adminFiles = array(
				"action1.php",
				"ftpupdate.php",
				"index.php"
		);
		
		foreach($adminFiles as $file){
			// Loeschen
			if (ftp_delete($conn_id, $file)) {
				echo "* $file erfolgreich geloescht.\n";
			} else {
				echo "* Ein Fehler trat beim Loeschen von $file auf.\n";
			}
			// Hochladen
			if (ftp_put($conn_id, $file, ($this->pathToLocalRoot . $file), FTP_ASCII)) {
				echo "* $file erfolgreich hochgeladen.\n";
			} else {
				echo "* Ein Fehler trat beim Hochladen von $file auf.\n";
			}
		}
	}
}

// Start der Prozedur
echo "\n";
echo "************************************************************************\n";
echo "* \n";
$obj = new FtpUpdate();
echo "* Update Skript\n";
echo "* \n";
$obj->update();
echo "************************************************************************\n";



?>