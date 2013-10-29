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
use Roadmap\Model\UserV2mom;
use Roadmap\Model\UserV2momQuery;


/**
 * This class defines the structure of the 'user_v2mom' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class UserV2momTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Roadmap.Model.Map.UserV2momTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'roadmap';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'user_v2mom';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Roadmap\\Model\\UserV2mom';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Roadmap.Model.UserV2mom';

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
    const ID = 'user_v2mom.ID';

    /**
     * the column name for the USER_ID field
     */
    const USER_ID = 'user_v2mom.USER_ID';

    /**
     * the column name for the ACCOUNT_ID field
     */
    const ACCOUNT_ID = 'user_v2mom.ACCOUNT_ID';

    /**
     * the column name for the VISION field
     */
    const VISION = 'user_v2mom.VISION';

    /**
     * the column name for the VALS field
     */
    const VALS = 'user_v2mom.VALS';

    /**
     * the column name for the METHODS field
     */
    const METHODS = 'user_v2mom.METHODS';

    /**
     * the column name for the OBSTACLES field
     */
    const OBSTACLES = 'user_v2mom.OBSTACLES';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'user_v2mom.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'user_v2mom.UPDATED_AT';

    /**
     * the column name for the VERSION field
     */
    const VERSION = 'user_v2mom.VERSION';

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
        self::TYPE_COLNAME       => array(UserV2momTableMap::ID, UserV2momTableMap::USER_ID, UserV2momTableMap::ACCOUNT_ID, UserV2momTableMap::VISION, UserV2momTableMap::VALS, UserV2momTableMap::METHODS, UserV2momTableMap::OBSTACLES, UserV2momTableMap::CREATED_AT, UserV2momTableMap::UPDATED_AT, UserV2momTableMap::VERSION, ),
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
        self::TYPE_COLNAME       => array(UserV2momTableMap::ID => 0, UserV2momTableMap::USER_ID => 1, UserV2momTableMap::ACCOUNT_ID => 2, UserV2momTableMap::VISION => 3, UserV2momTableMap::VALS => 4, UserV2momTableMap::METHODS => 5, UserV2momTableMap::OBSTACLES => 6, UserV2momTableMap::CREATED_AT => 7, UserV2momTableMap::UPDATED_AT => 8, UserV2momTableMap::VERSION => 9, ),
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
        $this->setName('user_v2mom');
        $this->setPhpName('UserV2mom');
        $this->setClassName('\\Roadmap\\Model\\UserV2mom');
        $this->setPackage('Roadmap.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('USER_ID', 'UserId', 'INTEGER', 'user', 'ID', true, null, null);
        $this->addForeignKey('ACCOUNT_ID', 'AccountId', 'INTEGER', 'account', 'ID', true, null, null);
        $this->addColumn('VISION', 'Vision', 'CHAR', true, null, null);
        $this->addColumn('VALS', 'Values', 'CHAR', true, null, null);
        $this->addColumn('METHODS', 'Methods', 'CHAR', true, null, null);
        $this->addColumn('OBSTACLES', 'Obstacles', 'CHAR', true, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('VERSION', 'Version', 'INTEGER', false, null, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', '\\Roadmap\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), null, null);
        $this->addRelation('Account', '\\Roadmap\\Model\\Account', RelationMap::MANY_TO_ONE, array('account_id' => 'id', ), null, null);
        $this->addRelation('UserV2momVersion', '\\Roadmap\\Model\\UserV2momVersion', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'UserV2momVersions');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'versionable' => array('version_column' => 'version', 'version_table' => '', 'log_created_at' => 'false', 'log_created_by' => 'false', 'log_comment' => 'false', 'version_created_at_column' => 'version_created_at', 'version_created_by_column' => 'version_created_by', 'version_comment_column' => 'version_comment', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to user_v2mom     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                UserV2momVersionTableMap::clearInstancePool();
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
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
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

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
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
        return $withPrefix ? UserV2momTableMap::CLASS_DEFAULT : UserV2momTableMap::OM_CLASS;
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
     * @return array (UserV2mom object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = UserV2momTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = UserV2momTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + UserV2momTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = UserV2momTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            UserV2momTableMap::addInstanceToPool($obj, $key);
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
            $key = UserV2momTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = UserV2momTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                UserV2momTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(UserV2momTableMap::ID);
            $criteria->addSelectColumn(UserV2momTableMap::USER_ID);
            $criteria->addSelectColumn(UserV2momTableMap::ACCOUNT_ID);
            $criteria->addSelectColumn(UserV2momTableMap::VISION);
            $criteria->addSelectColumn(UserV2momTableMap::VALS);
            $criteria->addSelectColumn(UserV2momTableMap::METHODS);
            $criteria->addSelectColumn(UserV2momTableMap::OBSTACLES);
            $criteria->addSelectColumn(UserV2momTableMap::CREATED_AT);
            $criteria->addSelectColumn(UserV2momTableMap::UPDATED_AT);
            $criteria->addSelectColumn(UserV2momTableMap::VERSION);
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
        return Propel::getServiceContainer()->getDatabaseMap(UserV2momTableMap::DATABASE_NAME)->getTable(UserV2momTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(UserV2momTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(UserV2momTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new UserV2momTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a UserV2mom or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or UserV2mom object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(UserV2momTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Roadmap\Model\UserV2mom) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(UserV2momTableMap::DATABASE_NAME);
            $criteria->add(UserV2momTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = UserV2momQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { UserV2momTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { UserV2momTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the user_v2mom table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return UserV2momQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a UserV2mom or Criteria object.
     *
     * @param mixed               $criteria Criteria or UserV2mom object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserV2momTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from UserV2mom object
        }

        if ($criteria->containsKey(UserV2momTableMap::ID) && $criteria->keyContainsValue(UserV2momTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.UserV2momTableMap::ID.')');
        }


        // Set the correct dbName
        $query = UserV2momQuery::create()->mergeWith($criteria);

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

} // UserV2momTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
UserV2momTableMap::buildTableMap();
