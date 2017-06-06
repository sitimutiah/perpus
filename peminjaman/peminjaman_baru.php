<?php
include_once "../library/inc.seslogin.php"; 
include_once "../library/inc.connection.php";
include_once "../library/inc.library.php"; 

$userLogin	= $_SESSION['SES_LOGIN'];

# HAPUS DAFTAR
if(isset($_GET['Aksi'])){
	if(trim($_GET['Aksi'])=="Hapus"){
		$id			= $_GET['id'];
		
		// Hapus Tmp jika datanya sudah dipindah
		$mySql = "DELETE FROM tmp_pinjam WHERE id='$id' AND kd_user='$userLogin'";
		mysql_query($mySql, $koneksidb) or die ("Gagal menghapus tmp".mysql_error());
	}
}
// =========================================================================

# TOMBOL TAMBAH DIKLIK
if(isset($_POST['btnTambah'])){
	// Baca Variabel
	$cmbSiswa		= $_POST['cmbSiswa'];
	$cmbBuku		= $_POST['cmbBuku'];
	$txtJumlah		= $_POST['txtJumlah'];

	// Validasi form
	$pesanError = array();
	if (trim($cmbSiswa)=="Kosong") {
		$pesanError[] = "Data <b>Siswa</b> belum dipilih, harus Anda pilih dari combo !";		
	}
	if (trim($cmbBuku)=="Kosong") {
		$pesanError[] = "Data <b>Judul Buku</b> belum dipilih, harus Anda pilih dari combo !";		
	}
	if (trim($txtJumlah)=="" or ! is_numeric(trim($txtJumlah))) {
		$pesanError[] = "Data <b>Jumlah</b> masih kosong, harus diisi angka ( 1 - 4 ) !";
	}
	else {
		if($txtJumlah < 1 or $txtJumlah > 4) {
			$pesanError[] = "Data <b>Jumlah</b> minimal 1 dan maksimal 4 buku dengan judul yang sama !";
		}
	}

	# JIKA ADA PESAN ERROR DARI VALIDASI
	if (count($pesanError)>=1 ){
		echo "<div class='mssgBox'>";
		echo "<img src='../images/attention.png'> <br><hr>";
			$noPesan=0;
			foreach ($pesanError as $indeks=>$pesan_tampil) { 
			$noPesan++;
				echo "&nbsp;&nbsp; $noPesan. $pesan_tampil<br>";	
			} 
		echo "</div> <br>"; 
	}
	else {
		# SIMPAN DATA BUKU KE TMP
		// Jika jumlah error pesanError tidak ada, skrip di bawah dijalankan
		$tmpSql 	= "INSERT INTO tmp_pinjam (kd_buku, jumlah, kd_user) VALUES ('$cmbBuku', '$txtJumlah', '$userLogin')";
		mysql_query($tmpSql, $koneksidb) or die ("Gagal Query tmp : ".mysql_error());				
	}
}

# ========================================================================================================
# JIKA TOMBOL SIMPAN TRANSAKSI DIKLIK
if(isset($_POST['btnSimpan'])){
	// Baca variabel data form
	$txtTanggal 	= InggrisTgl($_POST['txtTanggal']);
	$cmbSiswa		= $_POST['cmbSiswa'];
	$txtKeterangan	= $_POST['txtKeterangan'];
	$txtLama		= $_POST['txtLama'];
	
	// Skrip Validasi form
	$pesanError = array();
	if (trim($txtTanggal)=="--") {
		$pesanError[] = "Data <b>Tanggal Pinjam</b> belum diisi, silahkan pilih pada kalender !";		
	}
	if (trim($cmbSiswa)=="Kosong") {
		$pesanError[] = "Data <b>Siswa</b> belum dipilih, silahkan pilih pada Combo !";		
	}
	if (trim($txtLama)=="" or ! is_numeric(trim($txtLama))) {
		$pesanError[] = "Data <b>Lama Pinjam (hari)</b> masih kosong, harus diisi angka !";
	}

	// Validasi jika belum ada satupun data item yang dimasukkan
	$tmpSql ="SELECT COUNT(*) As qty FROM tmp_pinjam WHERE kd_user='$userLogin' ";
	$tmpQry = mysql_query($tmpSql, $koneksidb) or die ("Gagal Query Tmp".mysql_error());
	$tmpData = mysql_fetch_array($tmpQry);
	if ($tmpData['qty'] < 1) {
		$pesanError[] = "<b>DAFTAR BUKU MASIH KOSONG</b>, Daftar item buku belum ada yang dimasukan, <b>minimal 1 Buku</b>.";
	}

			
	# JIKA ADA PESAN ERROR DARI VALIDASI
	if (count($pesanError)>=1 ){
		echo "<div class='mssgBox'>";
		echo "<img src='../images/attention.png'> <br><hr>";
			$noPesan=0;
			foreach ($pesanError as $indeks=>$pesan_tampil) { 
			$noPesan++;
				echo "&nbsp;&nbsp; $noPesan. $pesan_tampil<br>";	
			} 
		echo "</div> <br>"; 
	}
	else {
		# SIMPAN KE DATABASE
		// Jika jumlah error pesanError tidak ada, maka proses Penyimpanan akan dikalkukan
		
		// Membuat kode Transaksi baru
		$kodePinjam = buatKode("peminjaman", "PJ");
				
		// Skrip menyimpan data ke tabel transaksi utama
		$mySql	= "INSERT INTO peminjaman(no_pinjam, tgl_pinjam, kd_siswa, keterangan, lama_pinjam, status, kd_user) 
					VALUES ('$kodePinjam', '$txtTanggal', '$cmbSiswa', '$txtKeterangan', '$txtLama', 'Pinjam', '$userLogin')";
		mysql_query($mySql, $koneksidb) or die ("Gagal query peminjaman ".mysql_error());

		// Ambil semua data buku/buku yang dipilih (diambil dari TMP), berdasarkan user yg login
		$tmpSql ="SELECT * FROM tmp_pinjam WHERE kd_user='$userLogin' ";
		$tmpQry = mysql_query($tmpSql, $koneksidb) or die ("Gagal Query baca Tmp".mysql_error());
		while ($tmpData = mysql_fetch_array($tmpQry)) {
			// Membaca data dari tabel TMP
			$kode		= $tmpData['kd_buku'];
			$jumlah		= $tmpData['jumlah'];
			
			// Masukkan semua buku/buku dari TMP ke tabel peminjaman detil
			$itemSql = "INSERT INTO peminjaman_detil(no_pinjam, kd_buku, jumlah) 
						VALUES ('$kodePinjam', '$kode', '$jumlah')";
			mysql_query($itemSql, $koneksidb) or die ("Gagal Query ".mysql_error());
		}
			
		// Kosongkan Tmp jika datanya sudah dipindah
		$hapusSql = "DELETE FROM tmp_pinjam WHERE kd_user='$userLogin' ";
		mysql_query($hapusSql, $koneksidb) or die ("Gagal kosongkan tmp".mysql_error());
		
		// Cetak nota peminjaman
		echo "<script>";
		echo "window.open('nota_pinjam.php?Kode=$kodePinjam')";
		echo "</script>";
	}	
}

# VARIABEL DATA DARI & UNTUK FORM
$noTransaksi 	= buatKode("peminjaman", "PJ");
$dataTanggal 	= isset($_POST['txtTanggal']) ? $_POST['txtTanggal'] : date('d-m-Y');
$dataSiswa		= isset($_POST['cmbSiswa']) ? $_POST['cmbSiswa'] : '';
$dataKeterangan	= isset($_POST['txtKeterangan']) ? $_POST['txtKeterangan'] : '';
$dataLama		= isset($_POST['txtLama']) ? $_POST['txtLama'] : '6';

$dataKategori	= isset($_POST['cmbKategori']) ? $_POST['cmbKategori'] : '';
$dataJumlah		= isset($_POST['txtJumlah']) ? $_POST['txtJumlah'] : '1';
?>
<SCRIPT language="JavaScript">
function submitform() {
	document.form1.submit();
}
</SCRIPT> 

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self">
  <table width="900" cellspacing="1"  class="table-list">
    <tr>
      <td colspan="3"><h1> PEMINJAMAN </h1></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCCC"><strong>DATA TRANSAKSI </strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="19%"><strong>No. Pinjam </strong></td>
      <td width="1%"><strong>:</strong></td>
      <td width="80%"><input name="txtNomor" value="<?php echo $noTransaksi; ?>" size="23" maxlength="20" readonly="readonly"/></td>
    </tr>
    <tr>
      <td><strong>Tgl.  Pinjam </strong></td>
      <td><strong>:</strong></td>
      <td><input type="text" name="txtTanggal" class="tcal" value="<?php echo $dataTanggal; ?>" /></td>
    </tr>
    <tr>
      <td><strong>Siswa</strong></td>
      <td><strong>:</strong></td>
      <td><select name="cmbSiswa">
          <option value="Kosong">....</option>
          <?php
		  // Skrip menampilkan data Siswa ke ComboBo (ListMenu)
	  $bacaSql = "SELECT * FROM siswa ORDER BY kd_siswa";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_siswa'] == $dataSiswa) {
			$cek = " selected";
		} else { $cek=""; }
		
		echo "<option value='$bacaData[kd_siswa]' $cek> $bacaData[nisn] - $bacaData[nm_siswa]</option>";
	  }
	  ?>
        </select>      </td>
    </tr>
    <tr>
      <td><strong> Keterangan </strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtKeterangan" value="<?php echo $dataKeterangan; ?>" size="80" maxlength="100" /></td>
    </tr>
    <tr>
      <td><strong>Lama Pinjam </strong></td>
      <td><strong>:</strong></td>
      <td><b>
        <input name="txtLama" value="<?php echo $dataLama; ?>" size="4" maxlength="2"/>
      </b>hari</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor="#CCCCCC"><strong>INPUT BUKU </strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><strong>Kategori </strong></td>
      <td><strong>:</strong></td>
      <td><select name="cmbKategori" onchange="javascript:submitform();" >
          <option value="Kosong">....</option>
          <?php
		  // Skrip menampilkan data Kategori ke ComboBo (ListMenu)
	  $bacaSql = "SELECT * FROM kategori ORDER BY kd_kategori";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_kategori'] == $dataKategori) {
			$cek = " selected";
		} else { $cek=""; }
		
		echo "<option value='$bacaData[kd_kategori]' $cek> $bacaData[nm_kategori]</option>";
	  }
	  ?>
        </select>      </td>
    </tr>
    <tr>
      <td><strong>Judul Buku  </strong></td>
      <td><strong>:</strong></td>
      <td><select name="cmbBuku">
        <option value="Kosong">....</option>
        <?php
		 // Skrip menampilkan data Buku ke ComboBo (ListMenu), filter per Kategori
	  $bacaSql = "SELECT * FROM buku WHERE kd_kategori='$dataKategori' ORDER BY kd_buku";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_buku'] == $dataBuku) {
			$cek = " selected";
		} else { $cek=""; }
		
		echo "<option value='$bacaData[kd_buku]' $cek> $bacaData[kd_buku] - $bacaData[judul] </option>";
	  }
	  ?>
      </select> </td>
    </tr>
    <tr>
      <td><strong>Jumlah</strong></td>
      <td><strong>:</strong></td>
      <td><b>
        <input name="txtJumlah" size="10" maxlength="1" value="<?php echo $dataJumlah; ?>"/>
        <input name="btnTambah" type="submit" style="cursor:pointer;" value=" Tambah " />
      </b></td>
    </tr>
    <tr>
      <td><strong>DAFTAR BUKU </strong></td>
      <td>&nbsp;</td>
      <td>
	  <table class="table-list" width="740" border="0" cellspacing="1" cellpadding="2">
        <tr>
          <td width="20" bgcolor="#CCCCCC"><strong>No</strong></td>
          <td width="40" bgcolor="#CCCCCC"><strong>Kode</strong></td>
          <td width="438" bgcolor="#CCCCCC"><strong>Judul Buku </strong></td>
          <td width="175" bgcolor="#CCCCCC"><strong>Pengarang</strong></td>
          <td width="41">&nbsp;</td>
        </tr>
	<?php
	// Skrip menampilkan data TMP Buku
	$tmpSql ="SELECT tmp.*, buku.judul, buku.pengarang FROM tmp_pinjam As tmp
		  LEFT JOIN buku ON tmp.kd_buku = buku.kd_buku 
		  WHERE tmp.kd_user='$userLogin' ORDER BY id";
	$tmpQry = mysql_query($tmpSql, $koneksidb) or die ("Gagal Query Tmp".mysql_error());
	$nomor=0; 
	while($tmpData = mysql_fetch_array($tmpQry)) {
		$nomor++;
		$id	=  $tmpData['id'];
	?>
        <tr>
          <td><?php echo $nomor; ?></td>
          <td><?php echo $tmpData['kd_buku']; ?></td>
          <td><?php echo $tmpData['judul']; ?></td>
          <td><?php echo $tmpData['pengarang']; ?></td>
          <td><a href="?Aksi=Hapus&id=<?php echo $id; ?>" target="_self">Delete</a></td>
        </tr>
        <?php } ?>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input name="btnSimpan" type="submit" style="cursor:pointer;" value=" SIMPAN TRANSAKSI " /></td>
    </tr>
  </table>
  <br>
</form>