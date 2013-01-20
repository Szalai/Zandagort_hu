<table>
<tr>
<th></th>
<th></th>
<th></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Érték']?></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['TE']?></th>
<th>HP</th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Ezek ellen jó']?></th>
<th>Ezek ellen rossz</th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Seb']?></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Lát']?></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Rejt']?></th>
</tr>
<?
$rovid_nevek['hu']=array('KC','Sk','Fr','IC','Csh','Szo','Ci','Va','Kv','Fu','DN','Koo','NCi','Rap','Ro','Anya','Neme','OHS','Behe','Zeu','Szú','Akna','Cas','Pol');
$rovid_nevek['en']=array('LCr','Sc','Fri','IC','BSh','Prb','Cr','Fig','Cv','Fu','DN','Coo','HCr','Rap','Dst','Car','Neme','OHS','Behe','Zeu','Mosq','Mine','Cas','Pol');
$alaposztaly_rovid_nevek['hu']=array('','Ci','Va','Ro','IC','CSH');
$alaposztaly_rovid_nevek['en']=array('','Cr','Fig','Dst','IC','BSh');

$er=mysql_query('select h.*,l.kep from hajok'.$verzio.' h, leirasok'.$verzio.' l where l.domen=2 and l.id=h.id and h.id<='.$max_hajo_id.' order by h.id') or hiba(__FILE__,__LINE__,mysql_error());
$sorszam=0;while($aux=mysql_fetch_array($er)) {$sorszam++;

$hajok_adatai[$aux['id']]=$aux;

$affin_p='';
$affin_n='';
$er2=mysql_query('select hh.*,h.nev'.$lang__lang.',h.ar,h.id from hajo_hajo'.$verzio.' hh left join hajok'.$verzio.' h on hh.masik_hajo_id=h.id where hh.hajo_id='.$aux['id'].' and (h.id<='.$max_hajo_id.' or h.id is null) order by hh.masik_hajo_id') or hiba(__FILE__,__LINE__,mysql_error());
while($aux2=mysql_fetch_array($er2)) {
	if ($aux2['masik_hajo_id']!=$aux['id']) {
		if ($aux2['coef']>10) $affin_p.=$rovid_nevek[$lang_lang][$aux2['id']-201].', ';
		elseif ($aux2['coef']<10) $affin_n.=$rovid_nevek[$lang_lang][$aux2['id']-201].', ';
	}
}
$affin_p=substr($affin_p,0,-2);
$affin_n=substr($affin_n,0,-2);
?>
<tr<? if ($sorszam%2==1) echo ' style="background: rgba(255,255,255,0.1)"';?>>
<th style="text-align: left"><?=$alaposztaly_rovid_nevek[$lang_lang][$aux['alaposztaly']];?></th>
<th style="text-align: left"><?=$rovid_nevek[$lang_lang][$aux['id']-201];?></th>
<td><a href="http://zandagort.<?=($lang_lang=='en'?'com':'hu')?>/wiki/<?=$aux['nev'.$lang__lang];?>" style="cursor: help" onclick="window.open('http://zandagort.<?=($lang_lang=='en'?'com':'hu')?>/wiki/<?=$aux['nev'.$lang__lang];?>','help_ablak','width=1000,height=650,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0');return false" title="<?=$aux['nev'.$lang__lang];?>"><img src="img/<?=$aux['kep'];?>_index.png" alt="<?=$aux['nev'.$lang__lang];?>" /></a></td>
<td><?=number_format($aux['ar']/100,2,$lang[$lang_lang]['battlesim/index.php'][','],'');?></td>
<td><?=$aux['tamado_ero'];?><? if ($aux['id']==222) echo '*';if ($aux['id']==224) echo '***';?></td>
<td><?=$aux['valodi_hp'];?><? if ($aux['id']==223) echo '**';?></td>
<td><?
if ($aux['id']==206 || $aux['id']==212 || $aux['id']==218) echo '-';else {
switch($aux['alaposztaly']) {
	case 1: echo $alaposztaly_rovid_nevek[$lang_lang][3].', '.$alaposztaly_rovid_nevek[$lang_lang][4];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][221-201];break;
	case 2: echo $alaposztaly_rovid_nevek[$lang_lang][1].', '.$alaposztaly_rovid_nevek[$lang_lang][5];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][224-201];break;
	case 3: echo $alaposztaly_rovid_nevek[$lang_lang][2].', '.$alaposztaly_rovid_nevek[$lang_lang][4];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][222-201];break;
	case 4: echo $alaposztaly_rovid_nevek[$lang_lang][2].', '.$alaposztaly_rovid_nevek[$lang_lang][5];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][223-201];break;
	case 5: echo $alaposztaly_rovid_nevek[$lang_lang][1].', '.$alaposztaly_rovid_nevek[$lang_lang][3];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][219-201];break;
	case 6: echo $alaposztaly_rovid_nevek[$lang_lang][1];break;
	case 7: echo '-';break;
	case 8: echo $alaposztaly_rovid_nevek[$lang_lang][2];break;
	case 9: echo $alaposztaly_rovid_nevek[$lang_lang][5];break;
	case 10: echo $alaposztaly_rovid_nevek[$lang_lang][3];break;
	case 11: echo $alaposztaly_rovid_nevek[$lang_lang][4];break;
}
}
?></td>
<td><?
if ($aux['id']==206 || $aux['id']==212 || $aux['id']==218) echo '-';else {
switch($aux['alaposztaly']) {
	case 1: echo $alaposztaly_rovid_nevek[$lang_lang][2].', '.$alaposztaly_rovid_nevek[$lang_lang][5];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][219-201];break;
	case 2: echo $alaposztaly_rovid_nevek[$lang_lang][3].', '.$alaposztaly_rovid_nevek[$lang_lang][4];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][221-201];break;
	case 3: echo $alaposztaly_rovid_nevek[$lang_lang][1].', '.$alaposztaly_rovid_nevek[$lang_lang][5];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][223-201];break;
	case 4: echo $alaposztaly_rovid_nevek[$lang_lang][1].', '.$alaposztaly_rovid_nevek[$lang_lang][3];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][224-201];break;
	case 5: echo $alaposztaly_rovid_nevek[$lang_lang][2].', '.$alaposztaly_rovid_nevek[$lang_lang][4];if ($max_hajo_id>218) echo ', '.$rovid_nevek[$lang_lang][222-201];break;
	case 6: echo $alaposztaly_rovid_nevek[$lang_lang][5];break;
	case 7: echo '-';break;
	case 8: echo $alaposztaly_rovid_nevek[$lang_lang][1];break;
	case 9: echo $alaposztaly_rovid_nevek[$lang_lang][3];break;
	case 10: echo $alaposztaly_rovid_nevek[$lang_lang][4];break;
	case 11: echo $alaposztaly_rovid_nevek[$lang_lang][2];break;
}
}
?></td>
<?/*?>
<td><?=$affin_p;?></td>
<td><?=$affin_n;?></td>
<?*/?>
<td><?=round($aux['sebesseg']/2);?></td>
<td><?=$aux['latotav'];?></td>
<td><?=$aux['rejtozes'];?></td>
</tr>
<? } ?>
<tr style="background: rgba(255,255,255,0.1)">
<th></th>
<th></th>
<th></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Érték']?></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['TE']?></th>
<th>HP</th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Ezek ellen jó']?></th>
<th>Ezek ellen rossz</th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Seb']?></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Lát']?></th>
<th><?=$lang[$lang_lang]['battlesim/index.php']['Rejt']?></th>
</tr>
</table>

<? if ($max_hajo_id>218) { ?>
<ul style="margin-top: 10px; font-size: 10pt">
<li>* <?=$lang[$lang_lang]['battlesim/index.php']['Akna spec']?></li>
<li>** <?=$lang[$lang_lang]['battlesim/index.php']['Castor spec']?></li>
<li>*** <?=$lang[$lang_lang]['battlesim/index.php']['Pollux spec']?></li>
</ul>
<? } ?>
