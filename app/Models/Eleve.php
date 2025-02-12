<?php

namespace App\Models;

use App\Models\Cour;
use App\Models\Emprut;
use App\Models\PointEvaluation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eleve extends Model
{
    use HasFactory;

    use SoftDeletes;

    // protected $fillable = ['first_name','last_name','description','classe_id','anne_scolaire','anne_scolaire_id'];

    protected $guarded = [];

    public function classe(){
    	return $this->belongsTo('App\Models\Classe');
    }

    public function is_a_girl(){
        return $this->sexe == "F";
    }
    public function is_a_boy(){
        return $this->sexe == "M" || $this->sexe == "H";
    }

    public function compte(){
    	return $this->belongsTo('App\Models\Compte','id','eleve_id');
    }

    public function getFullNameAttribute(){
    	return $this->first_name .'  '. $this->last_name;
    } 

    // NIVEAU D'ETUDE
    public function getLevelAttribute(){
        return $this->classe->level->id ?? 0;
    }

    public static function getEleveById($id)
    {
        return self::find($id);
    }

    public function empruts()
    {
        return $this->hasMany(Emprut::class);
    }

    public function isFondementale()
    {
        // code...
        if (strcmp(strtoupper($this->classe->level->section->name),'FONDAMENTALE') == 0){
            return true;
        }

        return false;
    }

    public function listeEmprutNonRemis()
    {
        return "JE suis cool";
    }

    // LES POINTS OBTENUES DANS UN EVALUATION
    public function point_obentu_evaluation($evaluation_id){
        $check = PointEvaluation::where('evaluation_id', '=',$evaluation_id)->where('eleve_id' ,'=',$this->id)->first();
        return  $check ?? new PointEvaluation;
    }

    // LA FONCTION POUR RECUPERER LES POINTS D'UN ELEVE
    // ELEVE 
    // COURS 
    // TRIMESTRE 
    // ANNEE SCOLAIRE 
    // TYPE D'EVALUATION 
    // point_evaluations , cour_id , eleve_id , anne_scolaire_id  ,trimestre_id , type_evaluation

    public function recuperer_point($eleve_id = "" ,$cour_id, $trimestre_id, $anne_scolaire_id, $type_evaluation ){

        $choosed_eleve =  $this->id;
        $points = PointEvaluation::where('cour_id', '=', $cour_id)
        ->where('eleve_id','=',$eleve_id ?? $choosed_eleve)
        ->where('trimestre_id','=',$trimestre_id)
        ->where('anne_scolaire_id','=',$anne_scolaire_id)
        ->where('type_evaluation','=',$type_evaluation)
        ->get();

        if (count($points) == 0) {
            // code...
            return NULL;
        }
        //CALCULER LA MOYENNE SUR 
        $ponderation = Cour::findOrFail($cour_id)->ponderation;
        if($type_evaluation == 'EXAMEN'){
            $ponderation = Cour::findOrFail($cour_id)->ponderation_examen;
        } 
        if($type_evaluation == 'COMPENTENCE'){
            //COMPETANCE
            $ponderation = Cour::findOrFail($cour_id)->ponderation_compentance;
        }
        //$ponderation = Cour::findOrFail($cour_id)->ponderation;
        //POINT OBTENUE  MOYENNE DU COURS
        if($points->sum('ponderation') != 0){
           $resultat = $points->sum('point_obtenu') * $ponderation / $points->sum('ponderation');
       }else{
        $resultat  = 0;
    }
    return $resultat;
}
    //TYPE DES EVALUATIONS PAR DEFAUT
    // INTERROGATION
    // EXAMEN
    // COMPENTENCE

public function getPointTatalObtenue($eleve_id,$courses,$trimestre_id, $anne_scolaire_id){
 $total = 0;
 $courses_listes = [];
 $nombres_cours = 0;
 $points_total = [
    'INTERROGATION'=> 0 ,
    'EXAMEN' => 0,
    'COMPENTENCE' => 0,
    'TOTAL' => 0,
    'POURCENTAGE_INTERROGATION' => 0,
    'POURCENTAGE_EXAMEN' => 0,
];
$type_evaluations = ['INTERROGATION', 'EXAMEN', 'COMPENTENCE'];
$categoriesTotal = [];
foreach ($courses as $key => $coursCategorie) {
    $categories = [];

    $total_tj = 0;
    $total_examen = 0;
    
    foreach ($coursCategorie as  $cours) {
        $v = 0;
        $nombres_cours++;
        $detailPoints = [];
        foreach ($type_evaluations as  $evaluation) {
                // code...
            $r = $this->recuperer_point($eleve_id ,$cours->id, $trimestre_id, $anne_scolaire_id, $evaluation );
            $v += $r;
            $detailPoints[$evaluation] =  $r;
        }
       // dd($detailPoints);
        $total += $v;
        /*$total_trimestre = $detailPoints['EXAMEN'] +
         $detailPoints['COMPENTENCE'] + $detailPoints['INTERROGATION'];*/

        if ($detailPoints['EXAMEN'] != NULL && 
            $detailPoints['INTERROGATION'] != NULL) {
            // code...
            $total_trimestre = $detailPoints['EXAMEN'] + $detailPoints['INTERROGATION'];
            
            if (!$this->isFondementale() and ($detailPoints['COMPENTENCE'] == NULL)) {
                // code...
                $total_trimestre = "";
            }else{
              $total_trimestre += $detailPoints['COMPENTENCE'];
            }

        }else{
              $total_trimestre = "";
        }
        $c = [
            'name' => $cours->name,
            'credit' => $cours->credit,
            'ponderationTJ' => $cours->ponderation,
            'ponderationEx' => $cours->totalExamen,
            'max_tj_examen' => ($cours->totalExamen + $cours->ponderation),
            'cours' => $cours,
            'total_1' => $v,
            'details' => $detailPoints,
            'interrogation' => $detailPoints['INTERROGATION'],
            'examen' => $detailPoints['EXAMEN'],
            'compentence' =>  $detailPoints['COMPENTENCE'],
            'poderation' => $cours->ponderationTotal,
            'total' => $total_trimestre,
            //Calcule du profondeur de l'echec point obtenu - 50 % du point total
            'profondeur_echec' => ($v - ( $cours->ponderationTotal / 2)),
            'is_echec' => ($v < ( $cours->ponderationTotal / 2))
        ];

        $total_tj += $c['interrogation'];
        $total_examen += $c['examen'];
        $points_total['INTERROGATION'] +=  $c['interrogation'];
        $points_total['EXAMEN'] +=  $c['examen'];
        $points_total['TOTAL'] +=  floatval($c['total']) ;

        $categories[$key][]= $c;

       
    }
     $categoriesTotal[$key][] = [
            'tj' => $total_tj,
            'examen' => $total_examen,
            'total' => ($total_examen + $total_tj)
        ];
        
   // $categories[$key]['total'] = [];
    $courses_listes[] = $categories;  
}
//dd($categoriesTotal);
$p = $this->classe->ponderation();
    // POURCENTAGE DES EXAMENS ET DES INTERROGATIONS
$points_total['POURCENTAGE_INTERROGATION'] = getPourcentage( $points_total['INTERROGATION'],  $p['total_interrogation'] ); 
$points_total['POURCENTAGE_EXAMEN'] = getPourcentage($points_total['EXAMEN'], $p['total_examen'] );

return [
    'categoriesTotal' => $categoriesTotal,
    'total' => $total,
    'courses_listes' => $courses_listes,
    'points_total' => $points_total,

];
}


public function is_nonClasse($trimestre,$anne_scolaire_id){
    // Un élève est consideré comme un non classé si il n'a pas passé tout les examens
    // Année scolaire
    // Classe
    // Trimestre
    // Tous les évaluations 
    // LES EVALUATIONS ANNEE SCOLAIRE TRIMESTRE
    
    $courses = $this->classe->courses();
    //dd($this->classe_id);
    // Vérifier que chaque cours possède l'évaluation d'une interrogagation
    // Vérifier que chaque cours possède l'évaluation de l'Examen

    $errors = [];

    foreach($courses as $course){
        // Je recupere tout les evaluations d'un cours
         $courses_evaluations = Evaluation::where('trimestre','=',$trimestre)
                                ->where('anne_scolaire_id','=',$anne_scolaire_id)
                                ->where('cour_id',$course->id)
                                ->where('classe_id',$this->classe_id)
                                ->get();
        // Quand on ne trouve pas une évaluation on s'arrête

        if(!$courses_evaluations or (!$course->conduite and $courses_evaluations->count() < 2) ){
            
            $errors['EVALUATION_INCOMPLETE'][] = $course;
        }
        //dd();
        //dump($courses_evaluations);
        foreach ($courses_evaluations as $key => $ev) {
            // code...
            $points = PointEvaluation::where('evaluation_id',$ev->id)
                                        ->where('eleve_id', $this->id)
                                        ->first();
            if($points == null || $points->point_obtenu == null){
                
                $errors['EVALUATION_INCOMPLETE'][] = [
                    'cours' => $ev->cour->name,
                    'evaluations' => $ev->type_evaluation,

                ];
            }
            
        }

    }

    return count($errors) > 0 ? $errors : false;
}

}


