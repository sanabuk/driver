<?php

namespace sanabuk\driver;

use Illuminate\Http\Request;

/**
 * Query Parser
 * 
 * Test to generate Eloquent Request with Url Parameters
 * 
 * example Route : base_request/{model}?equals[]=id:1&fields[]=id,name&includes[]=drivers
 * */

trait QueryParser
{
	public function getDatas(Request $request,$query)
    {
        $queryParamUrl = $request->all();
        $paramBuilder  = $this->split($queryParamUrl);

        //dd($paramBuilder);

        $query = $this->generateQuery($query, $paramBuilder);
        $perpage = $request->get('perpage',15);
        return $query;
    }

    private function split($queryParamUrl)
    {
        $queryStructure = [
            'includes' => [],
            'fields'   => [],
            'sort'     => [],
            'equals'   => [],
            'like'     => [],
            'min'      => [],
            'max'      => [],
        ];

        foreach ($queryParamUrl as $key => $value) {
            switch ($key) {
                case 'includes':
                    $queryStructure['includes'] = explode(',', $value);
                    break;
                case 'fields':
                    $queryStructure['fields'] = $value;
                    break;
                case 'sort':
                    $queryStructure['sort'] = $value;
                    break;
                case 'equals':
                    $queryStructure['equals'] = $value;
                    break;
                case 'like':
                    $queryStructure['like'] = $value;
                    break;
                case 'min':
                    $queryStructure['min'] = $value;
                    break;
                case 'max':
                    $queryStructure['max'] = $value;
                    break;

                default:
                    # code...
                    break;
            }
        }
        return $queryStructure;
    }

    private function generateQuery($query, $param)
    {
    	$paramForAskedModel = $this->getParamForAskedModel($param);
        
        foreach ($paramForAskedModel as $key => $value) {
            switch ($key) {
                case 'includes':
                    foreach ($value as $relation) {
                    	//Gestion des includes (with)
                        $relations = explode('.', $relation);
                        $query = $this->addIncludes($query, $relations, $param);
                        //Gestion des includes (whereHas)
                        $completeName ='';
                        for($cpt = 0;$cpt < count($relations);$cpt++){
                        	if(empty($completeName)){
                        		$completeName = $relations[$cpt];
                        	} else {
                        		$completeName = $completeName.'.'.$relations[$cpt];
                        	}	
	                        $query = $this->constrainsWhereHas($query, $completeName, $param);
                        }
                    }

                    break;
                case 'fields':
                    foreach ($value as $v) {
                        $query = $query->select(explode(',', $v));
                    }
                    break;
                case 'sort':
                    foreach ($value as $v) {
                        $query = $v[0] == '-' ? $this->addSort($query, trim($v, '-'), 'DESC') : $this->addSort($query,$v, 'ASC');
                    }
                    break;
                case 'equals':
                    foreach ($value as $v) {
                        $query = $this->addWhere($query,explode(':', $v)[0], explode(':', $v)[1], '=');
                    }
                    break;
                case 'like':
                    foreach ($value as $v) {
                        $query = $this->addWhere($query,explode(':', $v)[0], explode(':', $v)[1], 'like');
                    }
                    break;
                case 'min':
                    foreach ($value as $v) {
                        $query = $this->addWhere($query,explode(':', $v)[0], explode(':', $v)[1], '>');
                    }
                    //
                    break;
                case 'max':
                    foreach ($value as $v) {
                        $query = $this->addWhere($query,explode(':', $v)[0], explode(':', $v)[1], '<');
                    }
                    //
                    break;

                default:
                    # code...
                    break;
            }
        }
        return $query;
    }

    private function getParamForAskedModel($param)
    {
    	foreach ($param as $key => $array) {
    		if(is_array($array)){
    			$paramForAskedModel[$key] = array_where($array, function ($value, $key) {
		            return is_integer($key);
		        });
    		}
        }
        return $paramForAskedModel;
    } 

    private function addIncludes($query, $relations,$param, $counter = 0)
    {
    	$query = $query->with([$relations[$counter] => $this->getCallback($relations, $counter, $param)]);
	    return $query;
    }

    private function getCallback($relations,$counter,$param)
    {
        return function($q) use($relations,$counter,$param){
        	$relationName = '';
        	for($i = 0;$i<$counter+1;$i++){
        		if($i == 0){
        			$relationName = $relations[$i];
        		} else {
        			$relationName = $relationName.'.'.$relations[$i];
        		}
        	}

            $q = $this->constrainsSelectAndSortAndWhere($q, $relationName, $param);
            if(isset($relations[$counter+1])){
            	$counter += 1;
            	$q = $this->addIncludes($q,$relations,$param,$counter);
            }else{
	            $q;
	        }
        };
    }

    private function constrainsSelectAndSortAndWhere($q, $model, $param)
    {
        foreach ($param as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k === $model) {
                    if ($this->isSort($key)) {
                        if($v[0]=='-'){
                            $q = $this->addSort($q,trim($v,'-'),'DESC');
                        } else{
                            $q = $this->addSort($q,$v,'ASC');
                        }
                    }
                    if ($this->isSelect($key)) {
                        $q = $this->addSelect($q,explode(',',$v));
                    }
                    if ($this->isWhere($key)) {
                        $operator = [
                            'like'=>'like',
                            'equals'=>'=',
                            'min'=>'>',
                            'max'=>'<'
                        ];
                        $q = $this->addWhere($q,explode(':',$v)[0],explode(':',$v)[1],$operator[$key]);
                    }
                }
            }
            
        }
        return $q;
    }

    private function constrainsWhereHas($q, $model, $param)
    {
        foreach ($param as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k === $model) {
                    if ($this->isWhere($key)) {
                        $operator = [
                            'like'=>'like',
                            'equals'=>'=',
                            'min'=>'>',
                            'max'=>'<'
                        ];
                        $q = $q->whereHas($model,function($query)use($v,$key,$operator){
                            $this->addWhere($query,explode(':',$v)[0],explode(':',$v)[1],$operator[$key]);
                        });
                    }
                }
            }
            
        }
        return $q;
    }

    private function isSelect($key)
    {
        return $key == 'fields';
    }

    private function addSelect($query, $column)
    {
    	//todo Gérer les PrimaryKey et les foreignKey
        $column[]='id';
        return $query->select($column);
    }

    private function isSort($key)
    {
        return $key == 'sort';
    }

    private function addSort($query, $column, $operator = 'ASC')
    {
        return $query->orderBy($column, $operator);
    }

    private function isWhere($key)
    {
        return in_array($key, ['equals', 'like', 'min', 'max']);
    }

    private function addWhere($query, $column1, $column2, $operator = '=')
    {
        if ($operator == 'like'){
            $column2 = '%'.$column2.'%';
        }
        return $query->where($column1,$operator,$column2);
    }
}