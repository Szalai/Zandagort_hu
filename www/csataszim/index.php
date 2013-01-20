<?
include('lang.php');
include('csatlak.php');

//$max_hajo_id=224;//emberi+zanda hajok
$max_hajo_id=218;//csak emberi hajok

$tamado_id=0;
$vedo_id=0;
$korok_szama=0;
$nyert=0;
if (!isset($_REQUEST['maxkor'])) $_REQUEST['maxkor']=1;
else {
	$_REQUEST['maxkor']=(int)$_REQUEST['maxkor'];
	if ($_REQUEST['maxkor']<1) $_REQUEST['maxkor']=1;
	if ($_REQUEST['maxkor']>100) $_REQUEST['maxkor']=100;
}

$tuti_akna=false;
if ($_REQUEST['aknak']==1) $tuti_akna=true;


$verzio='_s8';


$_REQUEST['tamado_karrier']=(int)$_REQUEST['tamado_karrier'];if ($_REQUEST['tamado_karrier']<0 || $_REQUEST['tamado_karrier']>4) $_REQUEST['tamado_karrier']=0;
$_REQUEST['vedo_karrier']=(int)$_REQUEST['vedo_karrier'];if ($_REQUEST['vedo_karrier']<0 || $_REQUEST['vedo_karrier']>4) $_REQUEST['vedo_karrier']=0;
switch($_REQUEST['tamado_karrier']) {
	case 1:
		$tamado_karrier=2;$tamado_speci=0;
	break;
	case 2:
		$tamado_karrier=2;$tamado_speci=1;
	break;
	case 3:
		$tamado_karrier=2;$tamado_speci=2;
	break;
	case 4:
		$tamado_karrier=2;$tamado_speci=4;
	break;
	default:
		$tamado_karrier=0;$tamado_speci=0;
}
switch($_REQUEST['vedo_karrier']) {
	case 1:
		$vedo_karrier=2;$vedo_speci=0;
	break;
	case 2:
		$vedo_karrier=2;$vedo_speci=1;
	break;
	case 3:
		$vedo_karrier=2;$vedo_speci=2;
	break;
	case 4:
		$vedo_karrier=2;$vedo_speci=4;
	break;
	default:
		$vedo_karrier=0;$vedo_speci=0;
}

$tamado_rang=(int)$_REQUEST['tamado_rang'];if ($tamado_rang<1 || $tamado_rang>4) $tamado_rang=1;
$vedo_rang=(int)$_REQUEST['vedo_rang'];if ($vedo_rang<1 || $vedo_rang>4) $vedo_rang=1;

if (!isset($_REQUEST['tamado_moral'])) $_REQUEST['tamado_moral']=100;
else {
	$_REQUEST['tamado_moral']=(int)$_REQUEST['tamado_moral'];
	if ($_REQUEST['tamado_moral']<0) $_REQUEST['tamado_moral']=0;
	if ($_REQUEST['tamado_moral']>100) $_REQUEST['tamado_moral']=100;
}
if (!isset($_REQUEST['vedo_moral'])) $_REQUEST['vedo_moral']=100;
else {
	$_REQUEST['vedo_moral']=(int)$_REQUEST['vedo_moral'];
	if ($_REQUEST['vedo_moral']<0) $_REQUEST['vedo_moral']=0;
	if ($_REQUEST['vedo_moral']>100) $_REQUEST['vedo_moral']=100;
}


$er=mysql_query('select * from hajok'.$verzio);
while($aux=mysql_fetch_array($er)) {
	$_REQUEST['tamado'.$aux['id']]=(int)$_REQUEST['tamado'.$aux['id']];
	if ($_REQUEST['tamado'.$aux['id']]<0) $_REQUEST['tamado'.$aux['id']]=0;
	if ($_REQUEST['tamado'.$aux['id']]>100000000) $_REQUEST['tamado'.$aux['id']]=100000000;
	$_REQUEST['vedo'.$aux['id']]=(int)$_REQUEST['vedo'.$aux['id']];
	if ($_REQUEST['vedo'.$aux['id']]<0) $_REQUEST['vedo'.$aux['id']]=0;
	if ($_REQUEST['vedo'.$aux['id']]>100000000) $_REQUEST['vedo'.$aux['id']]=100000000;
}

$tamado_moral=100*$_REQUEST['tamado_moral'];
$vedo_moral=100*$_REQUEST['vedo_moral'];

$mikor_indul=microtime(true);
if (isset($_REQUEST['szim2'])) {
	mysql_query('insert into flottak(tulaj_szov,statusz) values(1,'.STATUSZ_TAMAD_FLOTTAT.')');
	$er=mysql_query('select last_insert_id() from flottak');
	$aux=mysql_fetch_array($er);$tamado_id=$aux[0];
	mysql_query('insert into flottak(tulaj_szov,statusz) values(2,'.STATUSZ_ALL.')');
	$er=mysql_query('select last_insert_id() from flottak');
	$aux=mysql_fetch_array($er);$vedo_id=$aux[0];
	mysql_query('update flottak set tamad_flotta='.$vedo_id.' where id='.$tamado_id);
	//tamado hajok bepakolasa
	$er=mysql_query('select * from hajok'.$verzio);
	while($aux=mysql_fetch_array($er)) mysql_query('insert into flotta_hajo(flotta_id,hajo_id,ossz_hp,tapasztalat,moral) values('.$tamado_id.','.$aux['id'].','.(100*$_REQUEST['tamado'.$aux['id']]).',0,'.$tamado_moral.')');
	//vedo hajok bepakolasa
	$er=mysql_query('select * from hajok'.$verzio);
	while($aux=mysql_fetch_array($er)) mysql_query('insert into flotta_hajo(flotta_id,hajo_id,ossz_hp,tapasztalat,moral) values('.$vedo_id.','.$aux['id'].','.(100*$_REQUEST['vedo'.$aux['id']]).',0,'.$vedo_moral.')');
	//
	$er=mysql_query('select round(sum(ossz_hp/100*ar)) from flotta_hajo fh, hajok'.$verzio.' h where fh.flotta_id='.$tamado_id.' and fh.hajo_id=h.id');$aux=mysql_fetch_array($er);$tamado_ertek_0=$aux[0];
	$er=mysql_query('select round(sum(ossz_hp/100*ar)) from flotta_hajo fh, hajok'.$verzio.' h where fh.flotta_id='.$vedo_id.' and fh.hajo_id=h.id');$aux=mysql_fetch_array($er);$vedo_ertek_0=$aux[0];
	for($i=0;$i<$_REQUEST['maxkor'];$i++) {
		flotta_minden_frissites($tamado_id);
		flotta_minden_frissites($vedo_id);
		//csatak
		mysql_query('delete from csatak');
		mysql_query('insert into csatak () values()');
		$er=mysql_query('select last_insert_id() from csatak');
		$aux=mysql_fetch_array($er);$csata_id=$aux[0];
		//csata_flotta
		mysql_query('truncate csata_flotta');
		mysql_query("insert into csata_flotta (csata_id,flotta_id,tulaj,iranyito_karrier,iranyito_speci,iranyito_rang) values($csata_id,$tamado_id,1,$tamado_karrier,$tamado_speci,$tamado_rang),($csata_id,$vedo_id,1,$vedo_karrier,$vedo_speci,$vedo_rang)");
		//csata
		egy_kor_csata();
		//nyert-e valaki
		$er=mysql_query('select sum(ossz_hp) from flotta_hajo where flotta_id='.$tamado_id.' and hajo_id>0');
		$aux=mysql_fetch_array($er);$tamado_hp=$aux[0];
		$er=mysql_query('select sum(ossz_hp) from flotta_hajo where flotta_id='.$vedo_id.' and hajo_id>0');
		$aux=mysql_fetch_array($er);$vedo_hp=$aux[0];
		if ($tamado_hp>0 && $vedo_hp==0) $nyert=1;
		if ($tamado_hp==0 && $vedo_hp>0) $nyert=2;
		if ($tamado_hp==0) break;
		if ($vedo_hp==0) break;
	}
	$korok_szama=$i;
	if ($tamado_hp==0 || $vedo_hp==0) $korok_szama++;
	//
	$er=mysql_query('select round(sum(ossz_hp/100*ar)) from flotta_hajo fh, hajok'.$verzio.' h where fh.flotta_id='.$tamado_id.' and fh.hajo_id=h.id');$aux=mysql_fetch_array($er);$tamado_ertek_1=$aux[0];
	$er=mysql_query('select round(sum(ossz_hp/100*ar)) from flotta_hajo fh, hajok'.$verzio.' h where fh.flotta_id='.$vedo_id.' and fh.hajo_id=h.id');$aux=mysql_fetch_array($er);$vedo_ertek_1=$aux[0];
	//
	$er=mysql_query('select h.id,fh.ossz_hp,fh.normalo_osszeg from hajok'.$verzio.' h left join flotta_hajo fh on fh.hajo_id=h.id where fh.flotta_id='.$tamado_id.' or fh.flotta_id is null');
	while($aux=mysql_fetch_array($er)) {
		$tamado_flotta[$aux[0]]=$aux[1];
		$tamado_flotta_norm[$aux[0]]=$aux[2];
	}
	$er=mysql_query('select h.id,fh.ossz_hp from hajok'.$verzio.' h left join flotta_hajo fh on fh.hajo_id=h.id where fh.flotta_id='.$vedo_id.' or fh.flotta_id is null');
	while($aux=mysql_fetch_array($er)) $vedo_flotta[$aux[0]]=$aux[1];
}
$mikor_vegzodik=microtime(true);


$zanda_menu='battlesim_s8';
$index_header_title=$lang[$lang_lang]['battlesim/index.php']['Zandagort Csataszimulátor'];
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<? if ($lang_lang=='hu') echo 'hu';else echo 'en';?>" lang="<? if ($lang_lang=='hu') echo 'hu';else echo 'en';?>">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?=$index_header_title;?></title>
<meta http-equiv="Content-Language" content="<? if ($lang_lang=='hu') echo 'hu';else echo 'en';?>" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
</head>
<body>
<div style="width: 800px; margin: 0 auto">

<form action="." method="post">

<h1><?=$lang[$lang_lang]['battlesim/index.php']['Csataszimulátor']?> s8</h1>

<table style="width: 100%">
<tr>
	<td></td>
	<td style="text-align: center" colspan="3">
		<h1<? if ($nyert==1) echo ' style="color:green"';if ($nyert==2) echo ' style="color:red"';?>><?=$lang[$lang_lang]['battlesim/index.php']['EGYIK']?></h1>
	</td>
	<td style="text-align: center" colspan="3">
		<h1<? if ($nyert==2) echo ' style="color:green"';if ($nyert==1) echo ' style="color:red"';?>><?=$lang[$lang_lang]['battlesim/index.php']['MÁSIK']?></h1>
	</td>
</tr>
<?
$er=mysql_query('select h.*,l.kep from hajok'.$verzio.' h, leirasok'.$verzio.' l where l.domen=2 and l.id=h.id and h.id<='.$max_hajo_id.' order by h.id') or hiba(__FILE__,__LINE__,mysql_error());
$sorszam=0;while($aux=mysql_fetch_array($er)) {$sorszam++;
?>
<tr<? if ($sorszam%2==1) echo ' style="background: rgba(255,255,255,0.1)"';?>>

<td><? if (strlen($aux['kep'])) { ?><a href="http://zandagort.<?=($lang_lang=='en'?'com':'hu')?>/wiki/<?=$aux['nev'.$lang__lang];?>" style="cursor: help" onclick="window.open('http://zandagort.<?=($lang_lang=='en'?'com':'hu')?>/wiki/<?=$aux['nev'.$lang__lang];?>','help_ablak','width=1000,height=650,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0');return false" title="<?=$aux['nev'.$lang__lang];?>"><img src="img/<?=$aux['kep'];?>_index.png" style="vertical-align: -11px" alt="<?=$aux['nev'.$lang__lang];?>" /></a><? } ?> <?=$aux['nev'.$lang__lang];?></td>

<td><input type="text" class="inputtext" name="tamado<?=$aux['id'];?>" value="<?=$_REQUEST['tamado'.$aux['id']];?>" /></td>
<td>&rarr;</td>
<td><? if ($tamado_flotta[$aux['id']]) echo number_format($tamado_flotta[$aux['id']]/100,2,$lang[$lang_lang]['battlesim/index.php'][','],' ').'<br />('.number_format($tamado_flotta[$aux['id']]/$_REQUEST['tamado'.$aux['id']],1,$lang[$lang_lang]['battlesim/index.php'][','],' ').'%)';else echo '';?></td>

<td><input type="text" class="inputtext" name="vedo<?=$aux['id'];?>" value="<?=$_REQUEST['vedo'.$aux['id']];?>" /></td>
<td>&rarr;</td>
<td><? if ($vedo_flotta[$aux['id']]) echo number_format($vedo_flotta[$aux['id']]/100,2,$lang[$lang_lang]['battlesim/index.php'][','],' ').'<br />('.number_format($vedo_flotta[$aux['id']]/$_REQUEST['vedo'.$aux['id']],1,$lang[$lang_lang]['battlesim/index.php'][','],' ').'%)';else echo '';?></td>

</tr>
<? } ?>
<tr style="background: rgba(255,255,255,0.1);font-weight: bold">
	<td><?=$lang[$lang_lang]['battlesim/index.php']['Teljes egyenérték']?></td>
	<td><?=number_format($tamado_ertek_0/100,2,$lang[$lang_lang]['battlesim/index.php'][','],' ');?></td>
	<td>&rarr;</td>
	<td><?=number_format($tamado_ertek_1/100,2,$lang[$lang_lang]['battlesim/index.php'][','],' ');?><? if ($tamado_ertek_0>0) echo '<br />('.number_format($tamado_ertek_1/$tamado_ertek_0*100,1,$lang[$lang_lang]['battlesim/index.php'][','],' ').'%)';?></td>
	<td><?=number_format($vedo_ertek_0/100,2,$lang[$lang_lang]['battlesim/index.php'][','],' ');?></td>
	<td>&rarr;</td>
	<td><?=number_format($vedo_ertek_1/100,2,$lang[$lang_lang]['battlesim/index.php'][','],' ');?><? if ($tamado_ertek_0>0) echo '<br />('.number_format($vedo_ertek_1/$vedo_ertek_0*100,1,$lang[$lang_lang]['battlesim/index.php'][','],' ').'%)';?></td>
</tr>
<tr>
	<td><?=$lang[$lang_lang]['battlesim/index.php']['Morál']?></td>
	<td colspan="3"><input type="text" class="inputtext" name="tamado_moral" value="<?=$_REQUEST['tamado_moral'];?>" /></td>
	<td colspan="3"><input type="text" class="inputtext" name="vedo_moral" value="<?=$_REQUEST['vedo_moral'];?>" /></td>
</tr>
<tr>
	<td><?=$lang[$lang_lang]['battlesim/index.php']['Karrier']?></td>
	<td colspan="3"><select class="inputtext_now" name="tamado_karrier">
		<option value="0"<? if ($_REQUEST['tamado_karrier']==0) echo ' selected="selected"'; ?>>-</option>
		<option value="1"<? if ($_REQUEST['tamado_karrier']==1) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs1']?></option>
		<option value="2"<? if ($_REQUEST['tamado_karrier']==2) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs2']?></option>
		<option value="3"<? if ($_REQUEST['tamado_karrier']==3) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs3']?></option>
		<option value="4"<? if ($_REQUEST['tamado_karrier']==4) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs4']?></option>
	</select></td>
	<td colspan="3"><select class="inputtext_now" name="vedo_karrier">
		<option value="0"<? if ($_REQUEST['vedo_karrier']==0) echo ' selected="selected"'; ?>>-</option>
		<option value="1"<? if ($_REQUEST['vedo_karrier']==1) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs1']?></option>
		<option value="2"<? if ($_REQUEST['vedo_karrier']==2) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs2']?></option>
		<option value="3"<? if ($_REQUEST['vedo_karrier']==3) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs3']?></option>
		<option value="4"<? if ($_REQUEST['vedo_karrier']==4) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['karrier_cs4']?></option>
	</select></td>
</tr>
<tr>
	<td><?=$lang[$lang_lang]['battlesim/index.php']['Rang']?></td>
	<td colspan="3"><select class="inputtext_now" name="tamado_rang">
		<option value="1"<? if ($_REQUEST['tamado_rang']==1) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_1']?></option>
		<option value="2"<? if ($_REQUEST['tamado_rang']==2) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_2']?></option>
		<option value="3"<? if ($_REQUEST['tamado_rang']==3) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_3']?></option>
		<option value="4"<? if ($_REQUEST['tamado_rang']==4) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_4']?></option>
	</select></td>
	<td colspan="3"><select class="inputtext_now" name="vedo_rang">
		<option value="1"<? if ($_REQUEST['vedo_rang']==1) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_1']?></option>
		<option value="2"<? if ($_REQUEST['vedo_rang']==2) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_2']?></option>
		<option value="3"<? if ($_REQUEST['vedo_rang']==3) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_3']?></option>
		<option value="4"<? if ($_REQUEST['vedo_rang']==4) echo ' selected="selected"'; ?>><?=$lang[$lang_lang]['battlesim/index.php']['rang_4']?></option>
	</select></td>
</tr>
<tr>
	<td><?=$lang[$lang_lang]['battlesim/index.php']['Körök száma']?></td>
	<td colspan="6"><input type="text" class="inputtext" name="maxkor" value="<?=$_REQUEST['maxkor'];?>" /></td>
</tr>
<tr>
	<td><?=$lang[$lang_lang]['battlesim/index.php']['Aknák']?></td>
	<td colspan="6"><select class="inputtext" name="aknak">
		<option value="0"<? if (!$tuti_akna) echo ' selected="selected"';?>><?=$lang[$lang_lang]['battlesim/index.php']['pontatlan']?></option>
		<option value="1"<? if ($tuti_akna) echo ' selected="selected"';?>><?=$lang[$lang_lang]['battlesim/index.php']['mindig pontos']?></option>
	</select></td>
</tr>

</table>

<input type="hidden" name="szim2" value="1" />
<p style="text-align: center"><input type="submit" name="szim" value="HARC" /></p>

<? if ($tamado_id) { ?>
<p><?
if ($lang_lang=='en') {
	echo 'Result: ';
	if ($nyert==1) echo 'fleet A has won';
	elseif ($nyert==2) echo 'fleet B has won';
	else echo 'draw';
	echo ' in ';
	echo $korok_szama;
	echo ' round';
	if ($korok_szama>1) echo 's';
} else {
	echo 'Eredmény: ';
	echo $korok_szama;
	echo ' kör alatt ';
	if ($nyert==1) echo 'győzött az EGYIK';
	elseif ($nyert==2) echo 'győzött a MÁSIK';
	else echo 'döntetlen született';
}
echo '.';
?></p>
<? } ?>

</form>

<p><br /></p>
<? if($lang_lang=='hu') { ?>
<p>Leírás:</p>
<ol class="normal_szamozott_lista">
<li>Add meg, hogy hány hajó van az egyes típusokból a két flottában.</li>
<li>Add meg a flották morálját.</li>
<li>Add meg az flották irányítóinak karrierjét és rangját.</li>
<li>Add meg, hogy legfeljebb hány kör csata fusson le.</li>
<li>Kattints a HARC gombra.</li>
</ol>
<p>Az eredmény, amit megad a szimulátor: a megmaradó hajók száma (és százaléka), a flották teljes egyenértéke a csata előtt és után, és a csata végkimenetele.</p>
<? } else { ?>
<p>How to use:</p>
<ol class="normal_szamozott_lista">
<li>Enter the initial number of ships of different types in each fleet.</li>
<li>Enter the morale of each fleet.</li>
<li>Enter the career and rank of the commanders of each fleet.</li>
<li>Enter the maximum number of rounds of battle.</li>
<li>Click on FIGHT.</li>
</ol>
<p>What you get is the number (and percentage) of remaining ships, the initial and remaining total equivalent of the fleets, and the result of the battle.</p>
<? } ?>
<p><?=$lang[$lang_lang]['battlesim/index.php']['A csaták logikájáról <a href="http://zandagort.hu/wiki/Csatak%C3%A9plet">itt</a> olvashatsz.']?></p>
<p><br /></p>

<? include('csataszim_tablazat.php');?>

<p><br /></p>

</div>
</body>
</html>
<?
mysql_query('delete from flottak where id='.$tamado_id.' or id='.$vedo_id) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from flotta_hajo where flotta_id='.$tamado_id.' or flotta_id='.$vedo_id) or hiba(__FILE__,__LINE__,mysql_error());
mysql_close($mysql_csatlakozas);
?>
