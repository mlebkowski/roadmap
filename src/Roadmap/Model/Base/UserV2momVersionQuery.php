<?php

namespace Roadmap\Model\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Roadmap\Model\UserV2momVersion as ChildUserV2momVersion;
use Roadmap\Model\UserV2momVersionQuery as ChildUserV2momVersionQuery;
use Roadmap\Model\Map\UserV2momVersionTableMap;

/**
 * Base class that represents a query for the 'user_v2mom_version' table.
 *
 *
 *
 * @method     ChildUserV2momVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildUserV2momVersionQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method     ChildUserV2momVersionQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method     ChildUserV2momVersionQuery orderByVision($order = Criteria::ASC) Order by the vision column
 * @method     ChildUserV2momVersionQuery orderByValues($order = Criteria::ASC) Order by the values column
 * @method     ChildUserV2momVersionQuery orderByMethods($order = Criteria::ASC) Order by the methods column
 * @method     ChildUserV2momVersionQuery orderByObstacles($order = Criteria::ASC) Order by the obstacles column
 * @method     ChildUserV2momVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildUserV2momVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildUserV2momVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 *
 * @method     ChildUserV2momVersionQuery groupById() Group by the id column
 * @method     ChildUserV2momVersionQuery groupByUserId() Group by the user_id column
 * @method     ChildUserV2momVersionQuery groupByAccountId() Group by the account_id column
 * @method     ChildUserV2momVersionQuery groupByVision() Group by the vision column
 * @method     ChildUserV2momVersionQuery groupByValues() Group by the values column
 * @method     ChildUserV2momVersionQuery groupByMethods() Group by the methods column
 * @method     ChildUserV2momVersionQuery groupByObstacles() Group by the obstacles column
 * @method     ChildUserV2momVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildUserV2momVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildUserV2momVersionQuery groupByVersion() Group by the version column
 *
 * @method     ChildUserV2momVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildUserV2momVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildUserV2momVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildUserV2momVersionQuery leftJoinUserV2mom($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserV2mom relation
 * @method     ChildUserV2momVersionQuery rightJoinUserV2mom($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserV2mom relation
 * @method     ChildUserV2momVersionQuery innerJoinUserV2mom($relationAlias = null) Adds a INNER JOIN clause to the query using the UserV2mom relation
 *
 * @method     ChildUserV2momVersion findOne(ConnectionInterface $con = null) Return the first ChildUserV2momVersion matching the query
 * @method     ChildUserV2momVersion findOneOrCreate(ConnectionInterface $con = null) Return the first ChildUserV2momVersion matching the query, or a new ChildUserV2momVersion object populated from the query conditions when no match is found
 *
 * @method     ChildUserV2momVersion findOneById(int $id) Return the first ChildUserV2momVersion filtered by the id column
 * @method     ChildUserV2momVersion findOneByUserId(int $user_id) Return the first ChildUserV2momVersion filtered by the user_id column
 * @method     ChildUserV2momVersion findOneByAccountId(int $account_id) Return the first ChildUserV2momVersion filtered by the account_id column
 * @method     ChildUserV2momVersion findOneByVision(string $vision) Return the first ChildUserV2momVersion filtered by the vision column
 * @method     ChildUserV2momVersion findOneByValues(string $values) Return the first ChildUserV2momVersion filtered by the values column
 * @method     ChildUserV2momVersion findOneByMethods(string $methods) Return the first ChildUserV2momVersion filtered by the methods column
 * @method     ChildUserV2momVersion findOneByObstacles(string $obstacles) Return the first ChildUserV2momVersion filtered by the obstacles column
 * @method     ChildUserV2momVersion findOneByCreatedAt(string $created_at) Return the first ChildUserV2momVersion filtered by the created_at column
 * @method     ChildUserV2momVersion findOneByUpdatedAt(string $updated_at) Return the first ChildUserV2momVersion filtered by the updated_at column
 * @method     ChildUserV2momVersion findOneByVersion(int $version) Return the first ChildUserV2momVersion filtered by the version column
 *
 * @method     array findById(int $id) Return ChildUserV2momVersion objects filtered by the id column
 * @method     array findByUserId(int $user_id) Return ChildUserV2momVersion objects filtered by the user_id column
 * @method     array findByAccountId(int $account_id) Return ChildUserV2momVersion objects filtered by the account_id column
 * @method     array findByVision(string $vision) Return ChildUserV2momVersion objects filtered by the vision column
 * @method     array findByValues(string $values) Return ChildUserV2momVersion objects filtered by the values column
 * @method     array findByMethods(string $methods) Return ChildUserV2momVersion objects filtered by the methods column
 * @method     array findByObstacles(string $obstacles) Return ChildUserV2momVersion objects filtered by the obstacles column
 * @method     array findByCreatedAt(string $created_at) Return ChildUserV2momVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildUserV2momVersion objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildUserV2momVersion objects filtered by the version column
 *
 */
abstract class UserV2momVersionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Roadmap\Model\Base\UserV2momVersionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'roadmap', $modelName = '\\Roadmap\\Model\\UserV2momVersion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildUserV2momVersionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildUserV2momVersionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Roadmap\Model\UserV2momVersionQuery) {
            return $criteria;
        }
        $query = new \Roadmap\Model\UserV2momVersionQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$id, $version] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildUserV2momVersion|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserV2momVersionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UserV2momVersionTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildUserV2momVersion A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, USER_ID, ACCOUNT_ID, VISION, VALUES, METHODS, OBSTACLES, CREATED_AT, UPDATED_AT, VERSION FROM user_v2mom_version WHERE ID = :p0 AND VERSION = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildUserV2momVersion();
            $obj->hydrate($row);
            UserV2momVersionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildUserV2momVersion|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(UserV2momVersionTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(UserV2momVersionTableMap::VERSION, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(UserV2momVersionTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(UserV2momVersionTableMap::VERSION, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @see       filterByUserV2mom()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id > 12
     * </code>
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id > 12
     * </code>
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the vision column
     *
     * Example usage:
     * <code>
     * $query->filterByVision('fooValue');   // WHERE vision = 'fooValue'
     * $query->filterByVision('%fooValue%'); // WHERE vision LIKE '%fooValue%'
     * </code>
     *
     * @param     string $vision The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByVision($vision = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($vision)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $vision)) {
                $vision = str_replace('*', '%', $vision);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::VISION, $vision, $comparison);
    }

    /**
     * Filter the query on the values column
     *
     * Example usage:
     * <code>
     * $query->filterByValues('fooValue');   // WHERE values = 'fooValue'
     * $query->filterByValues('%fooValue%'); // WHERE values LIKE '%fooValue%'
     * </code>
     *
     * @param     string $values The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByValues($values = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($values)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $values)) {
                $values = str_replace('*', '%', $values);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::VALUES, $values, $comparison);
    }

    /**
     * Filter the query on the methods column
     *
     * Example usage:
     * <code>
     * $query->filterByMethods('fooValue');   // WHERE methods = 'fooValue'
     * $query->filterByMethods('%fooValue%'); // WHERE methods LIKE '%fooValue%'
     * </code>
     *
     * @param     string $methods The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByMethods($methods = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($methods)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $methods)) {
                $methods = str_replace('*', '%', $methods);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::METHODS, $methods, $comparison);
    }

    /**
     * Filter the query on the obstacles column
     *
     * Example usage:
     * <code>
     * $query->filterByObstacles('fooValue');   // WHERE obstacles = 'fooValue'
     * $query->filterByObstacles('%fooValue%'); // WHERE obstacles LIKE '%fooValue%'
     * </code>
     *
     * @param     string $obstacles The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByObstacles($obstacles = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($obstacles)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $obstacles)) {
                $obstacles = str_replace('*', '%', $obstacles);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::OBSTACLES, $obstacles, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query on the version column
     *
     * Example usage:
     * <code>
     * $query->filterByVersion(1234); // WHERE version = 1234
     * $query->filterByVersion(array(12, 34)); // WHERE version IN (12, 34)
     * $query->filterByVersion(array('min' => 12)); // WHERE version > 12
     * </code>
     *
     * @param     mixed $version The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(UserV2momVersionTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserV2momVersionTableMap::VERSION, $version, $comparison);
    }

    /**
     * Filter the query by a related \Roadmap\Model\UserV2mom object
     *
     * @param \Roadmap\Model\UserV2mom|ObjectCollection $userV2mom The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function filterByUserV2mom($userV2mom, $comparison = null)
    {
        if ($userV2mom instanceof \Roadmap\Model\UserV2mom) {
            return $this
                ->addUsingAlias(UserV2momVersionTableMap::ID, $userV2mom->getId(), $comparison);
        } elseif ($userV2mom instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserV2momVersionTableMap::ID, $userV2mom->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUserV2mom() only accepts arguments of type \Roadmap\Model\UserV2mom or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserV2mom relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function joinUserV2mom($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserV2mom');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'UserV2mom');
        }

        return $this;
    }

    /**
     * Use the UserV2mom relation UserV2mom object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Roadmap\Model\UserV2momQuery A secondary query class using the current class as primary query
     */
    public function useUserV2momQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserV2mom($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserV2mom', '\Roadmap\Model\UserV2momQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildUserV2momVersion $userV2momVersion Object to remove from the list of results
     *
     * @return ChildUserV2momVersionQuery The current query, for fluid interface
     */
    public function prune($userV2momVersion = null)
    {
        if ($userV2momVersion) {
            $this->addCond('pruneCond0', $this->getAliasedColName(UserV2momVersionTableMap::ID), $userV2momVersion->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(UserV2momVersionTableMap::VERSION), $userV2momVersion->getVersion(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the user_v2mom_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserV2momVersionTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            UserV2momVersionTableMap::clearInstancePool();
            UserV2momVersionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildUserV2momVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildUserV2momVersion object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserV2momVersionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(UserV2momVersionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        UserV2momVersionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            UserV2momVersionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // UserV2momVersionQuery
