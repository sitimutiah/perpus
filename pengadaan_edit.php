<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";
include_once "library/inc.library.php";

if(isset($_POST['btnSimpan'])){
	# Baca variabel
	$txtTanggal 	= InggrisTgl($_POST['txtTanggal']);
	$cmbBuku		= $_POST['cmbBuku'];
	$txtAsalBuku	= $_POST['txtAsalBuku'];
	$txtJumlah		= $_POST['txtJumlah'];
	$txtKeterangan	= $_POST['txtKeterangan'];
	
	// Skrip Validasi form
	$pesanError = array();
	if (trim($txtTanggal)=="--") {
		$pesanError[] = "Data <b>Tanggal</b> belum diisi, silahkan pilih pada kalender !";		
	}
	if (trim($cmbBuku)=="Kosong") {
		$pesanError[] = "Data <b>Buku</b> belum dipilih, silahkan pilih pada Combo !";		
	}
	if (trim($txtAsalBuku)=="") {
		$pesanError[] = "Data <b>Asal Buku</b> tidak boleh kosong, silahkan dilengkapi !";		
	}
	if (trim($txtJumlah)=="" or ! is_numeric(trim($txtJumlah))) {
		$pesanError[] = "Data <b>Jumlah</b> masih kosong, harus diisi angka !";
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
		// Membaca Kode Siswa dari form hidden
		$Kode	= $_POST['txtKode'];
		
		// Skrip simpan data ke tabel database
		$mySql	= "UPDATE pengadaan SET 
								tgl_pengadaan= '$txtTanggal',
								kd_buku		= '$cmbBuku',
								asal_buku	= '$txtAsalBuku',
								jumlah		= '$txtJumlah',
								keterangan= '$txtKeterangan'
						WHERE no_pengadaan ='$Kode'";
		$myQry	= mysql_query($mySql, $koneksidb) or die ("Gagal query ".mysql_error());
		if($myQry){
			echo "<meta http-equiv='refresh' content='0; url=?open=Pengadaan-Data'>";
		}
		exit;
	}
} // Penutup POST
	
# TAMPILKAN DATA UNTUK DIEDIT
$Kode	= $_GET['Kode']; 
$mySql 	= "SELECT pengadaan.*, buku.kd_kategori FROM pengadaan 
			LEFT JOIN buku ON pengadaan.kd_buku = buku.kd_buku 
			WHERE pengadaan.no_pengadaan='$Kode'";
$myQry 	= mysql_query($mySql, $koneksidb)  or die ("Query ambil data salah : ".mysql_error());
$myData	= mysql_fetch_array($myQry);
	// Membaca data, lalu disimpan dalam variabel data
	$dataKode		=  $myData['no_pengadaan'];
	$dataTanggal 	= isset($_POST['txtTanggal']) ? $_POST['txtTanggal'] : IndonesiaTgl($myData['tgl_pengadaan']);
	$dataKategori	= isset($_POST['cmbKategori']) ? $_POST['cmbKategori'] : $myData['kd_kategori'];
	$dataBuku		= isset($_POST['cmbBuku']) ? $_POST['cmbBuku'] : $myData['kd_buku'];
	$dataAsalBuku	= isset($_POST['txtAsalBuku']) ? $_POST['txtAsalBuku'] : $myData['asal_buku'];
	$dataJumlah		= isset($_POST['txtJumlah']) ? $_POST['txtJumlah'] : $myData['jumlah'];
	$dataKeterangan	= isset($_POST['txtKeterangan']) ? $_POST['txtKeterangan'] : $myData['keterangan'];
?>
<SCRIPT language="JavaScript">
function submitform() {
	document.form1.submit();
}
</SCRIPT>
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self" enctype="multipart/form-data">
  <table  class="table-list" width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <th colspan="3" scope="col">UBAH PENGADAAN BUKU </th>
    </tr>
    <tr>
      <td width="16%"><strong>No. Pengadaan </strong></td>
      <td width="1%"><strong>:</strong></td>
      <td width="83%"><input name="txtNomor" value="<?php echo $dataKode; ?>" size="20" maxlength="20" readonly="readonly"/>
      <input name="txtKode" type="hidden" value="<?php echo $dataKode; ?>" /></td>
    </tr>
    <tr>
      <td><strong>Tgl.  Pengadaan </strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtTanggal" type="text" class="tcal" value="<?php echo $dataTanggal; ?>" size="18" maxlength="20" /></td>
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
      <td><strong>Buku</strong></td>
      <td><strong>:</strong></td>
      <td><select name="cmbBuku">
          <option value="Kosong">....</option>
          <?php
		  // Skrip menampilkan data Buku ke ComboBo (ListMenu)
	  $bacaSql = "SELECT * FROM buku WHERE kd_kategori='$dataKategori' ORDER BY kd_buku";
	  $bacaQry = mysql_query($bacaSql, $koneksidb) or die ("Gagal Query".mysql_error());
	  while ($bacaData = mysql_fetch_array($bacaQry)) {
		if ($bacaData['kd_buku'] == $dataBuku) {
			$cek = " selected";
		} else { $cek=""; }
		
		echo "<option value='$bacaData[kd_buku]' $cek> $bacaData[kd_buku] - $bacaData[judul]</option>";
	  }
	  ?>
        </select>      </td>
    </tr>
    <tr>
      <td><strong> Asal Buku </strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtAsalBuku" value="<?php echo $dataAsalBuku; ?>" size="80" maxlength="100" /></td>
    </tr>
    <tr>
      <td><strong>Jumlah</strong></td>
      <td><strong>:</strong></td>
      <td><b>
        <input name="txtJumlah" value="<?php echo $dataJumlah; ?>" size="4" maxlength="2"/>
      </b></td>
    </tr>
    <tr>
      <td><strong> Keterangan </strong></td>
      <td><strong>:</strong></td>
      <td><input name="txtKeterangan" value="<?php echo $dataKeterangan; ?>" size="80" maxlength="100" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="btnSimpan" value=" SIMPAN " style="cursor:pointer;"></td>
    </tr>
  </table>
</form>
