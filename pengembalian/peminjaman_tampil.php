<?php
include_once "../library/inc.seslogin.php";

# UNTUK PAGING (PEMBAGIAN HALAMAN)
$baris	= 50;
$hal 	= isset($_GET['hal']) ? $_GET['hal'] : 1;
$pageSql= "SELECT * FROM peminjaman";
$pageQry= mysql_query($pageSql, $koneksidb) or die ("error paging: ".mysql_error());
$jumlah	= mysql_num_rows($pageQry);
$maks	= ceil($jumlah/$baris);
$mulai	= $baris * ($hal-1); 
?>
<table width="850" border="0" cellpadding="2" cellspacing="1" class="table-border">
  <tr>
    <td colspan="2" align="right"><h1><b>DATA PEMINJAMAN </b></h1></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	<table class="table-list" width="100%" border="0" cellspacing="1" cellpadding="2">
      <tr>
        <th width="21" align="center">No</th>
        <th width="84">No. Pinjam </th>
        <th width="84">Tgl. Pinjam </th>
        <th width="68">Kode</th>
        <th width="103">NIS</th>
        <th width="286">Nama Siswa </th>
        <th width="70">Lama Pjm </th>
        <td colspan="2" align="center" bgcolor="#CCCCCC"><strong>Tools</strong></td>
      </tr>
      <?php
	  // Skrip menampilkan data Pinjam
	$mySql = "SELECT peminjaman.*, siswa.nisn, siswa.nm_siswa
				FROM peminjaman 
				LEFT JOIN siswa ON peminjaman.kd_siswa = siswa.kd_siswa
				ORDER BY peminjaman.no_pinjam DESC LIMIT $mulai, $baris";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor = $mulai; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
		$Kode = $myData['no_pinjam'];
		
		// Membaca Status Pengembalian
		if($myData['status']=="Kembali") {
			$menuKembali	= "-";
		}
		else {
			$menuKembali	= "<a href='?open=Pengembalian-Baru&Kode=$Kode' target='_blank'>Kembali</a>";
		}
		
		if($nomor%2==1) { $warna=""; } else {$warna="#F5F5F5";}
	?>
	  <tr bgcolor="<?php echo $warna; ?>">
        <td><?php echo $nomor; ?></td>
        <td><?php echo $myData['no_pinjam']; ?></td>
        <td><?php echo IndonesiaTgl($myData['tgl_pinjam']); ?></td>
        <td><?php echo $myData['kd_siswa']; ?></td>
        <td><?php echo $myData['nisn']; ?></td>
        <td><?php echo $myData['nm_siswa']; ?></td>
        <td><?php echo $myData['lama_pinjam']; ?> hr</td>
        <td width="31" align="center"><a href="../peminjaman/nota_pinjam.php?Kode=<?php echo $Kode; ?>" target="_blank">Cetak</a></td>
        <td width="51" align="center"> <?php echo $menuKembali; ?> </td>
      </tr>
      <?php } ?>
    </table></td>
  </tr>
  <tr class="selKecil">
    <td width="344"><b>Jumlah Data :</b> <?php echo $jumlah; ?></td>
    <td width="535" align="right"><b>Halaman ke :</b>
    <?php
	for ($h = 1; $h <= $maks; $h++) {
		echo " <a href='?open=Peminjaman-Tampil&hal=$h'>$h</a> ";
	}
	?></td>
  </tr>
</table>
