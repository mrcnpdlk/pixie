<?php

namespace Pecee\Pixie;

use Pecee\Pixie\QueryBuilder\QueryObject;

/**
 * Class Exception
 *
 * @package Pecee\Pixie
 */
class Exception extends \Exception
{

    protected $query;

    public function __construct(string $message = '', $code = 0, QueryObject $query = null)
    {
        parent::__construct($message, $code);
        $this->query = $query;
    }

    /**
     * Get query-object from last executed query.
     *
     * @return QueryObject|null
     */
    public function getQuery()
    {
        return $this->query;
    }

}
