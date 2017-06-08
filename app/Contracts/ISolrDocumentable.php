<?php

namespace App\Contracts;

use Solarium\QueryType\Update\Query\Document\DocumentInterface;

/**
 * Interface ISolrDocumentable
 * @package App\Contracts
 */
interface ISolrDocumentable
{
  /**
   * Converts model to a Solr document
   * @param DocumentInterface $doc
   * @return DocumentInterface
   */
  public function toSolrDocument(DocumentInterface $doc): DocumentInterface;
}