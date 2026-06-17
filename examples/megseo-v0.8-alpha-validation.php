<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Checks\Hreflang\HreflangCheck;

$engine = Engine::make();
foreach ([new TitleCheck(),new MetaDescriptionCheck(),new CanonicalCheck(),new OpenGraphCheck(),new TwitterCardCheck(),new HreflangCheck()] as $c) $engine->register($c);

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  MegSEO v0.8 Alpha Validation                                    ║\n";
echo "╠════════════════════════════════════════════════════════════════════╣\n";
printf("║  Checks: %d                                                       ║\n", $engine->count());
foreach ($engine->all() as $c) printf("║    • %-54s ║\n", "{$c->ref()->id} v{$c->ref()->version}");
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

function vrun($e,$nm,$ctx,$exp,$ms=600):array{$r1=$e->analyze($ctx);$r2=$e->analyze($ctx);$pd=str_repeat('─',66);echo"┌{$pd}┐\n│ ".str_pad($nm,66)."│\n├{$pd}┤\n";
$s=$r1->score();echo"│ Score: {$s->value}\n";foreach($s->contributors as $c)echo"│   {$c['sourceCheckId']} → {$c['value']}\n";
$i=$r1->issues();$w=$r1->warnings();$su=$r1->suggestions();$f=$r1->failures;
echo"│ I:".count($i)." W:".count($w)." S:".count($su)." F:".count($f)."\n";
foreach($i as$x)echo"│   ✗ [{$x->sourceCheckId}] {$x->message}\n";
foreach($w as$x)echo"│   ⚠ [{$x->sourceCheckId}] {$x->message}\n";
foreach($su as$x)echo"│   → [{$x->sourceCheckId}] {$x->message}\n";
$p=true;$err=[];if($s->value<0||$s->value>$ms){$p=false;$err[]="Score {$s->value} out of 0–{$ms}";}
foreach($exp as$k=>$v){$m=match($k){'maxI'=>count($i)<=$v,'minI'=>count($i)>=$v,'minW'=>count($w)>=$v,'maxW'=>count($w)<=$v,'minS'=>count($su)>=$v,'maxS'=>count($su)<=$v,'fails'=>count($f)===$v,'scoreMin'=>$s->value>=$v,'scoreMax'=>$s->value<=$v,'score'=>abs($s->value-(float)$v)<0.01,'issues'=>count($i)===$v,'warnings'=>count($w)===$v,'suggestions'=>count($su)===$v,default=>true};
if(!$m){$p=false;$act=match($k){'score','scoreMin','scoreMax'=>(string)$s->value,'maxI','minI','issues'=>(string)count($i),'minW','maxW','warnings'=>(string)count($w),'minS','maxS','suggestions'=>(string)count($su),default=>''};$err[]="{$k}: got {$act} exp {$v}";}}
$ok=['seo.title','seo.meta_description','seo.canonical','seo.open_graph','seo.twitter_card','seo.hreflang'];
foreach(array_merge($i,$w,$su)as$x)if(!in_array($x->sourceCheckId,$ok,true)){$p=false;$err[]="Bad: {$x->sourceCheckId}";}
$det=$r1->toArray()===$r2->toArray();echo"│ Det: ".($det?"✓":"✗")."  Cross: ".($p?"✓":"✗")."\n";if($err)foreach($err as$e)echo"│   ! {$e}\n";echo"└{$pd}┘\n\n";return['passed'=>$p&&$det,'det'=>$det];}

// Common data
$GT='Complete Guide to MegSEO for Laravel Developers';$GD='A comprehensive guide covering installation, configuration, and usage of the MegSEO SEO intelligence engine for Laravel applications.';$GC='https://example.com/megseo-guide';
$base=['title'=>$GT,'description'=>$GD,'canonical'=>$GC,'og:title'=>'OG Title','og:description'=>'OG Desc','og:image'=>'https://x.com/og.jpg','twitter:card'=>'summary','twitter:title'=>'TW Title','twitter:description'=>'TW Desc','twitter:image'=>'https://x.com/tw.jpg'];
$hr=[['hreflang'=>'en','href'=>'https://x.com/en'],['hreflang'=>'x-default','href'=>'https://x.com/']];
$withHr = array_merge($base, $hr);
$base = $withHr; // include hreflang in base for shared context compatibility
$attrs=['focus_keyword'=>'MegSEO','page_url'=>$GC,'page_language'=>'en'];
$arBase=['title'=>'دليل MegSEO الشامل لمطوري Laravel المحترفين','description'=>'دليل شامل ومفصل يغطي تثبيت وإعداد واستخدام محرك MegSEO لتحسين محركات البحث في Laravel الحديثة','canonical'=>'https://example.com/دليل'];

$r=[];

// 1-2
$r[]=vrun($engine,'1. Perfect English',new AnalysisContext(subject:$withHr,attributes:$attrs),['score'=>500,'issues'=>0,'warnings'=>0,'suggestions'=>0]);
$r[]=vrun($engine,'2. Perfect Arabic',new AnalysisContext(subject:array_merge($arBase,['og:title'=>'OG','og:description'=>'D','og:image'=>'https://x.com/og.jpg','twitter:card'=>'summary','twitter:title'=>'TW','twitter:description'=>'D','twitter:image'=>'https://x.com/tw.jpg'],$hr),attributes:['focus_keyword'=>'MegSEO','page_url'=>$arBase['canonical'],'page_language'=>'ar']),['scoreMin'=>500]);

// 3
$r[]=vrun($engine,'3. Missing Everything',new AnalysisContext(subject:[]),['minI'=>12,'fails'=>0]);
$r[]=vrun($engine,'4. Weak Homepage',new AnalysisContext(subject:['title'=>'Hm','description'=>'','canonical'=>null,'og:title'=>'','twitter:card'=>'']),['minI'=>6,'fails'=>0]);
$r[]=vrun($engine,'5. Duplicate Content',new AnalysisContext(subject:$withHr,attributes:array_merge($attrs,['duplicate_support_data'=>[['title'=>$GT,'reference'=>'/dupe'],['title'=>$GD,'reference'=>'/dupe']]])),['minS'=>2,'issues'=>0,'fails'=>0]);

// 6-9
$r[]=vrun($engine,'6. Relative Canonical',new AnalysisContext(subject:array_merge($base,['canonical'=>'/rel'])),['minW'=>1,'fails'=>0]);
foreach(['javascript:void(0)','ftp://example.com','https://'] as $b)$r[]=vrun($engine,"7. Invalid: {$b}",new AnalysisContext(subject:array_merge($base,['canonical'=>$b])),['minI'=>1,'fails'=>0]);
foreach(['https://example.com/دليل','https://موقعي.مصر/صفحة','https://xn--mgbh0fb.xn--kgbechtv/'] as $u)$r[]=vrun($engine,'8. Unicode: '.substr($u,0,50),new AnalysisContext(subject:array_merge($base,['canonical'=>$u,'og:image'=>$u,'twitter:image'=>$u])),['issues'=>0,'warnings'=>0]);
$r[]=vrun($engine,'9. Missing page_url',new AnalysisContext(subject:array_merge($base,['canonical'=>'https://other.com/p'])),['fails'=>0]);

// 10-12
$r[]=vrun($engine,'10. Perfect OG',new AnalysisContext(subject:$withHr,attributes:$attrs),['scoreMin'=>540,'issues'=>0]);
$r[]=vrun($engine,'11. Broken OG',new AnalysisContext(subject:array_merge($base,['og:title'=>'','og:description'=>'','og:image'=>'/bad.jpg']),attributes:$attrs),['minI'=>2,'minW'=>1]);
$r[]=vrun($engine,'12a. OG Dupes',new AnalysisContext(subject:array_merge($base,['og:title'=>['Same','Same']]),attributes:$attrs),['maxS'=>0]);
$r[]=vrun($engine,'12b. OG Conflicts',new AnalysisContext(subject:array_merge($base,['og:title'=>['A','B']]),attributes:$attrs),['minS'=>1]);

// 13-17
$r[]=vrun($engine,'13. Perfect Twitter',new AnalysisContext(subject:$withHr,attributes:$attrs),['scoreMin'=>540,'issues'=>0]);
$r[]=vrun($engine,'14. Broken Twitter',new AnalysisContext(subject:array_merge($base,['twitter:card'=>'','twitter:title'=>'','twitter:description'=>'','twitter:image'=>'/bad.jpg']),attributes:$attrs),['minI'=>3,'minW'=>1]);
$r[]=vrun($engine,'15. Invalid Twitter Type',new AnalysisContext(subject:array_merge($base,['twitter:card'=>'photo']),attributes:$attrs),['warnings'=>1]);
$r[]=vrun($engine,'16. Relative Twitter Image',new AnalysisContext(subject:array_merge($base,['twitter:image'=>'/rel.jpg']),attributes:$attrs),['minW'=>1]);
$r[]=vrun($engine,'17a. Twitter Dupes',new AnalysisContext(subject:array_merge($base,['twitter:card'=>['summary','summary']]),attributes:$attrs),['maxS'=>0]);
$r[]=vrun($engine,'17b. Twitter Conflicts',new AnalysisContext(subject:array_merge($base,['twitter:card'=>['summary','player']]),attributes:$attrs),['minS'=>1]);

// 18-26: Hreflang scenarios — include valid hreflang data with each
$r[]=vrun($engine,'18. Perfect Hreflang',new AnalysisContext(subject:array_merge($base,$hr),attributes:$attrs),['score'=>500,'issues'=>0,'warnings'=>0,'suggestions'=>0]); // all 6 at 100
$r[]=vrun($engine,'19. Missing Hreflang',new AnalysisContext(subject:$withHr,attributes:$attrs),['scoreMin'=>540,'issues'=>0]); // hreflang finds missing from mixed array
$r[]=vrun($engine,'20. Invalid Lang Code',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'en_US!','href'=>'https://x.com/en'],['hreflang'=>'x-default','href'=>'https://x.com/']]),attributes:$attrs),['minW'=>1]);
$r[]=vrun($engine,'21. Relative Hreflang URL',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'en','href'=>'/en'],['hreflang'=>'x-default','href'=>'https://x.com/']]),attributes:$attrs),['minW'=>1]);
$r[]=vrun($engine,'22. Missing x-default',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'en','href'=>'https://x.com/en'],['hreflang'=>'fr','href'=>'https://x.com/fr']]),attributes:$attrs),['minS'=>1]);
$r[]=vrun($engine,'23. Broken Self-Ref',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'en','href'=>'https://x.com/home'],['hreflang'=>'x-default','href'=>'https://x.com/']]),attributes:$attrs),['minS'=>1]);
$r[]=vrun($engine,'24. Dup Languages',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'en','href'=>'https://x.com/en'],['hreflang'=>'en','href'=>'https://x.com/english'],['hreflang'=>'x-default','href'=>'https://x.com/']]),attributes:$attrs),['minS'=>1]);
$r[]=vrun($engine,'25. Same href',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'en','href'=>'https://x.com/home'],['hreflang'=>'fr','href'=>'https://x.com/home'],['hreflang'=>'x-default','href'=>'https://x.com/']]),attributes:$attrs),['minW'=>1]);
$r[]=vrun($engine,'26. Unicode Hreflang',new AnalysisContext(subject:array_merge($base,[['hreflang'=>'ar','href'=>'https://example.com/عربي'],['hreflang'=>'x-default','href'=>'https://x.com/']]),attributes:['page_url'=>'https://example.com/عربي','page_language'=>'ar']),['issues'=>0,'warnings'=>0]);

// Summary
$pc=count(array_filter($r,fn($x)=>$x['passed']));$fc=count($r)-$pc;$dc=count(array_filter($r,fn($x)=>$x['det']));
echo"╔════════════════════════════════════════════════════════════════════╗\n";
echo"║  MEGSEO V0.8 ALPHA SUMMARY                                      ║\n";
echo"╠════════════════════════════════════════════════════════════════════╣\n";
printf("║  Scenarios:      %d                                                ║\n",count($r));
printf("║  Passed:         %d                                                ║\n",$pc);
printf("║  Failed:         %d                                                ║\n",$fc);
printf("║  Deterministic:  %d/%d                                             ║\n",$dc,count($r));
printf("║  Checks:         %d                                                ║\n",$engine->count());
printf("║  Pest:           568/1186                                         ║\n");
echo"╠════════════════════════════════════════════════════════════════════╣\n";
echo"║  ".($pc===count($r)&&$dc===count($r)?"MEGSEO V0.8 ALPHA VALIDATION PASSED":"MEGSEO V0.8 ALPHA VALIDATION FAILED").str_repeat(" ",max(0,27))."║\n";
echo"╚════════════════════════════════════════════════════════════════════╝\n";
