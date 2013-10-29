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
use Roadmap\Model\Project as ChildProject;
use Roadmap\Model\ProjectQuery as ChildProjectQuery;
use Roadmap\Model\Map\ProjectTableMap;

/**
 * Base class that represents a query for the 'project' table.
 *
 *
 *
 * @method     ChildProjectQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProjectQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method     ChildProjectQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildProjectQuery orderBySlug($order = Criteria::ASC) Order by the slug column
 * @method     ChildProjectQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method     ChildProjectQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildProjectQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildProjectQuery groupById() Group by the id column
 * @method     ChildProjectQuery groupByAccountId() Group by the account_id column
 * @method     ChildProjectQuery groupByTitle() Group by the title column
 * @method     ChildProjectQuery groupBySlug() Group by the slug column
 * @method     ChildProjectQuery groupByState() Group by the state column
 * @method     ChildProjectQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildProjectQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildProjectQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProjectQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProjectQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProjectQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method     ChildProjectQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method     ChildProjectQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method     ChildProjectQuery leftJoinProjectUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProjectUser relation
 * @method     ChildProjectQuery rightJoinProjectUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProjectUser relation
 * @method     ChildProjectQuery innerJoinProjectUser($relationAlias = null) Adds a INNER JOIN clause to the query using the ProjectUser relation
 *
 * @method     ChildProjectQuery leftJoinProjectActivity($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProjectActivity relation
 * @method     ChildProjectQuery rightJoinProjectActivity($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProjectActivity relation
 * @method     ChildProjectQuery innerJoinProjectActivity($relationAlias = null) Adds a INNER JOIN clause to the query using the ProjectActivity relation
 *
 * @method     ChildProject findOne(ConnectionInterface $con = null) Return the first ChildProject matching the query
 * @method     ChildProject findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProject matching the query, or a new ChildProject object populated from the query conditions when no match is found
 *
 * @method     ChildProject findOneById(int $id) Return the first ChildProject filtered by the id column
 * @method     ChildProject findOneByAccountId(int $account_id) Return the first ChildProject filtered by the account_id column
 * @method     ChildProject findOneByTitle(string $title) Return the first ChildProject filtered by the title column
 * @method     ChildProject findOneBySlug(string $slug) Return the first ChildProject filtered by the slug column
 * @method     ChildProject findOneByState(string $state) Return the first ChildProject filtered by the state column
 * @method     ChildProject findOneByCreatedAt(string $created_at) Return the first ChildProject filtered by the created_at column
 * @method     ChildProject findOneByUpdatedAt(string $updated_at) Return the first ChildProject filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildProject objects filtered by the id column
 * @method     array findByAccountId(int $account_id) Return ChildProject objects filtered by the account_id column
 * @method     array findByTitle(string $title) Return ChildProject objects filtered by the title column
 * @method     array findBySlug(string $slug) Return ChildProject objects filtered by the slug column
 * @method     array findByState(string $state) Return ChildProject objects filtered by the state column
 * @method     array findByCreatedAt(string $created_at) Return ChildProject objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildProject objects filtered by the updated_at column
 *
 */
abstract class ProjectQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Roadmap\Model\Base\ProjectQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'roadmap', $modelName = '\\Roadmap\\Model\\Project', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProjectQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProjectQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Roadmap\Model\ProjectQuery) {
            return $criteria;
        }
        $query = new \Roadmap\Model\ProjectQuery();
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
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildProject|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProjectTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProjectTableMap::DATABASE_NAME);
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
     * @return   ChildProject A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ACCOUNT_ID, TITLE, SLUG, STATE, CREATED_AT, UPDATED_AT FROM project WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildProject();
            $obj->hydrate($row);
            ProjectTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProject|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(12, 56, 832), $con);
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
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProjectTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProjectTableMap::ID, $keys, Criteria::IN);
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
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProjectTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProjectTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::ID, $id, $comparison);
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
     * @see       filterByAccount()
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(ProjectTableMap::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(ProjectTableMap::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the slug column
     *
     * Example usage:
     * <code>
     * $query->filterBySlug('fooValue');   // WHERE slug = 'fooValue'
     * $query->filterBySlug('%fooValue%'); // WHERE slug LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slug The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterBySlug($slug = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slug)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $slug)) {
                $slug = str_replace('*', '%', $slug);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::SLUG, $slug, $comparison);
    }

    /**
     * Filter the query on the state column
     *
     * Example usage:
     * <code>
     * $query->filterByState('fooValue');   // WHERE state = 'fooValue'
     * $query->filterByState('%fooValue%'); // WHERE state LIKE '%fooValue%'
     * </code>
     *
     * @param     string $state The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByState($state = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($state)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $state)) {
                $state = str_replace('*', '%', $state);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::STATE, $state, $comparison);
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
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ProjectTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ProjectTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ProjectTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ProjectTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProjectTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Roadmap\Model\Account object
     *
     * @param \Roadmap\Model\Account|ObjectCollection $account The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof \Roadmap\Model\Account) {
            return $this
                ->addUsingAlias(ProjectTableMap::ACCOUNT_ID, $account->getId(), $comparison);
        } elseif ($account instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProjectTableMap::ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAccount() only accepts arguments of type \Roadmap\Model\Account or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Account relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function joinAccount($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Account');

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
            $this->addJoinObject($join, 'Account');
        }

        return $this;
    }

    /**
     * Use the Account relation Account object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Roadmap\Model\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Account', '\Roadmap\Model\AccountQuery');
    }

    /**
     * Filter the query by a related \Roadmap\Model\ProjectUser object
     *
     * @param \Roadmap\Model\ProjectUser|ObjectCollection $projectUser  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByProjectUser($projectUser, $comparison = null)
    {
        if ($projectUser instanceof \Roadmap\Model\ProjectUser) {
            return $this
                ->addUsingAlias(ProjectTableMap::ID, $projectUser->getProjectId(), $comparison);
        } elseif ($projectUser instanceof ObjectCollection) {
            return $this
                ->useProjectUserQuery()
                ->filterByPrimaryKeys($projectUser->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProjectUser() only accepts arguments of type \Roadmap\Model\ProjectUser or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProjectUser relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function joinProjectUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProjectUser');

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
            $this->addJoinObject($join, 'ProjectUser');
        }

        return $this;
    }

    /**
     * Use the ProjectUser relation ProjectUser object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Roadmap\Model\ProjectUserQuery A secondary query class using the current class as primary query
     */
    public function useProjectUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProjectUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProjectUser', '\Roadmap\Model\ProjectUserQuery');
    }

    /**
     * Filter the query by a related \Roadmap\Model\ProjectActivity object
     *
     * @param \Roadmap\Model\ProjectActivity|ObjectCollection $projectActivity  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByProjectActivity($projectActivity, $comparison = null)
    {
        if ($projectActivity instanceof \Roadmap\Model\ProjectActivity) {
            return $this
                ->addUsingAlias(ProjectTableMap::ID, $projectActivity->getProjectId(), $comparison);
        } elseif ($projectActivity instanceof ObjectCollection) {
            return $this
                ->useProjectActivityQuery()
                ->filterByPrimaryKeys($projectActivity->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProjectActivity() only accepts arguments of type \Roadmap\Model\ProjectActivity or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProjectActivity relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function joinProjectActivity($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProjectActivity');

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
            $this->addJoinObject($join, 'ProjectActivity');
        }

        return $this;
    }

    /**
     * Use the ProjectActivity relation ProjectActivity object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Roadmap\Model\ProjectActivityQuery A secondary query class using the current class as primary query
     */
    public function useProjectActivityQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProjectActivity($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProjectActivity', '\Roadmap\Model\ProjectActivityQuery');
    }

    /**
     * Filter the query by a related User object
     * using the project_user table as cross reference
     *
     * @param User $user the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useProjectUserQuery()
            ->filterByUser($user, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProject $project Object to remove from the list of results
     *
     * @return ChildProjectQuery The current query, for fluid interface
     */
    public function prune($project = null)
    {
        if ($project) {
            $this->addUsingAlias(ProjectTableMap::ID, $project->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the project table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
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
            ProjectTableMap::clearInstancePool();
            ProjectTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProject or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProject object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProjectTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        ProjectTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ProjectTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildProjectQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ProjectTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildProjectQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ProjectTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildProjectQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProjectTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildProjectQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProjectTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildProjectQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProjectTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildProjectQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProjectTableMap::CREATED_AT);
    }

    // sluggable behavior

    /**
     * Find one object based on its slug
     *
     * @param     string $slug The value to use as filter.
     * @param     ConnectionInterface $con The optional connection object
     *
     * @return    ChildProject the result, formatted by the current formatter
     */
    public function findOneBySlug($slug, $con = null)
    {
        return $this->filterBySlug($slug)->findOne($con);
    }

} // ProjectQuery
