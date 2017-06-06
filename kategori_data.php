<?php
include_once "library/inc.seslogin.php";
include_once "library/inc.connection.php";

# UNTUK PAGING (PEMBAGIAN HALAMAN)
$baris	= 50;
$hal 	= isset($_GET['hal']) ? $_GET['hal'] : 1;
$pageSql= "SELECT * FROM kategori";
$pageQry= mysql_query($pageSql, $koneksidb) or die ("error paging: ".mysql_error());
$jumlah	= mysql_num_rows($pageQry);
$maks	= ceil($jumlah/$baris);
$mulai	= $baris * ($hal-1); 
?>
<table width="800" border="0" cellpadding="2" cellspacing="1" class="table-border">
  <tr>
    <td colspan="2"><h1><b>DATA  KATEGORI</b></h1></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><a href="?open=Kategori-Add" target="_self"><img src="images/btn_add_data.png" height="30" border="0"  /></a></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	<table class="table-list"  width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <th width="4%">No</th>
        <th width="7%">Kode</th>
        <th width="75%">Nama Kategori</th>
        <td colspan="2" align="center" bgcolor="#CCCCCC"><strong>Tools</strong></td>
        </tr>
      <?php
	  // Menampilkan data Kategori
	$mySql = "SELECT * FROM kategori ORDER BY kd_kategori ASC LIMIT $mulai, $baris";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;
		$Kode = $myData['kd_kategori'];
	?>
      <tr>
        <td> <?php echo $nomor; ?> </td>
        <td> <?php echo $myData['kd_kategori']; ?> </td>
        <td> <?php echo $myData['nm_kategori']; ?> </td>
        <td width="7%" align="center"><a href="?open=Kategori-Delete&Kode=<?php echo $Kode; ?>" target="_self" onclick="return confirm(' YAKIN INGIN MENGHAPUS DATA KATEGORI INI ... ?')">Delete</a></td>
        <td width="7%" align="center"><a href="?open=Kategori-Edit&Kode=<?php echo $Kode; ?>" target="_self">Edit</a></td>
      </tr>
	<?php } ?>
    </table></td>
  </tr>
  <tr class="selKecil">
    <td width="387"><strong>Jumlah Data :</strong> <?php echo $jumlah; ?> </td>
    <td width="402" align="right"><strong>Halaman ke :</strong> 
	<?php
	for ($h = 1; $h <= $maks; $h++) {
		echo " <a href='?open=Kategori-Data&hal=$h'>$h</a> ";
	}
	?> </td>
  </tr>
</table>

