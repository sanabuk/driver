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
 * ?query = "driver(sort:id){id,vehicle{id,license_number,brand,color},historic(sort:created_at){driver{name},vehicle{license_number}}}";
 * ?model=driver&conditions=max=id:10&output=
 * */

trait QueryParserV2
{
    protected $askedModel;

    public function getDatas(Request $request, $query)
    {
        $queryParamUrl = $request->all();

        $this->askedModel = $queryParamUrl['model'];
        $parser           = new ParentheseParser();
        $conditions       = $parser->generate($queryParamUrl['conditions']);
        $output           = $parser->generate($queryParamUrl['output']);

        //list($conditions, $format) = $this->split($queryParamUrl);
        $query = $this->handlingConditions($query, $conditions);
        $query = $this->handlingFormat($query, $output);
        return $query;
    }

    /**
     * @deprecated new request format
     * Séparer la requête en 2 parties
     * - les conditions de la requête
     * - le format de sortie attendue de la requête
     * @param string $queryParamUrl
     * @return array
     */
    /*public function split($queryParamUrl)
    {
    preg_match('/(.*\)){/i', $queryParamUrl, $matches);
    $conditions = $matches[1];
    $parser     = new ParentheseParser();
    $conditions = $parser->generate($conditions);

    preg_match('/\)({.*)/', $queryParamUrl, $matches);
    $format = $matches[1];
    $parser = new ParentheseParser();
    $format = $parser->generate($this->askedModel . $format, '{', '}');
    return [$conditions[$this->askedModel], $format[$this->askedModel]];
    }*/

    /**
     * Gestion des conditions de la requête
     * Equivalent WhereHas de Eloquent
     * @param Builder $query
     * @param array $conditions
     * @return Builder $query
     */
    private function handlingConditions($query, $conditions)
    {
        foreach ($conditions as $key => $value) {
            if (is_integer($key)) {
                // Conditions sur le modèle de base de la requête
                list($type, $condition)  = $this->getConditionType($value);
                list($needle, $haystack) = explode(':', $condition);
                $query                   = $this->checkTypeAndApplyCondition($query, $type, $needle, $haystack);
            } else {
                // Condition sur une relation
                $relation = $key;
                $negation = $relation[0] == "!" ? true : false;
                $query    = $this->constrainsWhereHas($query, trim($relation, '!'), $value, $negation);
            }
        }
        return $query;
    }

    private function handlingFormat($query, $format)
    {
        //dd($format);
        foreach ($format as $key => $value) {
            if (is_integer($key)) {
                // Conditions sur le modèle de base de la requête
                $selectArray[] = $value;
            } else {
                // Condition sur une relation
                $relation = $key;
                $query    = $this->addEagerLoadRelation($query, $relation, $value);
            }
            $query = $query->select($selectArray);
        }
        return $query;
    }

    private function addEagerLoadRelation($query, $relation, $param, $counter = 0)
    {
        $query = $query->with([$relation => $this->getCallback($relation, $counter, $param)]);
        return $query;
    }

    private function getCallback($relation, $counter, $param)
    {
        return function ($q) use ($relation, $counter, $param) {
            $q = $this->constrainsSelectAndSortAndWhere($q, $relation, $param);
        };
    }

    private function constrainsSelectAndSortAndWhere($q, $model, $param)
    {
        foreach ($param as $key => $v) {
            if (is_integer($key)) {
                if ($this->isSort($key)) {
                    if ($v[0] == '-') {
                        $q = $this->addSort($q, trim($v, '-'), 'DESC');
                    } else {
                        $q = $this->addSort($q, $v, 'ASC');
                    }
                }
                if ($this->isSelect($key)) {
                    $q = $this->addSelect($q, explode(',', $v));
                }
                if ($this->isWhere($key)) {
                    $operator = [
                        'like'   => 'like',
                        'equals' => '=',
                        'min'    => '>=',
                        'max'    => '<=',
                    ];
                    $q = $this->addWhere($q, explode(':', $v)[0], explode(':', $v)[1], $operator[$key]);
                }
            } else {
                $q = $this->addEagerLoadRelation($q, $key, $v);
            }
        }
        return $q;
    }

    private function getConditionType($value)
    {
        return explode('=', $value);
    }

    private function isSelect($key)
    {
        return is_integer($key);
    }

    private function addSelect($query, $column)
    {
        //todo Gérer les PrimaryKey et les foreignKey
        $column[] = 'id';
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
        return is_integer($key) ? false : in_array($key, ['equals', 'like', 'min', 'max']);
    }

    private function addWhere($query, $column1, $column2, $operator = '=')
    {
        if ($operator == 'like') {
            $column2 = '%' . $column2 . '%';
        }
        return $query->where($column1, $operator, $column2);
    }

    private function constrainsWhereHas($q, $model, $param, $negation = false)
    {
        if (!$negation) {
            $q = $q->whereHas($model, function ($query) use ($param) {
                foreach ($param as $key => $value) {
                    if (is_integer($key)) {
                        list($type, $condition)  = $this->getConditionType($value);
                        list($needle, $haystack) = explode(':', $condition);
                        $this->checkTypeAndApplyCondition($query, $type, $needle, $haystack);
                    } else {
                        $this->constrainsWhereHas($query, $key, $value);
                    }
                }
                $query;
            });
        } else {
            $q = $q->whereDoesntHave($model, function ($query) use ($param) {
                foreach ($param as $key => $value) {
                    if (is_integer($key)) {
                        list($type, $condition)  = $this->getConditionType($value);
                        list($needle, $haystack) = explode(':', $condition);
                        $this->checkTypeAndApplyCondition($query, $type, $needle, $haystack);
                    } else {
                        $this->constrainsWhereHas($query, $key, $value);
                    }
                }
                $query;
            });
        }
        return $q;
    }

    /**
     * @param Builder $query
     * @param string $type
     * @param string $needle
     * @param string $haystack
     * @return Builder
     */
    private function checkTypeAndApplyCondition($query, $type, $needle, $haystack)
    {
        switch ($type) {
            case 'equals':
                $query = $this->addWhere($query, $needle, $haystack, '=');
                break;
            case 'like':
                $query = $this->addWhere($query, $needle, $haystack, 'like');
                break;
            case 'min':
                $query = $this->addWhere($query, $needle, $haystack, '>=');
                break;
            case 'max':
                $query = $this->addWhere($query, $needle, $haystack, '<=');
                break;

            default:
                # code...
                break;
        }
        return $query;
    }
}
