<?php

namespace sanabuk\driver\strategies;

/**
 * AssociationDriverVehicleStrategy
 */
class AssociationDriverVehicleStrategy
{	
	/**
	 * @param array $data
	 * @param Driver $driver
	 * @param Vehicle $vehicle
	 * @return bool
	 */
	public function __invoke($data, $driver, $vehicle)
	{
		/**
		 * Ici on gère les règles données par l'utilisateur
		 * Pour ce cas précis: 
		 * Une association pouvant être déclarée à postériori, il faut checker les données temporelles possiblement envoyées [associated_at]
		 * Il faut alors vérifier l'état du driver et du vehicle impliqués à [associated_at]
		 * ...
		 */

	}
}