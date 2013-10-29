<?php

namespace Roadmap\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use Roadmap\Model\Account as ChildAccount;
use Roadmap\Model\AccountQuery as ChildAccountQuery;
use Roadmap\Model\AccountUser as ChildAccountUser;
use Roadmap\Model\AccountUserQuery as ChildAccountUserQuery;
use Roadmap\Model\Project as ChildProject;
use Roadmap\Model\ProjectQuery as ChildProjectQuery;
use Roadmap\Model\User as ChildUser;
use Roadmap\Model\UserQuery as ChildUserQuery;
use Roadmap\Model\UserV2mom as ChildUserV2mom;
use Roadmap\Model\UserV2momQuery as ChildUserV2momQuery;
use Roadmap\Model\Map\AccountTableMap;

abstract class Account implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Roadmap\\Model\\Map\\AccountTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the avatar_url field.
     * @var        string
     */
    protected $avatar_url;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        ObjectCollection|ChildProject[] Collection to store aggregation of ChildProject objects.
     */
    protected $collProjects;
    protected $collProjectsPartial;

    /**
     * @var        ObjectCollection|ChildAccountUser[] Collection to store aggregation of ChildAccountUser objects.
     */
    protected $collAccountUsers;
    protected $collAccountUsersPartial;

    /**
     * @var        ObjectCollection|ChildUserV2mom[] Collection to store aggregation of ChildUserV2mom objects.
     */
    protected $collUserV2moms;
    protected $collUserV2momsPartial;

    /**
     * @var        ChildUser[] Collection to store aggregation of ChildUser objects.
     */
    protected $collUsers;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $usersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $projectsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $accountUsersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $userV2momsScheduledForDeletion = null;

    /**
     * Initializes internal state of Roadmap\Model\Base\Account object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !empty($this->modifiedColumns);
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return in_array($col, $this->modifiedColumns);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return array_unique($this->modifiedColumns);
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            while (false !== ($offset = array_search($col, $this->modifiedColumns))) {
                array_splice($this->modifiedColumns, $offset, 1);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Account</code> instance.  If
     * <code>obj</code> is an instance of <code>Account</code>, delegates to
     * <code>equals(Account)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Account The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Account The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [name] column value.
     *
     * @return   string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [avatar_url] column value.
     *
     * @return   string
     */
    public function getAvatarUrl()
    {

        return $this->avatar_url;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = AccountTableMap::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = AccountTableMap::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [avatar_url] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function setAvatarUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->avatar_url !== $v) {
            $this->avatar_url = $v;
            $this->modifiedColumns[] = AccountTableMap::AVATAR_URL;
        }


        return $this;
    } // setAvatarUrl()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[] = AccountTableMap::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[] = AccountTableMap::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : AccountTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : AccountTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : AccountTableMap::translateFieldName('AvatarUrl', TableMap::TYPE_PHPNAME, $indexType)];
            $this->avatar_url = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : AccountTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : AccountTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = AccountTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Roadmap\Model\Account object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AccountTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildAccountQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collProjects = null;

            $this->collAccountUsers = null;

            $this->collUserV2moms = null;

            $this->collUsers = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Account::setDeleted()
     * @see Account::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(AccountTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildAccountQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(AccountTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(AccountTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(AccountTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(AccountTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                AccountTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->usersScheduledForDeletion !== null) {
                if (!$this->usersScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk  = $this->getPrimaryKey();
                    foreach ($this->usersScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }

                    AccountUserQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->usersScheduledForDeletion = null;
                }

                foreach ($this->getUsers() as $user) {
                    if ($user->isModified()) {
                        $user->save($con);
                    }
                }
            } elseif ($this->collUsers) {
                foreach ($this->collUsers as $user) {
                    if ($user->isModified()) {
                        $user->save($con);
                    }
                }
            }

            if ($this->projectsScheduledForDeletion !== null) {
                if (!$this->projectsScheduledForDeletion->isEmpty()) {
                    \Roadmap\Model\ProjectQuery::create()
                        ->filterByPrimaryKeys($this->projectsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectsScheduledForDeletion = null;
                }
            }

                if ($this->collProjects !== null) {
            foreach ($this->collProjects as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->accountUsersScheduledForDeletion !== null) {
                if (!$this->accountUsersScheduledForDeletion->isEmpty()) {
                    \Roadmap\Model\AccountUserQuery::create()
                        ->filterByPrimaryKeys($this->accountUsersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountUsersScheduledForDeletion = null;
                }
            }

                if ($this->collAccountUsers !== null) {
            foreach ($this->collAccountUsers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userV2momsScheduledForDeletion !== null) {
                if (!$this->userV2momsScheduledForDeletion->isEmpty()) {
                    \Roadmap\Model\UserV2momQuery::create()
                        ->filterByPrimaryKeys($this->userV2momsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userV2momsScheduledForDeletion = null;
                }
            }

                if ($this->collUserV2moms !== null) {
            foreach ($this->collUserV2moms as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = AccountTableMap::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AccountTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AccountTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(AccountTableMap::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(AccountTableMap::AVATAR_URL)) {
            $modifiedColumns[':p' . $index++]  = 'AVATAR_URL';
        }
        if ($this->isColumnModified(AccountTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(AccountTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO account (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'NAME':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'AVATAR_URL':
                        $stmt->bindValue($identifier, $this->avatar_url, PDO::PARAM_STR);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = AccountTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getAvatarUrl();
                break;
            case 3:
                return $this->getCreatedAt();
                break;
            case 4:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Account'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Account'][$this->getPrimaryKey()] = true;
        $keys = AccountTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getAvatarUrl(),
            $keys[3] => $this->getCreatedAt(),
            $keys[4] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collProjects) {
                $result['Projects'] = $this->collProjects->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAccountUsers) {
                $result['AccountUsers'] = $this->collAccountUsers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserV2moms) {
                $result['UserV2moms'] = $this->collUserV2moms->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = AccountTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setAvatarUrl($value);
                break;
            case 3:
                $this->setCreatedAt($value);
                break;
            case 4:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = AccountTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setAvatarUrl($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCreatedAt($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setUpdatedAt($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AccountTableMap::DATABASE_NAME);

        if ($this->isColumnModified(AccountTableMap::ID)) $criteria->add(AccountTableMap::ID, $this->id);
        if ($this->isColumnModified(AccountTableMap::NAME)) $criteria->add(AccountTableMap::NAME, $this->name);
        if ($this->isColumnModified(AccountTableMap::AVATAR_URL)) $criteria->add(AccountTableMap::AVATAR_URL, $this->avatar_url);
        if ($this->isColumnModified(AccountTableMap::CREATED_AT)) $criteria->add(AccountTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(AccountTableMap::UPDATED_AT)) $criteria->add(AccountTableMap::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(AccountTableMap::DATABASE_NAME);
        $criteria->add(AccountTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Roadmap\Model\Account (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setAvatarUrl($this->getAvatarUrl());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getProjects() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProject($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAccountUsers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccountUser($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserV2moms() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserV2mom($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Roadmap\Model\Account Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Project' == $relationName) {
            return $this->initProjects();
        }
        if ('AccountUser' == $relationName) {
            return $this->initAccountUsers();
        }
        if ('UserV2mom' == $relationName) {
            return $this->initUserV2moms();
        }
    }

    /**
     * Clears out the collProjects collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProjects()
     */
    public function clearProjects()
    {
        $this->collProjects = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProjects collection loaded partially.
     */
    public function resetPartialProjects($v = true)
    {
        $this->collProjectsPartial = $v;
    }

    /**
     * Initializes the collProjects collection.
     *
     * By default this just sets the collProjects collection to an empty array (like clearcollProjects());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjects($overrideExisting = true)
    {
        if (null !== $this->collProjects && !$overrideExisting) {
            return;
        }
        $this->collProjects = new ObjectCollection();
        $this->collProjects->setModel('\Roadmap\Model\Project');
    }

    /**
     * Gets an array of ChildProject objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAccount is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProject[] List of ChildProject objects
     * @throws PropelException
     */
    public function getProjects($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectsPartial && !$this->isNew();
        if (null === $this->collProjects || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjects) {
                // return empty collection
                $this->initProjects();
            } else {
                $collProjects = ChildProjectQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProjectsPartial && count($collProjects)) {
                        $this->initProjects(false);

                        foreach ($collProjects as $obj) {
                            if (false == $this->collProjects->contains($obj)) {
                                $this->collProjects->append($obj);
                            }
                        }

                        $this->collProjectsPartial = true;
                    }

                    $collProjects->getInternalIterator()->rewind();

                    return $collProjects;
                }

                if ($partial && $this->collProjects) {
                    foreach ($this->collProjects as $obj) {
                        if ($obj->isNew()) {
                            $collProjects[] = $obj;
                        }
                    }
                }

                $this->collProjects = $collProjects;
                $this->collProjectsPartial = false;
            }
        }

        return $this->collProjects;
    }

    /**
     * Sets a collection of Project objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $projects A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAccount The current object (for fluent API support)
     */
    public function setProjects(Collection $projects, ConnectionInterface $con = null)
    {
        $projectsToDelete = $this->getProjects(new Criteria(), $con)->diff($projects);


        $this->projectsScheduledForDeletion = $projectsToDelete;

        foreach ($projectsToDelete as $projectRemoved) {
            $projectRemoved->setAccount(null);
        }

        $this->collProjects = null;
        foreach ($projects as $project) {
            $this->addProject($project);
        }

        $this->collProjects = $projects;
        $this->collProjectsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Project objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Project objects.
     * @throws PropelException
     */
    public function countProjects(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectsPartial && !$this->isNew();
        if (null === $this->collProjects || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjects) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProjects());
            }

            $query = ChildProjectQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collProjects);
    }

    /**
     * Method called to associate a ChildProject object to this object
     * through the ChildProject foreign key attribute.
     *
     * @param    ChildProject $l ChildProject
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function addProject(ChildProject $l)
    {
        if ($this->collProjects === null) {
            $this->initProjects();
            $this->collProjectsPartial = true;
        }

        if (!in_array($l, $this->collProjects->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProject($l);
        }

        return $this;
    }

    /**
     * @param Project $project The project object to add.
     */
    protected function doAddProject($project)
    {
        $this->collProjects[]= $project;
        $project->setAccount($this);
    }

    /**
     * @param  Project $project The project object to remove.
     * @return ChildAccount The current object (for fluent API support)
     */
    public function removeProject($project)
    {
        if ($this->getProjects()->contains($project)) {
            $this->collProjects->remove($this->collProjects->search($project));
            if (null === $this->projectsScheduledForDeletion) {
                $this->projectsScheduledForDeletion = clone $this->collProjects;
                $this->projectsScheduledForDeletion->clear();
            }
            $this->projectsScheduledForDeletion[]= clone $project;
            $project->setAccount(null);
        }

        return $this;
    }

    /**
     * Clears out the collAccountUsers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAccountUsers()
     */
    public function clearAccountUsers()
    {
        $this->collAccountUsers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAccountUsers collection loaded partially.
     */
    public function resetPartialAccountUsers($v = true)
    {
        $this->collAccountUsersPartial = $v;
    }

    /**
     * Initializes the collAccountUsers collection.
     *
     * By default this just sets the collAccountUsers collection to an empty array (like clearcollAccountUsers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountUsers($overrideExisting = true)
    {
        if (null !== $this->collAccountUsers && !$overrideExisting) {
            return;
        }
        $this->collAccountUsers = new ObjectCollection();
        $this->collAccountUsers->setModel('\Roadmap\Model\AccountUser');
    }

    /**
     * Gets an array of ChildAccountUser objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAccount is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildAccountUser[] List of ChildAccountUser objects
     * @throws PropelException
     */
    public function getAccountUsers($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAccountUsersPartial && !$this->isNew();
        if (null === $this->collAccountUsers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountUsers) {
                // return empty collection
                $this->initAccountUsers();
            } else {
                $collAccountUsers = ChildAccountUserQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAccountUsersPartial && count($collAccountUsers)) {
                        $this->initAccountUsers(false);

                        foreach ($collAccountUsers as $obj) {
                            if (false == $this->collAccountUsers->contains($obj)) {
                                $this->collAccountUsers->append($obj);
                            }
                        }

                        $this->collAccountUsersPartial = true;
                    }

                    $collAccountUsers->getInternalIterator()->rewind();

                    return $collAccountUsers;
                }

                if ($partial && $this->collAccountUsers) {
                    foreach ($this->collAccountUsers as $obj) {
                        if ($obj->isNew()) {
                            $collAccountUsers[] = $obj;
                        }
                    }
                }

                $this->collAccountUsers = $collAccountUsers;
                $this->collAccountUsersPartial = false;
            }
        }

        return $this->collAccountUsers;
    }

    /**
     * Sets a collection of AccountUser objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $accountUsers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAccount The current object (for fluent API support)
     */
    public function setAccountUsers(Collection $accountUsers, ConnectionInterface $con = null)
    {
        $accountUsersToDelete = $this->getAccountUsers(new Criteria(), $con)->diff($accountUsers);


        $this->accountUsersScheduledForDeletion = $accountUsersToDelete;

        foreach ($accountUsersToDelete as $accountUserRemoved) {
            $accountUserRemoved->setAccount(null);
        }

        $this->collAccountUsers = null;
        foreach ($accountUsers as $accountUser) {
            $this->addAccountUser($accountUser);
        }

        $this->collAccountUsers = $accountUsers;
        $this->collAccountUsersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AccountUser objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related AccountUser objects.
     * @throws PropelException
     */
    public function countAccountUsers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAccountUsersPartial && !$this->isNew();
        if (null === $this->collAccountUsers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountUsers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountUsers());
            }

            $query = ChildAccountUserQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collAccountUsers);
    }

    /**
     * Method called to associate a ChildAccountUser object to this object
     * through the ChildAccountUser foreign key attribute.
     *
     * @param    ChildAccountUser $l ChildAccountUser
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function addAccountUser(ChildAccountUser $l)
    {
        if ($this->collAccountUsers === null) {
            $this->initAccountUsers();
            $this->collAccountUsersPartial = true;
        }

        if (!in_array($l, $this->collAccountUsers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccountUser($l);
        }

        return $this;
    }

    /**
     * @param AccountUser $accountUser The accountUser object to add.
     */
    protected function doAddAccountUser($accountUser)
    {
        $this->collAccountUsers[]= $accountUser;
        $accountUser->setAccount($this);
    }

    /**
     * @param  AccountUser $accountUser The accountUser object to remove.
     * @return ChildAccount The current object (for fluent API support)
     */
    public function removeAccountUser($accountUser)
    {
        if ($this->getAccountUsers()->contains($accountUser)) {
            $this->collAccountUsers->remove($this->collAccountUsers->search($accountUser));
            if (null === $this->accountUsersScheduledForDeletion) {
                $this->accountUsersScheduledForDeletion = clone $this->collAccountUsers;
                $this->accountUsersScheduledForDeletion->clear();
            }
            $this->accountUsersScheduledForDeletion[]= clone $accountUser;
            $accountUser->setAccount(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related AccountUsers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildAccountUser[] List of ChildAccountUser objects
     */
    public function getAccountUsersJoinUser($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildAccountUserQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getAccountUsers($query, $con);
    }

    /**
     * Clears out the collUserV2moms collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserV2moms()
     */
    public function clearUserV2moms()
    {
        $this->collUserV2moms = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserV2moms collection loaded partially.
     */
    public function resetPartialUserV2moms($v = true)
    {
        $this->collUserV2momsPartial = $v;
    }

    /**
     * Initializes the collUserV2moms collection.
     *
     * By default this just sets the collUserV2moms collection to an empty array (like clearcollUserV2moms());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserV2moms($overrideExisting = true)
    {
        if (null !== $this->collUserV2moms && !$overrideExisting) {
            return;
        }
        $this->collUserV2moms = new ObjectCollection();
        $this->collUserV2moms->setModel('\Roadmap\Model\UserV2mom');
    }

    /**
     * Gets an array of ChildUserV2mom objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAccount is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUserV2mom[] List of ChildUserV2mom objects
     * @throws PropelException
     */
    public function getUserV2moms($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserV2momsPartial && !$this->isNew();
        if (null === $this->collUserV2moms || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserV2moms) {
                // return empty collection
                $this->initUserV2moms();
            } else {
                $collUserV2moms = ChildUserV2momQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserV2momsPartial && count($collUserV2moms)) {
                        $this->initUserV2moms(false);

                        foreach ($collUserV2moms as $obj) {
                            if (false == $this->collUserV2moms->contains($obj)) {
                                $this->collUserV2moms->append($obj);
                            }
                        }

                        $this->collUserV2momsPartial = true;
                    }

                    $collUserV2moms->getInternalIterator()->rewind();

                    return $collUserV2moms;
                }

                if ($partial && $this->collUserV2moms) {
                    foreach ($this->collUserV2moms as $obj) {
                        if ($obj->isNew()) {
                            $collUserV2moms[] = $obj;
                        }
                    }
                }

                $this->collUserV2moms = $collUserV2moms;
                $this->collUserV2momsPartial = false;
            }
        }

        return $this->collUserV2moms;
    }

    /**
     * Sets a collection of UserV2mom objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userV2moms A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAccount The current object (for fluent API support)
     */
    public function setUserV2moms(Collection $userV2moms, ConnectionInterface $con = null)
    {
        $userV2momsToDelete = $this->getUserV2moms(new Criteria(), $con)->diff($userV2moms);


        $this->userV2momsScheduledForDeletion = $userV2momsToDelete;

        foreach ($userV2momsToDelete as $userV2momRemoved) {
            $userV2momRemoved->setAccount(null);
        }

        $this->collUserV2moms = null;
        foreach ($userV2moms as $userV2mom) {
            $this->addUserV2mom($userV2mom);
        }

        $this->collUserV2moms = $userV2moms;
        $this->collUserV2momsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserV2mom objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserV2mom objects.
     * @throws PropelException
     */
    public function countUserV2moms(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserV2momsPartial && !$this->isNew();
        if (null === $this->collUserV2moms || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserV2moms) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserV2moms());
            }

            $query = ChildUserV2momQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collUserV2moms);
    }

    /**
     * Method called to associate a ChildUserV2mom object to this object
     * through the ChildUserV2mom foreign key attribute.
     *
     * @param    ChildUserV2mom $l ChildUserV2mom
     * @return   \Roadmap\Model\Account The current object (for fluent API support)
     */
    public function addUserV2mom(ChildUserV2mom $l)
    {
        if ($this->collUserV2moms === null) {
            $this->initUserV2moms();
            $this->collUserV2momsPartial = true;
        }

        if (!in_array($l, $this->collUserV2moms->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserV2mom($l);
        }

        return $this;
    }

    /**
     * @param UserV2mom $userV2mom The userV2mom object to add.
     */
    protected function doAddUserV2mom($userV2mom)
    {
        $this->collUserV2moms[]= $userV2mom;
        $userV2mom->setAccount($this);
    }

    /**
     * @param  UserV2mom $userV2mom The userV2mom object to remove.
     * @return ChildAccount The current object (for fluent API support)
     */
    public function removeUserV2mom($userV2mom)
    {
        if ($this->getUserV2moms()->contains($userV2mom)) {
            $this->collUserV2moms->remove($this->collUserV2moms->search($userV2mom));
            if (null === $this->userV2momsScheduledForDeletion) {
                $this->userV2momsScheduledForDeletion = clone $this->collUserV2moms;
                $this->userV2momsScheduledForDeletion->clear();
            }
            $this->userV2momsScheduledForDeletion[]= clone $userV2mom;
            $userV2mom->setAccount(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related UserV2moms from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserV2mom[] List of ChildUserV2mom objects
     */
    public function getUserV2momsJoinUser($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildUserV2momQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getUserV2moms($query, $con);
    }

    /**
     * Clears out the collUsers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUsers()
     */
    public function clearUsers()
    {
        $this->collUsers = null; // important to set this to NULL since that means it is uninitialized
        $this->collUsersPartial = null;
    }

    /**
     * Initializes the collUsers collection.
     *
     * By default this just sets the collUsers collection to an empty collection (like clearUsers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initUsers()
    {
        $this->collUsers = new ObjectCollection();
        $this->collUsers->setModel('\Roadmap\Model\User');
    }

    /**
     * Gets a collection of ChildUser objects related by a many-to-many relationship
     * to the current object by way of the account_user cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAccount is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildUser[] List of ChildUser objects
     */
    public function getUsers($criteria = null, ConnectionInterface $con = null)
    {
        if (null === $this->collUsers || null !== $criteria) {
            if ($this->isNew() && null === $this->collUsers) {
                // return empty collection
                $this->initUsers();
            } else {
                $collUsers = ChildUserQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collUsers;
                }
                $this->collUsers = $collUsers;
            }
        }

        return $this->collUsers;
    }

    /**
     * Sets a collection of User objects related by a many-to-many relationship
     * to the current object by way of the account_user cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $users A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return ChildAccount The current object (for fluent API support)
     */
    public function setUsers(Collection $users, ConnectionInterface $con = null)
    {
        $this->clearUsers();
        $currentUsers = $this->getUsers();

        $this->usersScheduledForDeletion = $currentUsers->diff($users);

        foreach ($users as $user) {
            if (!$currentUsers->contains($user)) {
                $this->doAddUser($user);
            }
        }

        $this->collUsers = $users;

        return $this;
    }

    /**
     * Gets the number of ChildUser objects related by a many-to-many relationship
     * to the current object by way of the account_user cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related ChildUser objects
     */
    public function countUsers($criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        if (null === $this->collUsers || null !== $criteria) {
            if ($this->isNew() && null === $this->collUsers) {
                return 0;
            } else {
                $query = ChildUserQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByAccount($this)
                    ->count($con);
            }
        } else {
            return count($this->collUsers);
        }
    }

    /**
     * Associate a ChildUser object to this object
     * through the account_user cross reference table.
     *
     * @param  ChildUser $user The ChildAccountUser object to relate
     * @return ChildAccount The current object (for fluent API support)
     */
    public function addUser(ChildUser $user)
    {
        if ($this->collUsers === null) {
            $this->initUsers();
        }

        if (!$this->collUsers->contains($user)) { // only add it if the **same** object is not already associated
            $this->doAddUser($user);
            $this->collUsers[] = $user;
        }

        return $this;
    }

    /**
     * @param    User $user The user object to add.
     */
    protected function doAddUser($user)
    {
        $accountUser = new ChildAccountUser();
        $accountUser->setUser($user);
        $this->addAccountUser($accountUser);
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$user->getAccounts()->contains($this)) {
            $foreignCollection   = $user->getAccounts();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a ChildUser object to this object
     * through the account_user cross reference table.
     *
     * @param ChildUser $user The ChildAccountUser object to relate
     * @return ChildAccount The current object (for fluent API support)
     */
    public function removeUser(ChildUser $user)
    {
        if ($this->getUsers()->contains($user)) {
            $this->collUsers->remove($this->collUsers->search($user));

            if (null === $this->usersScheduledForDeletion) {
                $this->usersScheduledForDeletion = clone $this->collUsers;
                $this->usersScheduledForDeletion->clear();
            }

            $this->usersScheduledForDeletion[] = $user;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->avatar_url = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collProjects) {
                foreach ($this->collProjects as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAccountUsers) {
                foreach ($this->collAccountUsers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserV2moms) {
                foreach ($this->collUserV2moms as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUsers) {
                foreach ($this->collUsers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collProjects instanceof Collection) {
            $this->collProjects->clearIterator();
        }
        $this->collProjects = null;
        if ($this->collAccountUsers instanceof Collection) {
            $this->collAccountUsers->clearIterator();
        }
        $this->collAccountUsers = null;
        if ($this->collUserV2moms instanceof Collection) {
            $this->collUserV2moms->clearIterator();
        }
        $this->collUserV2moms = null;
        if ($this->collUsers instanceof Collection) {
            $this->collUsers->clearIterator();
        }
        $this->collUsers = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AccountTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildAccount The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = AccountTableMap::UPDATED_AT;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
