<?php

namespace Roadmap\Model\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Roadmap\Model\UserV2momVersion;
use Roadmap\Model\UserV2momVersionQuery;


/**
 * This class defines the structure of the 'user_v2mom_version' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class UserV2momVersionTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Roadmap.Model.Map.UserV2momVersionTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'roadmap';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'user_v2mom_version';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Roadmap\\Model\\UserV2momVersion';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Roadmap.Model.UserV2momVersion';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 10;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 10;

    /**
     * the column name for the ID field
     */
    const ID = 'user_v2mom_version.ID';

    /**
     * the column name for the USER_ID field
     */
    const USER_ID = 'user_v2mom_version.USER_ID';

    /**
     * the column name for the ACCOUNT_ID field
     */
    const ACCOUNT_ID = 'user_v2mom_version.ACCOUNT_ID';

    /**
     * the column name for the VISION field
     */
    const VISION = 'user_v2mom_version.VISION';

    /**
     * the column name for the VALS field
     */
    const VALS = 'user_v2mom_version.VALS';

    /**
     * the column name for the METHODS field
     */
    const METHODS = 'user_v2mom_version.METHODS';

    /**
     * the column name for the OBSTACLES field
     */
    const OBSTACLES = 'user_v2mom_version.OBSTACLES';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'user_v2mom_version.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'user_v2mom_version.UPDATED_AT';

    /**
     * the column name for the VERSION field
     */
    const VERSION = 'user_v2mom_version.VERSION';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'UserId', 'AccountId', 'Vision', 'Values', 'Methods', 'Obstacles', 'CreatedAt', 'UpdatedAt', 'Version', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'userId', 'accountId', 'vision', 'values', 'methods', 'obstacles', 'createdAt', 'updatedAt', 'version', ),
        self::TYPE_COLNAME       => array(UserV2momVersionTableMap::ID, UserV2momVersionTableMap::USER_ID, UserV2momVersionTableMap::ACCOUNT_ID, UserV2momVersionTableMap::VISION, UserV2momVersionTableMap::VALS, UserV2momVersionTableMap::METHODS, UserV2momVersionTableMap::OBSTACLES, UserV2momVersionTableMap::CREATED_AT, UserV2momVersionTableMap::UPDATED_AT, UserV2momVersionTableMap::VERSION, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'USER_ID', 'ACCOUNT_ID', 'VISION', 'VALS', 'METHODS', 'OBSTACLES', 'CREATED_AT', 'UPDATED_AT', 'VERSION', ),
        self::TYPE_FIELDNAME     => array('id', 'user_id', 'account_id', 'vision', 'vals', 'methods', 'obstacles', 'created_at', 'updated_at', 'version', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'UserId' => 1, 'AccountId' => 2, 'Vision' => 3, 'Values' => 4, 'Methods' => 5, 'Obstacles' => 6, 'CreatedAt' => 7, 'UpdatedAt' => 8, 'Version' => 9, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'userId' => 1, 'accountId' => 2, 'vision' => 3, 'values' => 4, 'methods' => 5, 'obstacles' => 6, 'createdAt' => 7, 'updatedAt' => 8, 'version' => 9, ),
        self::TYPE_COLNAME       => array(UserV2momVersionTableMap::ID => 0, UserV2momVersionTableMap::USER_ID => 1, UserV2momVersionTableMap::ACCOUNT_ID => 2, UserV2momVersionTableMap::VISION => 3, UserV2momVersionTableMap::VALS => 4, UserV2momVersionTableMap::METHODS => 5, UserV2momVersionTableMap::OBSTACLES => 6, UserV2momVersionTableMap::CREATED_AT => 7, UserV2momVersionTableMap::UPDATED_AT => 8, UserV2momVersionTableMap::VERSION => 9, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'USER_ID' => 1, 'ACCOUNT_ID' => 2, 'VISION' => 3, 'VALS' => 4, 'METHODS' => 5, 'OBSTACLES' => 6, 'CREATED_AT' => 7, 'UPDATED_AT' => 8, 'VERSION' => 9, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'user_id' => 1, 'account_id' => 2, 'vision' => 3, 'vals' => 4, 'methods' => 5, 'obstacles' => 6, 'created_at' => 7, 'updated_at' => 8, 'version' => 9, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('user_v2mom_version');
        $this->setPhpName('UserV2momVersion');
        $this->setClassName('\\Roadmap\\Model\\UserV2momVersion');
        $this->setPackage('Roadmap.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('ID', 'Id', 'INTEGER' , 'user_v2mom', 'ID', true, null, null);
        $this->addColumn('USER_ID', 'UserId', 'INTEGER', true, null, null);
        $this->addColumn('ACCOUNT_ID', 'AccountId', 'INTEGER', true, null, null);
        $this->addColumn('VISION', 'Vision', 'CHAR', true, null, null);
        $this->addColumn('VALS', 'Values', 'CHAR', true, null, null);
        $this->addColumn('METHODS', 'Methods', 'CHAR', true, null, null);
        $this->addColumn('OBSTACLES', 'Obstacles', 'CHAR', true, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addPrimaryKey('VERSION', 'Version', 'INTEGER', true, null, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('UserV2mom', '\\Roadmap\\Model\\UserV2mom', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database. In some cases you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by find*()
     * and findPk*() calls.
     *
     * @param \Roadmap\Model\UserV2momVersion $obj A \Roadmap\Model\UserV2momVersion object.
     * @param string $key             (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (null === $key) {
                $key = serialize(array((string) $obj->getId(), (string) $obj->getVersion()));
            } // if key === null
            self::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param mixed $value A \Roadmap\Model\UserV2momVersion object or a primary key value.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && null !== $value) {
            if (is_object($value) && $value instanceof \Roadmap\Model\UserV2momVersion) {
                $key = serialize(array((string) $value->getId(), (string) $value->getVersion()));

            } elseif (is_array($value) && count($value) === 2) {
                // assume we've been passed a primary key";
                $key = serialize(array((string) $value[0], (string) $value[1]));
            } elseif ($value instanceof Criteria) {
                self::$instances = [];

                return;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or \Roadmap\Model\UserV2momVersion object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value, true)));
                throw $e;
            }

            unset(self::$instances[$key]);
        }
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null && $row[TableMap::TYPE_NUM == $indexType ? 9 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return serialize(array((string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], (string) $row[TableMap::TYPE_NUM == $indexType ? 9 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)]));
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return $pks;
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? UserV2momVersionTableMap::CLASS_DEFAULT : UserV2momVersionTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (UserV2momVersion object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = UserV2momVersionTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = UserV2momVersionTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + UserV2momVersionTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = UserV2momVersionTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            UserV2momVersionTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = UserV2momVersionTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = UserV2momVersionTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                UserV2momVersionTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(UserV2momVersionTableMap::ID);
            $criteria->addSelectColumn(UserV2momVersionTableMap::USER_ID);
            $criteria->addSelectColumn(UserV2momVersionTableMap::ACCOUNT_ID);
            $criteria->addSelectColumn(UserV2momVersionTableMap::VISION);
            $criteria->addSelectColumn(UserV2momVersionTableMap::VALS);
            $criteria->addSelectColumn(UserV2momVersionTableMap::METHODS);
            $criteria->addSelectColumn(UserV2momVersionTableMap::OBSTACLES);
            $criteria->addSelectColumn(UserV2momVersionTableMap::CREATED_AT);
            $criteria->addSelectColumn(UserV2momVersionTableMap::UPDATED_AT);
            $criteria->addSelectColumn(UserV2momVersionTableMap::VERSION);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.USER_ID');
            $criteria->addSelectColumn($alias . '.ACCOUNT_ID');
            $criteria->addSelectColumn($alias . '.VISION');
            $criteria->addSelectColumn($alias . '.VALS');
            $criteria->addSelectColumn($alias . '.METHODS');
            $criteria->addSelectColumn($alias . '.OBSTACLES');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
            $criteria->addSelectColumn($alias . '.VERSION');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(UserV2momVersionTableMap::DATABASE_NAME)->getTable(UserV2momVersionTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(UserV2momVersionTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(UserV2momVersionTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new UserV2momVersionTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a UserV2momVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or UserV2momVersion object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserV2momVersionTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Roadmap\Model\UserV2momVersion) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(UserV2momVersionTableMap::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(UserV2momVersionTableMap::ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(UserV2momVersionTableMap::VERSION, $value[1]));
                $criteria->addOr($criterion);
            }
        }

        $query = UserV2momVersionQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { UserV2momVersionTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { UserV2momVersionTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the user_v2mom_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return UserV2momVersionQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a UserV2momVersion or Criteria object.
     *
     * @param mixed               $criteria Criteria or UserV2momVersion object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserV2momVersionTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from UserV2momVersion object
        }


        // Set the correct dbName
        $query = UserV2momVersionQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // UserV2momVersionTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
UserV2momVersionTableMap::buildTableMap();
