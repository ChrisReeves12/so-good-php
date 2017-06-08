<?php
/**
 * The SolrDataSourceService class definition.
 *
 * The description of the class
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Contracts\ISolrDocumentable;
use App\Services\Contracts\INoSQLDataSourceService;
use App\Services\Contracts\IProductService;
use App\NoSQLDataSourceResult;
use App\NoSQLDataSourceResultSet;
use App\Product;
use Illuminate\Support\Collection;
use Solarium\Core\Client\Client;

class SolrDataSourceService implements INoSQLDataSourceService
{
  /** @var Client */
  protected $client;

  /**
   * Find in the collection by a given set of criteria
   * @param string $collection
   * @param array $criteria
   * @param array $options
   * @return NoSQLDataSourceResultSet
   */
  public function findBy(string $collection, array $criteria = [], array $options = []): NoSQLDataSourceResultSet
  {
    $result_set = new NoSQLDataSourceResultSet();
    $this->_getClient($collection);

    // Build query string
    $query_string = '';
    foreach($criteria as $key => $value)
    {
      // General search among all fields
      if($key == '*all*')
        $key = '_text_';

      if(is_array($value))
      {
        if(strtolower(key($value)) == 'between')
        {
          $query_value = '[' . trim($value['between'][0]) . ' TO ' . trim($value['between'][1]) . ']';
        }
        elseif(strtolower(key($value)) == 'not')
        {
          if(is_array(current($value)))
          {
            $params = current($value);
            $query_value = $params[0] . ' TO ' . $params[1] . ' NOT ' . $params[2];
          }
          else
          {
            $query_value = '* TO * NOT ' . current($value);
          }
        }
        else
        {
          $query_value = '(';
          foreach($value as $value_part)
          {
            $query_value .= $value_part . ' OR ';
          }

          $query_value = rtrim($query_value, ' OR ');
          $query_value .= ')';
        }
      }
      else
      {
        $query_value = trim($value);
      }

      $query_string .= $key . ':' . $query_value . ' AND ';
    }

    $query_string = rtrim($query_string, ' AND ');

    $query = $this->client->createSelect()->setQuery($query_string);

    // Add sort
    if(!empty($options['sort_by']))
    {
      $query->addSort($options['sort_by'][0], $options['sort_by'][1]);
    }

    // Add start and max results
    if(!empty($options['start']))
    {
      $query->setStart($options['start']);
    }

    if(!empty($options['max_results']))
    {
      $query->setRows($options['max_results']);
    }

    // Get facets
    if(!empty($options['facets']))
    {
      foreach($options['facets'] as $facet)
      {
        $query->getFacetSet()->createFacetField($facet)->setField($facet);
      }
    }

    $docs = $this->client->execute($query);
    $result_set->setTotalResultsCount($docs->getNumFound());

    if($docs->getNumFound() > 0)
    {
      foreach($docs as $doc)
      {
        $fields = $doc->getFields();
        $formatted_fields = [];
        foreach($fields as $key => $value)
        {
          if($key == '_version_' || $key == 'score')
            continue;

          $value = str_replace(',USD', '', $value);
          $formatted_fields[$key] = $value;
        }

        $result_set->addResult(new NoSQLDataSourceResult($formatted_fields));
      }
    }

    // Get facets
    if(!empty($options['facets']))
    {
      foreach($options['facets'] as $facet)
      {
        $facet_values = $docs->getFacetSet()->getFacet($facet);
        foreach($facet_values as $facet_value => $count)
        {
          if($count < 1)
          {
            continue;
          }

          $result_set->addFacet([$facet_value => $count]);
        }
      }
    }

    return $result_set;
  }

  /**
   * Update index of the given collection
   * @param string $collection
   * @param string $model_class
   * @return array
   * @throws \Exception
   */
  public function updateCollectionIndex(string $collection, string $model_class)
  {
    $result = ['success' => true, 'error' => false];

    // Check if the model class exists
    if(!class_exists($model_class))
      throw new \Exception('The model class ' . $model_class . ' does not exist.');

    // Check if the model class implements correct interface
    if(!in_array(ISolrDocumentable::class, class_implements($model_class)))
      throw new \Exception('The model class must implement ' . ISolrDocumentable::class);

    try
    {
      // Get Solr Client
      $client = $this->_getClient($collection);
      $update = $client->createUpdate();

      // Go through each product and add documents
      $docs = $model_class::all()->map(function(ISolrDocumentable $model) use($update) {
        $doc = $update->createDocument();
        $updated_doc = $model->toSolrDocument($doc);
        return $updated_doc;
      });

      $update->addDocuments($docs->toArray());
      $update->addCommit();
      $update->addOptimize();
      $this->_clearIndex($collection);
      $client->update($update);
    }
    catch(\Exception $ex)
    {
      $result = ['success' => false, 'error' => $ex];
    }

    return $result;
  }

  /**
   * Load solr client
   * @param string $collection
   * @return Client
   */
  private function _getClient(string $collection)
  {
    $config = business('solr_config');
    $config['endpoint']['localhost']['path'] .= $collection;
    $this->client = new Client($config);
    return $this->client;
  }

  /**
   * Clear the index of the collection
   * @param string $collection
   */
  private function _clearIndex(string $collection)
  {
    $config = business('solr_config');
    $curl = curl_init('http://localhost:' . $config['endpoint']['localhost']['port'] . '/solr/' . $collection . '/update?commit=true');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '<delete><query>*:*</query></delete>');
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
    curl_exec($curl);
  }
}