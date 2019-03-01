<?php
namespace sanabuk\driver;

use Illuminate\Http\Request;

/**
 * Query Parser
 *
 * Test to generate Eloquent Request with Url Parameters
 *
 * example Route : {startModel}?includes=model1,model2&equals[]=id:1&max[model1]=id:100&min[model2]=id:70
 * 
 * api/driver?includes=vehicle,historic.vehicle,historic.driver&sort[historic]=-created_at&sort[]=id&fields[historic.driver]=name&fields[historic.vehicle]=license_number
 * $query = "driver(sort:id){id,vehicle{id,license_number,brand,color},historic(sort:created_at){driver{name},vehicle{license_number}}}";
 * */

trait QueryParserV2
{
	public function getDatas(Request $request, $query)
    {
        $queryParamUrl = $request->get('query');
        list($conditions, $format)  = $this->split($queryParamUrl);

        //dump($conditions);

        $query   = $this->handlingConditions($query, $conditions);
        return $query;
    }

    /**
     * Séparer la requête en 2 parties
     * - les conditions de la requêtes
     * - le format de sortie attendue de la requête
     * @param string $queryParamUrl
     * @return array
     */
    public function split($queryParamUrl)
    {
        preg_match('/(.*\)){/i', $queryParamUrl, $matches);
        $conditions = $matches[1];

        $parser = new ParentheseParser();
        $conditions = $parser->generate($conditions);
        
        preg_match('/\)({.*)/', $queryParamUrl, $matches);
        $format = $matches[1];

        $parser = new ParentheseParser();
        $format = $parser->generate('driver'.$format,'{','}');

        return [$conditions,$format];
    }

    /**
     * Gestion des conditions de la requête
     * Equivalent WhereHas de Eloquent
     * @param Builder $query
     * @param array $conditions
     * @return Builder $query
     */ 
    public function handlingConditions($query, $conditions)
    {
    	foreach($conditions as $key => $value){
    		if(is_array($value)){
    			foreach ($value as $key2 => $value2) {
    				if(is_integer($key2)){
    					// Conditions sur le modèle de base de la requête
    					list($type,$condition) = $this->getConditionType($value2);
    					switch ($type) {
			                case 'equals':
			                	list($needle, $haystack) = explode(':',$condition);
			                    $query = $this->addWhere($query,$needle,$haystack,'=');
			                    break;
			                case 'like':
			                    list($needle, $haystack) = explode(':',$condition);
			                    $query = $this->addWhere($query,$needle,$haystack,'like');
			                    break;
			                case 'min':
			                    list($needle, $haystack) = explode(':',$condition);
			                    $query = $this->addWhere($query,$needle,$haystack,'>=');
			                    break;
			                case 'max':
			                    list($needle, $haystack) = explode(':',$condition);
			                    $query = $this->addWhere($query,$needle,$haystack,'<=');
			                    break;

			                default:
			                    # code...
			                    break;
			            }

    				} else {
    					// Condition sur une relation
    					$relation = $key2;
    					//dump($value2);
    					$query = $this->constrainsWhereHas($query, $relation, $value2);
    				}
    			}
    		}
    	}
    	return $query;
    }

    private function getConditionType($value)
    {
    	return explode('=',$value);
    }

    private function isWhere($key)
    {
        return in_array($key, ['equals', 'like', 'min', 'max']);
    }

    private function addWhere($query, $column1, $column2, $operator = '=')
    {
        if ($operator == 'like') {
            $column2 = '%' . $column2 . '%';
        }
        return $query->where($column1, $operator, $column2);
    }

    private function constrainsWhereHas($q, $model, $param)
    {
    	$q = $q->whereHas($model, function($query) use ($param){
    		foreach ($param as $key => $value) {
    			if(is_integer($key)){
    				list($type,$condition) = $this->getConditionType($value);
    				list($needle, $haystack) = explode(':',$condition);
					switch ($type) {
		                case 'equals':
		                    $query = $this->addWhere($query,$needle,$haystack,'=');
		                    break;
		                case 'like':
		                    $query = $this->addWhere($query,$needle,$haystack,'like');
		                    break;
		                case 'min':
		                    $query = $this->addWhere($query,$needle,$haystack,'>=');
		                    break;
		                case 'max':
		                    $query = $this->addWhere($query,$needle,$haystack,'<=');
		                    break;

		                default:
		                    # code...
		                    break;
		            }
    			}
    		}
    		$query;
    	});
        return $q;
    }
}