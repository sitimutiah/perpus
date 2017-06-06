<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";
include_once "library/inc.library.php";

# TOMBOL SIMPAN DIKLIK
if(isset($_POST['btnSimpan'])){
	# Baca Variabel Form
	$txtJudul		= $_POST['txtJudul'];
	$txtIsbn		= $_POST['txtIsbn'];
	$txtPengarang	= $_POST['txtPengarang'];
	$txtHalaman		= $_POST['txtHalaman'];
	$txtJumlah		= $_POST['txtJumlah'];
	$txtTahun		= $_POST['txtTahun'];
	$txtSinopsis	= $_POST['txtSinopsis'];
	$cmbKategori	= $_POST['cmbKategori'];
	$cmbPenerbit	= $_POST['cmbPenerbit'];
	
	# Validasi form, jika kosong sampaikan pesan error
	$pesanError = array();
	if (trim($txtJudul)=="") {
		$pesanError[] = "Data <b>Judul Buku</b> tidak boleh kosong, harus dilengkapi !";		
	}
	if (trim($txtIsbn)=="") {
		$pesanError[] = "Data <b>ISBN</b> tidak boleh kosong, bisa diisi - untuk mengosongkan !";		
	}
	if (trim($txtPengarang)=="") {
		$pesanError[] = "Data <b>Pengarang</b> tidak boleh kosong, bisa diisi - untuk mengosongkan !";		
	}
	if (trim($txtHalaman)=="" or ! is_numeric(trim($txtHalaman))) {
		$pesanError[] = "Data <b>Tebal Halaman</b> masih kosong, harus diisi angka !";
	}
	if (trim($txtJumlah)=="" or ! is_numeric(trim($txtJumlah))) {
		$pesanError[] = "Data <b>Jumlah Koleksi</b> masih kosong, harus diisi angka !";		
	}
	if (trim($txtTahun)=="" or ! is_numeric(trim($txtTahun))) {
		$pesanError[] = "Data <b>Tahun Terbit </b> masih kosong, harus diisi angka !";		
	}
	if (trim($txtSinopsis)=="") {
		$pesanError[] = "Data <b>Sinopsis</b> tidak boleh kosong, bisa diisi - untuk mengosongkan !";		
	}
	if (trim($cmbPenerbit)=="Kosong") {
		$pesanError[] = "Data <b>Penerbit</b> belum ada yang dipilih, silahkan pilih dulu !";		
	}
	if (trim($cmbKategori)=="Kosong") {
		$pesanError[] = "Data <b>Kategori</b> belum ada yang dipilih, silahkan pilih dulu !";		
	}

	# JIKA ADA PESAN ERROR DARI VALIDASI
	if (count($pesanError)>=1 ){
		echo "<div class='mssgBox'>";
		echo "<img src='images/attention.png'> <br><hr>";
			$noPesan=0;
			foreach ($pesanError as $indeks=>$pesan_tampil) { 
			$noPesan++;
				echo "&nbsp;&nbsp; $noPesan. $pesan_tampil<br>";	
			} 
		echo "</div> <br>"; 
	}
	else {
		# SIMPAN DATA KE DATABASE. 
		// Jika tidak menemukan error, simpan data ke database

		// Membaca Kode Buku dari form
		$Kode	= $_POST['txtKode'];

		# Baca keberadaan file gambar baru pada form
		if (empty($_FILES['txtNamaFile']['tmp_name'])) {
			$nama_file = $_POST['txtFileSembunyi'];
		}
		else  {
			// Jika file gambar lama ada, akan dihapus
			$txtFileSembunyi = $_POST['txtFileSembunyi'];
			if(file_exists("foto/buku/$txtFileSembunyi")) {
				unlink("foto/buku/$txtFileSembunyi");	
			}

			# Jika gambar lama kosong, atau ada gambar baru, maka Mengkopi file gambar
			$nama_file = $_FILES['txtNamaFile']['name'];
			$nama_file = stripslashes($nama_file);
			$nama_file = str_replace("'","",$nama_file);
			
			// Perintah mengkopi file ke folder foto/buku
			$nama_file = $Kode.".".$nama_file;
			copy($_FILES['txtNamaFile']['tmp_name'],"foto/buku/".$nama_file);					
		}
		
		// Skrip simpan data ke tabel database
		$mySql	= "UPDATE buku SET judul	= '$txtJudul',
								isbn		= '$txtIsbn',
								pengarang	= '$txtPengarang',
								halaman		= '$txtHalaman',
								jumlah		= '$txtJumlah',
								th_terbit	= '$txtTahun',
								gambar		= '$nama_file',
								sinopsis	= '$txtSinopsis',
								kd_penerbit	= '$cmbPenerbit',
								kd_kategori	= '$cmbKategori'
						WHERE kd_buku ='$Kode'";
		$myQry	= mysql_query($mySql, $koneksidb) or die ("Gagal query ".mysql_error());
		if($myQry){
			echo "<meta http-equiv='refresh' content='0; url=?open=Buku-Data'>";
		}
		exit;
	}
} // Penutup POST
	
# TAMPILKAN DATA UNTUK DIEDIT
$Kode	= $_GET['Kode']; 
$mySql 	= "SELECT * FROM buku WHERE kd_buku='$Kode'";
$myQry 	= mysql_query($mySql, $koneksidb)  or die ("Query ambil data salah : ".mysql_error());
$myData	= mysql_fetch_array($myQry);
	// Membaca data, lalu disimpan dalam variabel data
	$dataKode		=  $myData['kd_buku'];
	$dataJudul		= isset($_POST['txtJudul']) ? $_POST['txtJudul'] : $myData['judul'];
	$dataIsbn		= isset($_POST['txtIsbn']) ? $_POST['txtIsbn'] : $myData['isbn'];
	$dataPengarang	= isset($_POST['txtPengarang']) ? $_POST['txtPengarang'] : $myData['pengarang'];
	$dataHalaman	= isset($_POST['txtHalaman']) ? $_POST['txtHalaman'] : $myData['halaman'];
	$dataJumlah		= isset($_POST['txtJumlah']) ? $_POST['txtJumlah'] :  $myData['jumlah'];
	$dataTahun		= isset($_POST['txtTahun']) ? $_POST['txtTahun'] :  $myData['th_terbit'];
	$dataSinopsis	= isset($_POST['txtSinopsis']) ? $_POST['txtSinopsis'] : $myData['sinopsis'];
	$dataPenerbit	= isset($_POST['cmbPenerbit']) ? $_POST['cmbPenerbit'] : $myData['kd_penerbit'];
	$dataKategori	= isset($_POST['cmbKategori']) ? $_POST['cmbKategori'] : $myData['kd_kategori'];
?>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self" enctype="multipart/form-data">
  <table  class="table-list" width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <th colspan="3" scope="col">UBAH DATA BUKU </th>
    </tr>
    <tr>
      <td width="16%"><strong>Kode</strong></td>
      <td width="1%"><strong>:</strong></td>
      <td width="83%"><input name="textfield" value="<?php echo $dataKode; ?>" size="14" maxlength="10" readonly="readonly"/>
      <input name="txtKode" type="hidden" value="<?php echo $dataKode; ?>" /></td>
    </tr>
    <tr>
      <td><strong>Judul Buku </strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtJudul" value="<?php echo $dataJudul; ?>" size="80" maxlength="100" /></td>
    </tr>
    <tr>
      <td><strong>ISBN</strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtIsbn" id="txtIsbn" value="<?php echo $dataIsbn; ?>" size="30" maxlength="30" /></td>
    </tr>
    <tr>
      <td><strong>Pengarang</strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtPengarang" value="<?php echo $dataPengarang; ?>" size="80" maxlength="200" /></td>
    </tr>
    <tr>
      <td><strong>Halaman</strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtHalaman" value="<?php echo $dataHalaman; ?>" size="10" maxlength="4"/></td>
    </tr>
    <tr>
      <td><strong>Jumlah</strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtJumlah" value="<?php echo $dataJumlah; ?>" size="10" maxlength="4"/></td>
    </tr>
    <tr>
      <td><strong>Tahun Terbit</strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtTahun" value="<?php echo $dataTahun; ?>" size="10" maxlength="4"/></td>
    </tr>
    
    <tr>
      <td><strong>Kategori</strong></td>
      <td><strong>:</strong></td>
      <td><select name="cmbKategori">
          <option value="Kosong">....</option>
          <?php
		  // Skrip membuat daftar Kategori pada ComboBox (list/Menu)
	  $bacaSql = "SELECT * FROM kategori ORDER BY kd_kategori";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_kategori'] == $dataKategori) {
			$cek = " selected";
		} else { $cek=""; }
		echo "<option value='$bacaData[kd_kategori]' $cek>$bacaData[nm_kategori]</option>";
	  }
	  ?>
      </select></td>
    </tr>
    <tr>
      <td><b>Penerbit</b></td>
      <td><b>:</b></td>
      <td><b>
        <select name="cmbPenerbit">
          <option value="Kosong">....</option>
          <?php
		// Skrip menampilkan data Penerbit ke dalam ComboBox (List/Menu)
	  $bacaSql = "SELECT * FROM penerbit ORDER BY kd_penerbit";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_penerbit'] == $dataPenerbit) {
			$cek = " selected";
		} else { $cek=""; }
		echo "<option value='$bacaData[kd_penerbit]' $cek> $bacaData[nm_penerbit]</option>";
	  }
	  ?>
        </select>
      </b></td>
    </tr>
    <tr>
      <td><b>Gambar</b></td>
      <td><b>:</b></td>
      <td><input name="txtNamaFile" type="file" id="txtNamaFile" size="40" />
      <input name="txtFileSembunyi" type="hidden" value="<?php echo $myData['gambar']; ?>" /></td>
    </tr>
    <tr>
      <td><b>Sinopsis</b></td>
      <td><b>:</b></td>
      <td><textarea name="txtSinopsis" cols="50" rows="4"><?php echo $dataSinopsis; ?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="btnSimpan" value=" SIMPAN " style="cursor:pointer;"></td>
    </tr>
  </table>
</form>
