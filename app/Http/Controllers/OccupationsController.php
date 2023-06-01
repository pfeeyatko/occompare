<?php

namespace App\Http\Controllers;

use App\Contracts\OccupationParser;
use App\Helpers\OccompareHelper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OccupationsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $occparser;

    public function __construct(OccupationParser $parser)
    {
        $this->occparser = $parser;
    }

    public function index()
    {
        return $this->occparser->list();
    }

    public function compare(Request $request)
    {
        $this->occparser->setScope('skills');
        $occupation_1 = $this->occparser->get($request->get('occupation_1'));
        $occupation_2 = $this->occparser->get($request->get('occupation_2'));

        /** IMPLEMENT COMPARISON **/
        
        // order each occupations skills by name asc
        usort($occupation_1, ['App\Helpers\OccompareHelper', 'compareBySkillName']);
        usort($occupation_2, ['App\Helpers\OccompareHelper', 'compareBySkillName']);

        $skillsComparisonList = [];

        // iterate through the skills in each occupation
        // compare "importance" to find a value for similarity 
        for ($i = 0; $i < count($occupation_1); $i++) {
            if ($occupation_1[$i][0] == 0 && $occupation_2[$i][0] == 0) {
                array_push($skillsComparisonList, 100);
            } else {
                array_push($skillsComparisonList, OccompareHelper::compareSimilarity($occupation_1[$i][0], $occupation_2[$i][0]));
            }
        }

        // find the avg percentage for skills comparison
        $skillSimilarityPercent = array_sum($skillsComparisonList) / count($skillsComparisonList);

        // order each occupations skills by importance asc
        usort($occupation_1, ['App\Helpers\OccompareHelper', 'compareByImportance']);
        usort($occupation_2, ['App\Helpers\OccompareHelper', 'compareByImportance']);

        // take top skills by importance from each occupation
        $occupation_1_top = array_map(function ($object) {
            return $object[1];
        }, array_slice($occupation_1, -OccompareHelper::TOP_SKILLS_LIMIT, OccompareHelper::TOP_SKILLS_LIMIT, true));

        $occupation_2_top = array_map(function ($object) {
            return $object[1];
        }, array_slice($occupation_2, -OccompareHelper::TOP_SKILLS_LIMIT, OccompareHelper::TOP_SKILLS_LIMIT, true));

        // find which top skills intersect
        $skillIntersect = array_intersect($occupation_1_top, $occupation_2_top);

        // find the percentage of top skills which intersect
        $skillIntersectPercent = count($skillIntersect) / OccompareHelper::TOP_SKILLS_LIMIT * 100;

        // find the avg of skills similarity and skills intersect values
        $match = round(($skillSimilarityPercent + $skillIntersectPercent) / 2);

        /** IMPLEMENT COMPARISON **/

        return [
            'skills_intersect' => array_reverse($skillIntersect),
            'occupation_1' => $occupation_1,
            'occupation_2' => $occupation_2,
            'match' => $match
        ];
    }
}
