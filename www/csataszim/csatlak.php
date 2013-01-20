<?
include('../config.php');
$mysql_csatlakozas=mysql_connect('localhost',$mysql_username,$mysql_password) or hiba(__FILE__,__LINE__,mysql_error());
$result=mysql_select_db($zanda_db_name.'_csataszim') or die();
mysql_query('set names "utf8"');
mysql_query('SET SESSION sql_mode=""');//strict mode kikapcsolasa; ahhoz tul "laza" a kod

function hiba($f,$l,$e) {
//	die();
	die('HIBA a '.$f.' file '.$l.'. sorában: '.$e);
}

function flotta_minden_frissites($melyik) {
global $verzio,$tuti_akna;
//ohsek, koordik, anyahajok, castorok, polluxok aranya
mysql_query('
update flottak f,(
select sum(if(h.id='.HAJO_TIPUS_KOORDI.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as koordi_arany
,sum(if(h.id='.HAJO_TIPUS_OHS.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as ohs_arany
,sum(if(h.id='.HAJO_TIPUS_ANYA.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as anyahajo_arany
,sum(if(h.id='.HAJO_TIPUS_CASTOR.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as castor_arany
,sum(if(h.id='.HAJO_TIPUS_POLLUX.',fh.ossz_hp*h.ar,null))/sum(fh.ossz_hp*h.ar) as pollux_arany
from flotta_hajo fh, hajok'.$verzio.' h
where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id
) t
set f.koordi_arany=round(100*t.koordi_arany)
,f.ohs_arany=round(100*t.ohs_arany)
,f.anyahajo_arany=round(100*t.anyahajo_arany)
,f.castor_arany=round(100*t.castor_arany)
,f.pollux_arany=round(100*t.pollux_arany)
where f.id='.$melyik.'
');
//effektiv pontossag (pontapasz), ohsek, koordik, anyahajok, castorok, polluxok aranya
mysql_query('update flotta_hajo fh, hajok'.$verzio.' h, flottak f
set fh.pontapasz=least(round(fh.tapasztalat/100),100)
,fh.ohs_arany=f.ohs_arany
,fh.koordi_arany=f.koordi_arany
,fh.castor_arany=f.castor_arany
,fh.pollux_arany=f.pollux_arany
,fh.tamado_ero=h.tamado_ero
,fh.valodi_hp=h.valodi_hp
where fh.flotta_id='.$melyik.' and fh.hajo_id=h.id and f.id='.$melyik);

}

function egy_kor_csata() {
global $verzio,$tuti_akna;
	//3. csata_flottamatrix
	mysql_query('truncate csata_flottamatrix') or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('insert into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csf1.csata_id,f1.id,f2.id
from csata_flotta csf1
inner join csata_flotta csf2 on csf2.csata_id=csf1.csata_id
inner join flottak f1 on f1.id=csf1.flotta_id
inner join flottak f2 on f2.id=csf2.flotta_id
left join diplomacia_statuszok dsz on dsz.ki=f1.tulaj_szov and dsz.kivel=f2.tulaj_szov
where
greatest(
if(f1.statusz='.STATUSZ_TAMAD_FLOTTAT.' and f1.tamad_flotta=f2.id,1,if(f2.statusz='.STATUSZ_TAMAD_FLOTTAT.' and f2.tamad_flotta=f1.id,1,0)),
if(f1.statusz in ('.STATUSZ_TAMAD_BOLYGOT.','.STATUSZ_RAID_BOLYGOT.') and f2.statusz='.STATUSZ_ALLOMAS.' and f1.tamad_bolygo=f2.bolygo,1,if(f2.statusz in ('.STATUSZ_TAMAD_BOLYGOT.','.STATUSZ_RAID_BOLYGOT.') and f1.statusz='.STATUSZ_ALLOMAS.' and f2.tamad_bolygo=f1.bolygo,1,0)),
if(dsz.mi='.DIPLO_HADI.',1,0)
)=1') or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('insert into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csfm.csata_id,csfm.masik_flotta_id,fu.id
from csata_flottamatrix csfm
inner join flottak fr on fr.id=csfm.egyik_flotta_id
inner join csata_flotta csfu on csfu.csata_id=csfm.csata_id
inner join flottak fu on fu.id=csfu.flotta_id and fu.id!=csfm.egyik_flotta_id and fu.id!=csfm.masik_flotta_id
left join diplomacia_statuszok dsz on dsz.ki=fr.tulaj_szov and dsz.kivel=fu.tulaj_szov
where fr.tulaj_szov=fu.tulaj_szov or dsz.mi='.DIPLO_TESTVER) or hiba(__FILE__,__LINE__,mysql_error());
	//4. csata_flotta_hajo
	mysql_query('truncate csata_flotta_hajo') or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('insert into csata_flotta_hajo (csata_id,flotta_id,hajo_id)
select csf.csata_id,csf.flotta_id,fh.hajo_id
from csata_flotta csf, flotta_hajo fh
where csf.flotta_id=fh.flotta_id') or hiba(__FILE__,__LINE__,mysql_error());
	//5. normalo_osszeg
	mysql_query('update csata_flotta_hajo tamado_csfh,(
select tamado_csfh.csata_id,tamado_csfh.flotta_id,tamado_csfh.hajo_id,sum(vedo_fh.ossz_hp*vedo_fh.valodi_hp*hh.coef*hh.coef) as uj_normalo_osszeg
from csata_flotta_hajo tamado_csfh, flotta_hajo tamado_fh, csata_flotta_hajo vedo_csfh, flotta_hajo vedo_fh, hajo_hajo'.$verzio.' hh, csata_flottamatrix csfm
where csfm.csata_id=tamado_csfh.csata_id and csfm.csata_id=vedo_csfh.csata_id
and csfm.egyik_flotta_id=tamado_csfh.flotta_id and csfm.egyik_flotta_id=tamado_fh.flotta_id
and csfm.masik_flotta_id=vedo_csfh.flotta_id and csfm.masik_flotta_id=vedo_fh.flotta_id
and tamado_csfh.hajo_id=tamado_fh.hajo_id
and vedo_csfh.hajo_id=vedo_fh.hajo_id
and tamado_fh.hajo_id=hh.hajo_id and vedo_fh.hajo_id=hh.masik_hajo_id and hh.masik_hajo_id>0
group by tamado_csfh.csata_id,tamado_csfh.flotta_id,tamado_csfh.hajo_id
) t
set tamado_csfh.normalo_osszeg=t.uj_normalo_osszeg
where tamado_csfh.csata_id=t.csata_id and tamado_csfh.flotta_id=t.flotta_id and tamado_csfh.hajo_id=t.hajo_id') or hiba(__FILE__,__LINE__,mysql_error());
	//6. sebzesek (azert kell az if greatest ossz_hp, hogy ne legyen vegtelen csata)
	mysql_query('update csata_flotta_hajo vedo_csfh,(
select vedo_csfh.csata_id,vedo_csfh.flotta_id,vedo_csfh.hajo_id,
round(sum(if(
tamado_csfh.normalo_osszeg=0
,0

,(
100
+ 10*least(tamado_fh.koordi_arany,10) + if(tamado_fh.hajo_id='.HAJO_TIPUS_POLLUX.',2.5*least(tamado_fh.castor_arany,20),0)
+ case tamado_csf.iranyito_rang
	when 2 then 10
	when 3 then 30
	when 4 then 50
	else 0
end
+ if(tamado_csf.iranyito_karrier=2,10,0)
+ case tamado_csf.iranyito_speci
	when 2 then 50
	when 4 then 70
	else 0
end 
)/100

* greatest(
100
- 5*least(vedo_fh.ohs_arany,10) - if(vedo_fh.hajo_id='.HAJO_TIPUS_CASTOR.',2.5*least(vedo_fh.pollux_arany,20),0)
- if(vedo_csf.iranyito_karrier=2,3,0)
- case vedo_csf.iranyito_speci
	when 1 then 7
	when 2 then 2
	when 4 then 7
	else 0
end
,0)/100

* sqrt(tamado_fh.moral/10000)
* if(tamado_fh.ossz_hp>0,greatest(tamado_fh.ossz_hp,3)/100,0)
/tamado_csfh.normalo_osszeg*(vedo_fh.ossz_hp*vedo_fh.valodi_hp*hh.coef*hh.coef) *
hh.coef/10 * tamado_fh.tamado_ero / vedo_fh.valodi_hp * 100 * if(tamado_fh.hajo_id='.HAJO_TIPUS_AKNA.','.($tuti_akna?'1':'if(rand()<0.1,1,0)').',1)
))) as uj_serules

from csata_flotta tamado_csf, csata_flotta_hajo tamado_csfh, flotta_hajo tamado_fh, csata_flotta vedo_csf, csata_flotta_hajo vedo_csfh, flotta_hajo vedo_fh, hajo_hajo'.$verzio.' hh, csata_flottamatrix csfm
where csfm.csata_id=tamado_csfh.csata_id and csfm.csata_id=tamado_csf.csata_id
and csfm.csata_id=vedo_csfh.csata_id and csfm.csata_id=vedo_csf.csata_id
and csfm.egyik_flotta_id=tamado_csfh.flotta_id and csfm.egyik_flotta_id=tamado_csf.flotta_id and csfm.egyik_flotta_id=tamado_fh.flotta_id
and csfm.masik_flotta_id=vedo_csfh.flotta_id and csfm.masik_flotta_id=vedo_csf.flotta_id and csfm.masik_flotta_id=vedo_fh.flotta_id
and tamado_csfh.hajo_id=tamado_fh.hajo_id
and vedo_csfh.hajo_id=vedo_fh.hajo_id
and tamado_fh.hajo_id=hh.hajo_id and vedo_fh.hajo_id=hh.masik_hajo_id and hh.masik_hajo_id>0
group by vedo_csfh.csata_id,vedo_csfh.flotta_id,vedo_csfh.hajo_id
) t
set vedo_csfh.serules=t.uj_serules
where vedo_csfh.csata_id=t.csata_id and vedo_csfh.flotta_id=t.flotta_id and vedo_csfh.hajo_id=t.hajo_id') or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('update flotta_hajo fh, csata_flotta_hajo csfh
set fh.ossz_hp=if(csfh.serules<0,fh.ossz_hp,if(csfh.serules>fh.ossz_hp,0,fh.ossz_hp-csfh.serules))
,fh.delta_hp=if(csfh.serules<0,0,if(csfh.serules>fh.ossz_hp,-fh.ossz_hp,-csfh.serules))
where fh.flotta_id=csfh.flotta_id and fh.hajo_id=csfh.hajo_id') or hiba(__FILE__,__LINE__,mysql_error());
}


define('STATUSZ_ALLOMAS',1);
define('STATUSZ_ALL',2);
define('STATUSZ_PATROL_1',3);
define('STATUSZ_PATROL_2',4);//idaig ne szamozd at!!!
define('STATUSZ_MEGY_XY',5);
define('STATUSZ_MEGY_BOLYGO',6);
define('STATUSZ_TAMAD_BOLYGORA',7);
define('STATUSZ_TAMAD_BOLYGOT',8);
define('STATUSZ_RAID_BOLYGORA',9);
define('STATUSZ_RAID_BOLYGOT',10);
define('STATUSZ_VISSZA',11);
define('STATUSZ_MEGY_FLOTTAHOZ',12);
define('STATUSZ_TAMAD_FLOTTARA',13);
define('STATUSZ_TAMAD_FLOTTAT',14);

define('DIPLO_HADI',1);
define('DIPLO_BEKE',2);
define('DIPLO_MNT',3);
define('DIPLO_TESTVER',4);

define('HAJO_TIPUS_SZONDA',206);
define('HAJO_TIPUS_KOORDI',212);
define('HAJO_TIPUS_OHS',218);
define('HAJO_TIPUS_ANYA',216);
define('HAJO_TIPUS_FULGUR',210);
define('HAJO_TIPUS_AKNA',222);
define('HAJO_TIPUS_CASTOR',223);
define('HAJO_TIPUS_POLLUX',224);

?>