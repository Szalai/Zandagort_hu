<?
$lang_lang='hu';$lang__lang='';

$lang['en']=array(
'battlesim/index.php'=>array(
	'Zandagort Csataszimulátor'=>'Zandagort Battle Simulator',
	'Csataszimulátor'=>'Battle Simulator',
	'EGYIK'=>'Fleet A',
	'MÁSIK'=>'Fleet B',
	'Teljes egyenérték'=>'Total equivalent',
	'Morál'=>'Morale',
	'Tapasztalat'=>'Experience',
	'Karrier'=>'Career',
	'Rang'=>'Rank',
	'rang_1'=>'Cadet',
	'rang_2'=>'Regular',
	'rang_3'=>'Veteran',
	'rang_4'=>'Elite',
	'karrier_cs1'=>'Warlord',
	'karrier_cs2'=>'Guardian',
	'karrier_cs3'=>'Fleet Marshal',
	'karrier_cs4'=>'Zealot',
	','=>'.',
	'Körök száma'=>'Number of rounds',
	'Aknák'=>'Mines',
	'pontatlan'=>'inaccurate',
	'mindig pontos'=>'always accurate',
	'A csaták logikájáról <a href="http://zandagort.hu/wiki/Csatak%C3%A9plet">itt</a> olvashatsz.'=>'You can read about the logic of battles <a href="http://zandagort.com/wiki/Battle_formula">here</a>.',
	'Érték'=>'Equiv',
	'TE'=>'AV',
	'VE'=>'DV',
	'Ezek ellen jó'=>'Good vs',
	'Ezek jók ellene'=>'Bad vs',
	'Pont'=>'Prec',
	'Seb'=>'Spd',
	'Lát'=>'Vis',
	'Rejt'=>'Stlth',
	'Akna spec'=>'',
	'Castor spec'=>'',
	'Pollux spec'=>'',
	'Akna spec'=>'Mine is pretty inaccurate, only about 10% of the shots hit the target.',
	'Castor spec'=>'Pollux strengthens the shield of Castor increasing its HP.',
	'Pollux spec'=>'Castor strengthens Pollux increasing its attack value.',
),
);

//kivetelek felulirasa:
$lang['hu']=array(
'battlesim/index.php'=>array(
	'győzött a támadó prefix'=>'',
	'győzött a védő prefix'=>'',
	'döntetlen született prefix'=>'',
	'győzött a támadó postfix'=>' kör alatt győzött a támadó',
	'győzött a védő postfix'=>' kör alatt győzött a védő',
	'döntetlen született postfix'=>' kör alatt döntetlen született',
	'Akna spec'=>'Az Akna meglehetősen pontatlan, ezért a lövések kb 10%-a talál célba.',
	'Castor spec'=>'A Pollux erősíti a Castor pajzsát, vagyis növeli a HP-ját.',
	'Pollux spec'=>'A Castor erősíti a Pollux-ot, vagyis növeli a támadóerejét.',
),
);

//maradek $lang['hu'] generalasa kulcsok alapjan
foreach($lang['en'] as $hol=>$mi) {
	foreach($mi as $kulcs=>$ertek) {
		if (!isset($lang['hu'][$hol][$kulcs])) $lang['hu'][$hol][$kulcs]=$kulcs;
	}
}

$lang['hu']['battlesim/index.php']['rang_1']='Kadét';
$lang['hu']['battlesim/index.php']['rang_2']='Briganti';
$lang['hu']['battlesim/index.php']['rang_3']='Veterán';
$lang['hu']['battlesim/index.php']['rang_4']='Elit';

$lang['hu']['battlesim/index.php']['karrier_cs1']='Hadúr';
$lang['hu']['battlesim/index.php']['karrier_cs2']='Örző';
$lang['hu']['battlesim/index.php']['karrier_cs3']='Flotta marsall';
$lang['hu']['battlesim/index.php']['karrier_cs4']='Zélóta';


?>