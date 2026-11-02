<?php
declare(strict_types=1);
/** K-Means dengan Z-score, inisialisasi deterministik berbasis seed, SSE dan silhouette. */
final class KMeans {
    private static function dist(array $a,array $b): float { $s=0.0; foreach($a as $i=>$v){$d=$v-$b[$i];$s+=$d*$d;} return sqrt($s); }
    public static function standardize(array $data): array {
        $d=count($data[0]??[]);$means=array_fill(0,$d,0.0);$std=array_fill(0,$d,0.0);$n=count($data);
        for($j=0;$j<$d;$j++){foreach($data as $row)$means[$j]+=$row[$j];$means[$j]/=max(1,$n);foreach($data as $row)$std[$j]+=($row[$j]-$means[$j])**2;$std[$j]=sqrt($std[$j]/max(1,$n));}
        $z=[];foreach($data as $row){$v=[];foreach($row as $j=>$x)$v[]=($std[$j]<1e-12)?0.0:($x-$means[$j])/$std[$j];$z[]=$v;}
        return [$z,$means,$std];
    }
    public static function fit(array $raw,int $k=3,int $maxIter=100,float $tol=0.0001,int $seed=42): array {
        $n=count($raw); if($n<$k||$k<2)throw new RuntimeException('Jumlah data harus minimal sama dengan K dan K minimal 2.');
        foreach($raw as $row)if(count($row)!==count($raw[0])||in_array(null,$row,true))throw new RuntimeException('Data nilai tidak lengkap atau tidak konsisten.');
        [$x,$means,$std]=self::standardize($raw);
        mt_srand($seed);$indices=range(0,$n-1);shuffle($indices);$centers=[];for($c=0;$c<$k;$c++)$centers[]=$x[$indices[$c]];
        $labels=array_fill(0,$n,-1);$iterations=0;
        for($it=1;$it<=$maxIter;$it++){
            $changed=false;$newLabels=[];
            foreach($x as $i=>$row){$best=0;$bd=INF;foreach($centers as $c=>$center){$dd=self::dist($row,$center);if($dd<$bd){$bd=$dd;$best=$c;}}$newLabels[$i]=$best;if($labels[$i]!==$best)$changed=true;}
            $groups=array_fill(0,$k,[]);foreach($newLabels as $i=>$lab)$groups[$lab][]=$i;
            for($c=0;$c<$k;$c++)if(!$groups[$c]){
                // Pecah cluster terbesar dengan memindahkan titik terjauh dari centroidnya.
                $largest=0;for($j=1;$j<$k;$j++)if(count($groups[$j])>count($groups[$largest]))$largest=$j;
                if(count($groups[$largest])>1){$farthest=$groups[$largest][0];$fd=-1;foreach($groups[$largest] as $idx){$dd=self::dist($x[$idx],$centers[$largest]);if($dd>$fd){$fd=$dd;$farthest=$idx;}}$newLabels[$farthest]=$c;$groups[$c]=[$farthest];$groups[$largest]=array_values(array_diff($groups[$largest],[$farthest]));}
                else $centers[$c]=$x[mt_rand(0,$n-1)];
            }
            $newCenters=[];for($c=0;$c<$k;$c++){$center=array_fill(0,count($x[0]),0.0);$cnt=count($groups[$c]);foreach($groups[$c] as $idx)foreach($x[$idx] as $j=>$v)$center[$j]+=$v;foreach($center as $j=>$v)$center[$j]=$v/max(1,$cnt);$newCenters[]=$center;}
            $shift=0;foreach($centers as $c=>$old)$shift=max($shift,self::dist($old,$newCenters[$c]));
            $centers=$newCenters;$labels=$newLabels;$iterations=$it;if(!$changed||$shift<$tol)break;
        }
        $sse=0.0;foreach($x as $i=>$row)$sse+=self::dist($row,$centers[$labels[$i]])**2;
        $sil=self::silhouette($x,$labels,$k);
        $centroidRaw=[];foreach($centers as $center){$r=[];foreach($center as $j=>$v)$r[]=$std[$j]<1e-12?$means[$j]:$v*$std[$j]+$means[$j];$centroidRaw[]=$r;}
        $avg=[];foreach($centroidRaw as $c=>$row)$avg[$c]=array_sum($row)/count($row);$order=array_keys($avg);usort($order,fn($a,$b)=>$avg[$a]<=>$avg[$b]);$labelsByCluster=[];foreach($order as $rank=>$cluster)$labelsByCluster[$cluster]=($rank===0?'Kompetensi Perlu Ditingkatkan':($rank===$k-1?'Kompetensi Tinggi':'Kompetensi Menengah'));
        return ['labels'=>$labels,'centers'=>$centers,'centroid_raw'=>$centroidRaw,'cluster_names'=>$labelsByCluster,'sse'=>$sse,'silhouette'=>$sil,'iterations'=>$iterations,'means'=>$means,'std'=>$std,'standardized'=>$x,'seed'=>$seed];
    }
    public static function silhouette(array $x,array $labels,int $k): ?float {
        $n=count($x);if($n<2)return null;$groups=array_fill(0,$k,[]);foreach($labels as $i=>$c)$groups[$c][]=$i;
        $valid=0;$sum=0.0;
        foreach($x as $i=>$row){$own=$labels[$i];if(count($groups[$own])<2)continue;$a=0;foreach($groups[$own] as $j)if($i!==$j)$a+=self::dist($row,$x[$j]);$a/=count($groups[$own])-1;$b=INF;
            foreach($groups as $c=>$members){if($c===$own||!$members)continue;$d=0;foreach($members as $j)$d+=self::dist($row,$x[$j]);$b=min($b,$d/count($members));}
            if(!is_finite($b))continue;$den=max($a,$b);$sum+=($den<1e-12?0:($b-$a)/$den);$valid++;
        }return $valid?$sum/$valid:null;
    }
}
