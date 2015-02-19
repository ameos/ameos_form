<?php

namespace Ameos\AmeosForm\Domain\Repository;

class SearchableRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * return query clause
	 * @param array $clause clause
	 * @param object $query query
	 * @return object
	 */	 
	protected function getQueryClause($clause, $query) {
		if(is_array($clause)) {
			switch($clause['type']) {
				case 'contains':
					return $query->$clause['type']($clause['field'], $clause['value']);
					break;

				case 'logicalOr':
					if(is_array($clause['clauses'])) {
						$subclauses = [];
						foreach($clause['clauses'] as $subclause) {
							$subclauses[] = $this->getQueryClause($subclause, $query);
						}
						return $query->logicalOr($subclauses);
					}
					break;

				case 'logicalAnd':
					if(is_array($clause['clauses'])) {
						$subclauses = [];
						foreach($clause['clauses'] as $subclause) {
							$subclauses[] = $this->getQueryClause($subclause, $query);
						}
						return $query->logicalAnd($subclauses);
					}
					break;

				case 'logicalNot':
					return $query->logicalNot($this->getQueryClause($clause['clause'], $query));
					break;

				default:
					return $query->$clause['type']($clause['field'], $clause['value']);
					break;
			}
		}
	}

	/**
	 * Return query result
	 *
	 * @param	array	$clauses where clauses
	 * @param	bool|string	$orderby order by 
	 * @param	string	$direction direction
	 * @return 	object
	 */
	public function findByClausesArray($clauses, $orderby = FALSE, $direction = 'ASC') {
		$query = $this->createQuery();
		$objectsClauses = [];
		foreach($clauses as $clause) {
			$objectsClauses[] = $this->getQueryClause($clause, $query);
		}
		if(!empty($objectsClauses)) {
			$query->matching($query->logicalAnd($objectsClauses));
		}

		if($orderby !== FALSE) {
			$query->setOrderings(array($orderby => $direction));
		}
		
		return $query->execute();
	}	
}
