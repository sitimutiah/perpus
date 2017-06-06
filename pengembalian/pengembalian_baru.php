<?php
include_once "../library/inc.seslogin.php";

$userLogin	= $_SESSION['SES_LOGIN'];

# JIKA TOMBOL SIMPAN TRANSAKSI DIKLIK
if(isset($_POST['btnSimpan'])){
	# Baca variabel
	$txtNoPinjam	= $_POST['txtNoPinjam'];
	$txtTglKembali 	= InggrisTgl($_POST['txtTglKembali']);
	$txtDenda		= $_POST['txtDenda'];
	
	// Skrip Validasi form
	$pesanError = array();
	if (trim($txtNoPinjam)=="") {
		$pesanError[] = "Data <b>No. Peminjaman</b> tidak terbaca !";		
	}
	if (trim($txtTglKembali)=="--") {
		$pesanError[] = "Data <b>Tanggal Kembali</b> belum diisi, silahkan pilih pada kalender !";		
	}
	if (trim($txtDenda)=="" or ! is_numeric(trim($txtDenda))) {
		$pesanError[] = "Data <b>Denda (Rp)</b> masih kosong, harus diisi angka atau diisi 0 !";
	}

	// Validasi jika sudah ada Transaksi Pengembalian untuk No_Pinjam yang sama
	$cekSql ="SELECT * FROM pengembalian WHERE no_pinjam = '$txtNoPinjam' ";
	$cekQry = mysql_query($cekSql, $koneksidb) or die ("Gagal Query validasi ".mysql_error());
	if (mysql_num_rows($cekQry) >= 1) {
		$pesanError[] = "<b>BUKU SUDAH DIKEMBALIKAN</b>, tidak boleh ada transaksi Pengembalian ganda !";
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
		# Jika jumlah error pesanError tidak ada, maka proses Penyimpanan akan dikalkukan
		
		// Membuat kode Transaksi baru
		$kodeKembali = buatKode("pengembalian", "KB");
				
		// Skrip menyimpan data ke tabel transaksi utama
		$mySql	= "INSERT INTO pengembalian(no_kembali, tgl_kembali, no_pinjam, denda, kd_user) 
					VALUES ('$kodeKembali', '$txtTglKembali', '$txtNoPinjam', '$txtDenda', '$userLogin')";
		mysql_query($mySql, $koneksidb) or die ("Gagal query peminjaman ".mysql_error());
			
		// Update status
		$statusSql = "UPDATE peminjaman SET status='Kembali' WHERE no_pinjam='$txtNoPinjam'";
		mysql_query($statusSql, $koneksidb) or die ("Gagal query status ".mysql_error());

		// Refresh
		echo "<meta http-equiv='refresh' content='0; url=?open=Peminjaman-Tampil'>";
	}	
}

# MEMBACA DATA DARI FORM UTAMA TRANSAKSI 
# TAMPILKAN DATA UNTUK DIEDIT
$Kode	= $_GET['Kode']; 
$mySql 	= "SELECT peminjaman.*, siswa.nisn, siswa.nm_siswa FROM peminjaman 
			LEFT JOIN siswa ON peminjaman.kd_siswa = siswa.kd_siswa
			WHERE peminjaman.no_pinjam ='$Kode'";
$myQry 	= mysql_query($mySql, $koneksidb)  or die ("Query ambil data salah : ".mysql_error());
$myData	= mysql_fetch_array($myQry);
	// Membaca data, lalu disimpan dalam variabel data
	$dataKode		=  $myData['no_pinjam'];
	$dataTglPinjam 	= IndonesiaTgl($myData['tgl_pinjam']);
	$dataSiswaNis	= $myData['nisn'];
	$dataSiswaNama	= $myData['nm_siswa'];
	$dataKeterangan	= $myData['keterangan'];
	$dataLama		= $myData['lama_pinjam'];

$noTransaksi 	= buatKode("pengembalian", "KB");
$dataTglKembali	= isset($_POST['txtTglKembali']) ? $_POST['txtTglKembali'] : date('d-m-Y');
$dataDenda		= isset($_POST['txtDenda']) ? $_POST['txtDenda'] : '0';
?>
<SCRIPT language="JavaScript">
function submitform() {
	document.form1.submit();
}
</SCRIPT> 

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self">
  <table width="900" cellspacing="1"  class="table-list">
    <tr>
      <td colspan="3"><h1> PENGEMBALIAN </h1></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCCC"><strong>PINJAMAN </strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="19%"><strong>No. Pinjam </strong></td>
      <td width="1%"><strong>:</strong></td>
      <td width="80%"><?php echo $dataKode; ?>
        <input name="txtNoPinjam" type="hidden" value="<?php echo $dataKode; ?>" /></td>
    </tr>
    <tr>
      <td><strong>Tgl.  Pinjam </strong></td>
      <td><strong>:</strong></td>
      <td><?php echo $dataTglPinjam; ?></td>
    </tr>
    <tr>
      <td><strong>Siswa</strong></td>
      <td><strong>:</strong></td>
      <td><?php echo $dataSiswaNis; ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>:</strong></td>
      <td><?php echo $dataSiswaNama; ?></td>
    </tr>
    <tr>
      <td><strong> Keterangan </strong></td>
      <td><strong>:</strong></td>
      <td><?php echo $dataKeterangan; ?></td>
    </tr>
    <tr>
      <td><strong>Lama Pinjam </strong></td>
      <td><strong>:</strong></td>
      <td><?php echo $dataLama; ?>  hari</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor="#CCCCCC"><strong>KEMBALI</strong></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><strong>No. Kembali </strong></td>
      <td><strong>:</strong></td>
      <td width="80%"><input type="text" name="txtNoKembali" value="<?php echo $noTransaksi; ?>" size="23" maxlength="20" readonly="readonly"/></td>
    </tr>
    <tr>
      <td><strong>Tgl. Kembali </strong></td>
      <td><strong>:</strong></td>
      <td><input type="text" name="txtTglKembali" class="tcal" value="<?php echo $dataTglKembali; ?>" /></td>
    </tr>
    <tr>
      <td><strong>Denda (Rp.) </strong></td>
      <td><strong>:</strong></td>
      <td><b>
        <input type="text" name="txtDenda" size="20" maxlength="12" value="<?php echo $dataDenda; ?>"/> 
      </b></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input name="btnSimpan" type="submit" style="cursor:pointer;" value=" SIMPAN TRANSAKSI " /></td>
    </tr>
    <tr>
      <td colspan="3"><strong>DAFTAR BUKU PINJAMAN</strong>
	    <table class="table-list" width="740" border="0" cellspacing="1" cellpadding="2">
	      <tr>
	        <td width="20" bgcolor="#CCCCCC"><strong>No</strong></td>
            <td width="40" bgcolor="#CCCCCC"><strong>Kode</strong></td>
            <td width="438" bgcolor="#CCCCCC"><strong>Judul Buku </strong></td>
            <td width="175" bgcolor="#CCCCCC"><strong>Pengarang</strong></td>
          </tr>
	      <?php
		// Skrip menampilkan data Peminjaman_Detil
	$tmpSql ="SELECT detil.*, buku.judul, buku.pengarang FROM peminjaman_detil As detil
			  LEFT JOIN buku ON detil.kd_buku = buku.kd_buku 
			  WHERE detil.no_pinjam = '$Kode' ORDER BY kd_buku";
	$tmpQry = mysql_query($tmpSql, $koneksidb) or die ("Gagal Query detil".mysql_error());
	$nomor=0; 
	while($tmpData = mysql_fetch_array($tmpQry)) {
		$nomor++;
	?>
	      <tr>
	        <td><?php echo $nomor; ?></td>
            <td><?php echo $tmpData['kd_buku']; ?></td>
            <td><?php echo $tmpData['judul']; ?></td>
            <td><?php echo $tmpData['pengarang']; ?></td>
          </tr>
	      <?php } ?>
        </table></td>
    </tr>
  </table>
  <br>
</form>