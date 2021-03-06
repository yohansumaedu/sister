<?php
  session_start();
  require_once '../../lib/dbcon.php';
  require_once '../../lib/mpdf/mpdf.php';
  require_once '../../lib/tglindo.php';
  require_once '../../lib/func.php';

  $x     = $_SESSION['id_loginS'].$_GET['a_departemenS'].$_GET['a_tahunajaranS'].$_GET['a_tingkatS'].$_GET['a_namaS'].$_GET['a_rekeningS'].$_GET['a_keteranganS'];
  $token = base64_encode($x);

  if(!isset($_SESSION)){ // belum login  
    echo 'user has been logout';
  }else{ // sudah login 
    if(!isset($_GET['token']) OR  $token!==$_GET['token']){ //token salah 
      echo 'Token URL tidak sesuai';
    }else{ //token benar
      sleep(1);
      ob_start(); // digunakan untuk convert php ke html
      $out='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>SISTER::Keu - Anggaran</title>
              </head>';
                $departemen  = (isset($_GET['a_departemenS']) && $_GET['a_departemenS']!='')?' ta.departemen='.$_GET['a_departemenS'].' AND ':'';
                $tahunajaran = (isset($_GET['a_tahunajaranS']) && $_GET['a_tahunajaranS']!='')?' t.tahunajaran='.$_GET['a_tahunajaranS'].' AND ':'';
                $tingkat     = (isset($_GET['a_tingkatS']) && $_GET['a_tingkatS']!='')?' k.tingkat='.$_GET['a_tingkatS'].' AND ':'';
                $nama        = isset($_GET['a_namaS'])?filter($_GET['a_namaS']):'';
                $rekening    = isset($_GET['a_rekeningS'])?filter($_GET['a_rekeningS']):'';
                $keterangan  = isset($_GET['a_keteranganS'])?filter($_GET['a_keteranganS']):'';

                $s ='SELECT
                      k.replid,
                      k.nama,
                      k.keterangan,
                      concat(r.kode," - ",r.nama)rekening,
                      SUM(n.nominal)nominal,
                      round((IF (count(*) = 1, 0, count(*) / 12)),0) jmlItem
                    FROM
                      keu_kategorianggaran k
                      LEFT JOIN aka_tingkat t ON t.replid = k.tingkat
                      LEFT JOIN aka_tahunajaran ta ON ta.replid = t.tahunajaran
                      LEFT JOIN keu_detilrekening r ON r.replid = k.rekening
                      LEFT JOIN keu_detilanggaran d ON d.kategorianggaran = k.replid
                      LEFT JOIN keu_nominalanggaran n ON n.detilanggaran = d.replid
                    WHERE
                      '.$departemen.$tahunajaran.$tingkat.'
                      k.nama LIKE "%'.$nama.'%"
                      AND (
                        r.nama LIKE "%'.$rekening.'%"
                        OR r.kode LIKE "%'.$rekening.'%"
                      )AND k.keterangan LIKE "%'.$keterangan.'%"
                    GROUP BY
                      k.replid
                    ORDER BY
                      k.replid ASC';
                $e = mysql_query($s) or die(mysql_error());
                $n = mysql_num_rows($e);
                // print_r($n);exit();
                
                $out.='<body>
                    <table width="100%">
                      <tr>
                        <td width="41%">
                          <img width="100" src="../../images/logo.png" alt="" />
                        </td>
                        <td>
                          <b>Kategori Anggaran</b>
                        </td>
                      </tr>
                </table><br />

                <table width="100%">
                  <tr>
                    <td width="15%">Departemen</td>
                    <td>: '.($departemen!=''?getDepartemen('nama',$_GET['a_departemenS']):'Semua Departemen').'</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td width="15%">Tahun Ajaran </td>
                    <td>: '.($tahunajaran!=''?getTahunAjaran('tahunajaran',$_GET['a_tahunajaranS']):'Semua Tahun Ajaran').'</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td width="15%">Tingkat </td>
                    <td>: '.($tingkat!=''?getTingkat('tingkat',$_GET['a_tingkatS']):'Semua Tingkat').'</td>
                    <td align="right"> Total :'.$n.' Data</td>
                  </tr>
                </table>';

                $out.='<table class="isi" width="100%">';
                $nox = 1;
                    if($n==0){
                      $out.='<tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                      </tr>';
                    }else{
                      while ($r=mysql_fetch_assoc($e)) {
                        $out.='<tr class="head">
                          <td align="center">Nama Kategori</td>
                          <td align="center">Rekening</td>
                          <td align="center">Tujuan</td>
                          <td align="center">Jumlah</td>
                          <td align="center">Kuota Anggaran</td>
                          <td align="center">Sisa Anggaran</td>
                        </tr>';
                        $out.='<tr>
                                  <td>'.$r['nama'].'</td>
                                  <td>'.$r['rekening'].'</td>
                                  <td>'.$r['keterangan'].'</td>
                                  <td align="right">'.$r['jmlItem'].'</td>
                                  <td align="right">Rp. '.number_format(getKatAnggaran($r['replid'],'kuotaNum')).'</td>
                                  <td align="right" >Rp. '.number_format(getKatAnggaran($r['replid'],'sisaNum')).'</td>
                            </tr>';
                          $out.='<tr>
                          <td colspan="6">';
                            $out.='<table class="isi" width="100%">
                              <tr class="head2">
                                <th width="25%">Anggaran</th>
                                <th width="25%">Nominal</th>
                                <th width="25%">Terpakai</th>
                                <th width="25%">Tujuan</th>
                              </tr>';
                            $s2 = 'SELECT 
                                    d.replid,
                                    d.nama,
                                    d.keterangan,
                                    sum(n.nominal)totNominal
                                  FROM keu_detilanggaran d
                                    LEFT JOIN keu_nominalanggaran n on n.detilanggaran = d.replid
                                  WHERE 
                                    d.kategorianggaran = '.$r['replid'].'
                                  GROUP BY  
                                    d.replid';
                            $e2 = mysql_query($s2);
                            $n2 = mysql_num_rows($e2);
                            if($n2<=0){
                              $out.='<tr><td width="100%" colspan="4" align="center">.. kosong ..</td></tr>';
                            }else{
                              while ($r2=mysql_fetch_assoc($e2)) {
                                $out.='<tr>
                                    <td width="25%">'.$r2['nama'].'</td>
                                    <td align="right" width="25%">Rp. '.number_format($r2['totNominal']).'</td>
                                    <td align="right"  width="25%">Rp. '.number_format(getDetAnggaran($r2['replid'],'terpakaiNum')).'</td>
                                    <td width="25%">'.$r2['keterangan'].'</td>
                                  </tr>';
                              }
                            }$out.='</table>';
                            $out.='</td>';
                        $nox++;
                      }
                    }
            $out.='</table>';
            // $out.='<p>Total : '.$n.'</p>';
          echo $out;
  
        #generate html -> PDF ------------
          $out2 = ob_get_contents();
          ob_end_clean(); 
          $mpdf=new mPDF('c','A4','');   
          $mpdf->SetDisplayMode('fullpage');   
          $stylesheet = file_get_contents('../../lib/mpdf/r_cetak.css');
          $mpdf->WriteHTML($stylesheet,1);  // The parameter 1 tells that this is css/style only and no body/html/text
          $mpdf->WriteHTML($out);
          $mpdf->Output();
    }
}
?>