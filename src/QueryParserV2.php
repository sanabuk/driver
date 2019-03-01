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

        dump($conditions);
        dump($format);

        dd();

        $query   = $this->generateQuery($query, $paramBuilder);
        $perpage = $request->get('perpage', 15);
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
}