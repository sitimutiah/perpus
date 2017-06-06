<?php
if(isset($_SESSION['SES_LOGIN'])) {
	echo "<h2>Selamat datang di SiPusta - Sistem Informasi Perpustakaan SMU T Way Jepara !</h2>";
	echo "<b> Anda login sebagai Admin";
	exit;
}
else {
	echo "<h2>Selamat datang di SiPusta - Sistem Informasi Perpustakaan SMU T Way Jepara !</h2>";
	echo "<b>Anda belum login, silahkan <a href='?open=Login' alt='Login'>login </a>untuk mengakses sitem ini ";	
}
?>