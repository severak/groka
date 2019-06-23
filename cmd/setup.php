<?php

require __DIR__ . "/../config.php";
require __DIR__ . "/../lib/groonga.php";
require __DIR__ . "/../lib/utils.php";

$g = new groonga(GROONGA_URL);

if (!$g->status()) {
	die ('groonga server not available');
}

if ($g->select(['table'=>'groka'])) {
	die('table groka already exists');
}

echo 'setting up database...' . PHP_EOL;

// setup tables
if (!$g->table_create(['name'=>'groka', 'flags'=>'TABLE_HASH_KEY', 'key_type'=>'ShortText'])) {
	die('problem with creating table');
}
$g->column_create(['table'=>'groka', 'name'=>'title', 'type'=>'ShortText']);
$g->column_create(['table'=>'groka', 'name'=>'description', 'type'=>'ShortText']);
$g->column_create(['table'=>'groka', 'name'=>'text', 'type'=>'Text']);

$g->table_create(['name'=>'groka_index', 'flags'=>'TABLE_PAT_KEY', 'key_type'=>'ShortText', 'default_tokenizer'=>'TokenBigram', 'normalizer'=>'NormalizerAuto']);
$g->column_create(['table'=>'groka_index', 'name'=>'title_index', 'flags'=>'COLUMN_INDEX|WITH_POSITION', 'type'=>'groka', 'source'=>'title']);
$g->column_create(['table'=>'groka_index', 'name'=>'text_index', 'flags'=>'COLUMN_INDEX|WITH_POSITION', 'type'=>'groka', 'source'=>'text']);

/*
echo 'filling with test data...' . PHP_EOL;



// loads data
$g->load(['table'=>'groka'], ['_key'=>'https://k47.cz/ascii/porcelan.html', 'title'=>'Porcelán', 'description'=>'Každá vý­prava za hra­nice ci­vi­li­zace mě znovu pře­svědčí, že věc, která lidem chybí nej­více, je spla­cho­vací záchod. Nic jiného. Je mo­derní říkat, že lidé ne­mů­žou žít bez svých te­le­fonů & in­ter­netu', 'text'=>cleantext('Každá vý­prava za hra­nice ci­vi­li­zace mě znovu pře­svědčí, že věc, která lidem chybí nej­více, je spla­cho­vací záchod. Nic jiného. Je mo­derní říkat, že lidé ne­mů­žou žít bez svých te­le­fonů & in­ter­netu, ale bez nich se člověk bude jen nudit, nebo si bude při­pa­dat ne­in­for­mo­vaný & mimo tep světa, při­nej­hor­ším se začne bavit s živými lidmi, bez sprchy člověk smrdí, bez topení člověk smrdí kouřem z ohně a tak po­dobně. Ale všichni musí srát a nikomu se nechce dřepět v lese.

Hlav­ním spo­jo­va­cím prvkem post-apo­ka­lyp­tic­kých filmů by neměly být ko­ču­jící gangy ob­le­čené v BDSM kom­ple­tech. Konec světa by se měl poznat podle toho, že lidé serou v lesích.'
)]);


$g->load(['table'=>'groka'], ['_key'=>'https://k47.cz/ascii/uzke-hrdlo.html', 'title'=>'Úzké hrdlo', 'description'=>'Miluju, když se to na Hlav­ním Ná­draží začne srát.', 'text'=>cleantext('Miluju, když se to na Hlav­ním Ná­draží začne srát.

Ne­hledě na pověst Čes­kých Drah, vlaky vět­ši­nou jezdí podle gra­fi­konu & na čas. Přesto, někdy se sejdou vnější okol­nosti a všechno se začne hrou­tit. Ty mo­menty jsou okouz­lu­jící.

Na ta­bu­lích od­jezdů na­ska­kují zpož­dění, tlam­pače jedou non-stop, ohla­šují zpož­děné spoje, upřes­ňují ros­toucí časy do pří­jezdu, vítají vlaky, které to do­ká­zaly a na­vzdory ne­pří­zni osudu dojely na cen­t­rální ná­draží Re­pub­liky, jeden za druhým, jedno hlá­šení skončí no­tic­kou, že pro­blémy jsou způ­so­bené ne­ho­dou na trati, a hned druhé začíná, bez pauzy a bez pro­dlevy.

Stačí jeden šťastný skokan nebo str­žená trolej v úzkém hrdle, ně­ko­lik vlaků se vydá od­klo­nem a na­jed­nou se naplno pro­jeví síťový efekt, kdy to vypadá, že se po­sralo všechno, co mohlo.

Pří­pojné vlaky čekají, blo­kují ná­stu­piště, ná­draží je plné lidé a strojů a nikdo neví, co se děje. V pod­chodu mě za­sta­vila jedna žena. Ptala se: „Nevíte, co se děje?“ To byla dobrá otázka, na kterou znali od­po­věď jen vědci zkou­ma­jící teorii chaosu.

Jiný člověk říká: „Jestli chcete jet jenom do ██████████, tak teď pojede vlak z pětky.“ Ner­vo­zita stoupá.

Lidé po­stá­vají kolem in­for­mač­ních panelů a jejich tváře jsou v uha­sí­na­jí­cím dni oza­řo­vány svět­lem, které tvrdí, že náš spoj jako jediný nemá žádnou sekyru. Na jedné straně ná­stu­piště čeká zpož­děný vlak na chvíli, až do­stane ze­le­nou a může se od­klo­nem začít plazit do Brna, na druhou stranu přijel In­ter­Pan­ter. Na moment mi pro­blesklo hlavou, že se všechno zhrou­tilo, na dis­pe­činku vy­hlá­sili stanné právo a snaží se si­tu­aci uhasit za každou cenu a onen In­ter­Pan­ter je náš ná­hradní spoj. Tohle je po­slední zna­mení, rych­lí­kový vlak jako ná­hrada pro loudák, konec světa se blíží, za­ne­dlouho všichni budeme srát v lesích. Ale pak na čele vlaku blikl nápis „NO BO­AR­DING“ a bylo jasno. Tenhle není náš, jen ho pro­táhli kolem, pro­tože neměli jinou volnou hranu ná­stu­piště. Čekal jsem, že se v tom chaosu začnou ob­je­vo­vat ža­botlamy. Před­stava, že je ge­ne­ra­lita drah vy­táhne ze šrotu na jednu po­slední spa­ni­lou jízdu ko­la­bu­jí­cím ná­draží, v tu chvíli ne­pů­so­bila nijak zvláštně. Myslím, že kdyby při­jeli včas, lidé by na místě bouchli šam­paň­ské a pro­vo­lá­vali slávu soupra­vám z še­de­sá­tých let.

Náš spoj, pořád hlá­šený bez zpož­dění, začal sklouzá­val, pět minut a nejen že vlak byl v ne­do­hlednu, ale ani nebyla žádná volná kolej, kam by mohl přijet.

V davu byla cítit ner­vo­zita a napětí. Dala by se tam vy­kře­sat jiskra re­vo­luce. Tihle lidé by pod­po­řili ja­ký­koli režim, jen kdyby slí­bili, že za jejich vlády budou vlaky jezdit včas. Jistě není roz­hodně ná­ho­dou, s při­hléd­nu­tím k po­li­tické si­tu­aci v Re­pub­lice, že podle le­gendy přesně tohle za­ří­dil Benito Mus­so­lini.

+1: Jedno po­zi­ti­vum je, že jsem poprvé někoho viděl nosit mikinu ČVUT.')]);

*/

echo 'OK' . PHP_EOL;

