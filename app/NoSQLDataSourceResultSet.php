<?php
/**
 * The NoSQLDataSourceResultSet class definition.
 *
 * Represents a collection of results from a NoSQL query
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App;

use Illuminate\Support\Collection;

/**
 * Class NoSQLDataSourceResultSet
 * @package App
 */
class NoSQLDataSourceResultSet
{
  /** @var Collection */
  protected $results;

  /** @var Collection */
  protected $facets;

  /** @var int */
  protected $total_results_count;

  public function __construct()
  {
    $this->results = new Collection();
    $this->facets = new Collection();
  }

  /**
   * @return Collection
   */
  public function getResults(): Collection
  {
    return $this->results;
  }

  /**
   * @param Collection $results
   */
  public function setResults(Collection $results)
  {
    $this->results = $results;
  }

  /**
   * @param NoSQLDataSourceResult $result
   */
  public function addResult(NoSQLDataSourceResult $result)
  {
    $this->results->push($result);
  }

  /**
   * @param array $facet
   */
  public function addFacet(array $facet)
  {
    $this->facets->push($facet);
  }

  /**
   * @return Collection
   */
  public function getFacets(): Collection
  {
    return $this->facets;
  }

  /**
   * @param Collection $facets
   */
  public function setFacets(Collection $facets)
  {
    $this->facets = $facets;
  }

  /**
   * @return int
   */
  public function getTotalResultsCount(): int
  {
    return $this->total_results_count;
  }

  /**
   * @param int $total_results_count
   */
  public function setTotalResultsCount(int $total_results_count)
  {
    $this->total_results_count = $total_results_count;
  }
}