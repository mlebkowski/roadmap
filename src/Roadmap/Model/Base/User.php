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
use Roadmap\Model\Project as ChildProject;
use Roadmap\Model\ProjectActivity as ChildProjectActivity;
use Roadmap\Model\ProjectActivityQuery as ChildProjectActivityQuery;
use Roadmap\Model\ProjectQuery as ChildProjectQuery;
use Roadmap\Model\ProjectUser as ChildProjectUser;
use Roadmap\Model\ProjectUserQuery as ChildProjectUserQuery;
use Roadmap\Model\User as ChildUser;
use Roadmap\Model\UserQuery as ChildUserQuery;
use Roadmap\Model\UserV2mom as ChildUserV2mom;
use Roadmap\Model\UserV2momQuery as ChildUserV2momQuery;
use Roadmap\Model\Map\UserTableMap;

abstract class User implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Roadmap\\Model\\Map\\UserTableMap';


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
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the picture field.
     * @var        string
     */
    protected $picture;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * @var        ObjectCollection|ChildProjectUser[] Collection to store aggregation of ChildProjectUser objects.
     */
    protected $collProjectUsers;
    protected $collProjectUsersPartial;

    /**
     * @var        ObjectCollection|ChildProjectActivity[] Collection to store aggregation of ChildProjectActivity objects.
     */
    protected $collProjectActivities;
    protected $collProjectActivitiesPartial;

    /**
     * @var        ObjectCollection|ChildUserV2mom[] Collection to store aggregation of ChildUserV2mom objects.
     */
    protected $collUserV2moms;
    protected $collUserV2momsPartial;

    /**
     * @var        ChildProject[] Collection to store aggregation of ChildProject objects.
     */
    protected $collProjects;

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
    protected $projectsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $projectUsersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $projectActivitiesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $userV2momsScheduledForDeletion = null;

    /**
     * Initializes internal state of Roadmap\Model\Base\User object.
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
     * Compares this with another <code>User</code> instance.  If
     * <code>obj</code> is an instance of <code>User</code>, delegates to
     * <code>equals(User)</code>.  Otherwise, returns <code>false</code>.
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
     * @return User The current object, for fluid interface
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
     * @return User The current object, for fluid interface
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
     * Get the [email] column value.
     *
     * @return   string
     */
    public function getEmail()
    {

        return $this->email;
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
     * Get the [picture] column value.
     *
     * @return   string
     */
    public function getPicture()
    {

        return $this->picture;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
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
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = UserTableMap::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [email] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = UserTableMap::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [name] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = UserTableMap::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [picture] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function setPicture($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->picture !== $v) {
            $this->picture = $v;
            $this->modifiedColumns[] = UserTableMap::PICTURE;
        }


        return $this;
    } // setPicture()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[] = UserTableMap::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : UserTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : UserTableMap::translateFieldName('Email', TableMap::TYPE_PHPNAME, $indexType)];
            $this->email = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : UserTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : UserTableMap::translateFieldName('Picture', TableMap::TYPE_PHPNAME, $indexType)];
            $this->picture = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : UserTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = UserTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Roadmap\Model\User object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(UserTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildUserQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collProjectUsers = null;

            $this->collProjectActivities = null;

            $this->collUserV2moms = null;

            $this->collProjects = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see User::setDeleted()
     * @see User::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildUserQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UserTableMap::addInstanceToPool($this);
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

            if ($this->projectsScheduledForDeletion !== null) {
                if (!$this->projectsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk  = $this->getPrimaryKey();
                    foreach ($this->projectsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }

                    ProjectUserQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->projectsScheduledForDeletion = null;
                }

                foreach ($this->getProjects() as $project) {
                    if ($project->isModified()) {
                        $project->save($con);
                    }
                }
            } elseif ($this->collProjects) {
                foreach ($this->collProjects as $project) {
                    if ($project->isModified()) {
                        $project->save($con);
                    }
                }
            }

            if ($this->projectUsersScheduledForDeletion !== null) {
                if (!$this->projectUsersScheduledForDeletion->isEmpty()) {
                    \Roadmap\Model\ProjectUserQuery::create()
                        ->filterByPrimaryKeys($this->projectUsersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectUsersScheduledForDeletion = null;
                }
            }

                if ($this->collProjectUsers !== null) {
            foreach ($this->collProjectUsers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->projectActivitiesScheduledForDeletion !== null) {
                if (!$this->projectActivitiesScheduledForDeletion->isEmpty()) {
                    \Roadmap\Model\ProjectActivityQuery::create()
                        ->filterByPrimaryKeys($this->projectActivitiesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectActivitiesScheduledForDeletion = null;
                }
            }

                if ($this->collProjectActivities !== null) {
            foreach ($this->collProjectActivities as $referrerFK) {
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

        $this->modifiedColumns[] = UserTableMap::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(UserTableMap::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'EMAIL';
        }
        if ($this->isColumnModified(UserTableMap::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(UserTableMap::PICTURE)) {
            $modifiedColumns[':p' . $index++]  = 'PICTURE';
        }
        if ($this->isColumnModified(UserTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO user (%s) VALUES (%s)',
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
                    case 'EMAIL':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'NAME':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'PICTURE':
                        $stmt->bindValue($identifier, $this->picture, PDO::PARAM_STR);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
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
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getEmail();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getPicture();
                break;
            case 4:
                return $this->getCreatedAt();
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
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getEmail(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getPicture(),
            $keys[4] => $this->getCreatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collProjectUsers) {
                $result['ProjectUsers'] = $this->collProjectUsers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProjectActivities) {
                $result['ProjectActivities'] = $this->collProjectActivities->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setEmail($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setPicture($value);
                break;
            case 4:
                $this->setCreatedAt($value);
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
        $keys = UserTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setEmail($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPicture($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCreatedAt($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserTableMap::DATABASE_NAME);

        if ($this->isColumnModified(UserTableMap::ID)) $criteria->add(UserTableMap::ID, $this->id);
        if ($this->isColumnModified(UserTableMap::EMAIL)) $criteria->add(UserTableMap::EMAIL, $this->email);
        if ($this->isColumnModified(UserTableMap::NAME)) $criteria->add(UserTableMap::NAME, $this->name);
        if ($this->isColumnModified(UserTableMap::PICTURE)) $criteria->add(UserTableMap::PICTURE, $this->picture);
        if ($this->isColumnModified(UserTableMap::CREATED_AT)) $criteria->add(UserTableMap::CREATED_AT, $this->created_at);

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
        $criteria = new Criteria(UserTableMap::DATABASE_NAME);
        $criteria->add(UserTableMap::ID, $this->id);

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
     * @param      object $copyObj An object of \Roadmap\Model\User (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setEmail($this->getEmail());
        $copyObj->setName($this->getName());
        $copyObj->setPicture($this->getPicture());
        $copyObj->setCreatedAt($this->getCreatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getProjectUsers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProjectUser($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProjectActivities() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProjectActivity($relObj->copy($deepCopy));
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
     * @return                 \Roadmap\Model\User Clone of current object.
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
        if ('ProjectUser' == $relationName) {
            return $this->initProjectUsers();
        }
        if ('ProjectActivity' == $relationName) {
            return $this->initProjectActivities();
        }
        if ('UserV2mom' == $relationName) {
            return $this->initUserV2moms();
        }
    }

    /**
     * Clears out the collProjectUsers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProjectUsers()
     */
    public function clearProjectUsers()
    {
        $this->collProjectUsers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProjectUsers collection loaded partially.
     */
    public function resetPartialProjectUsers($v = true)
    {
        $this->collProjectUsersPartial = $v;
    }

    /**
     * Initializes the collProjectUsers collection.
     *
     * By default this just sets the collProjectUsers collection to an empty array (like clearcollProjectUsers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjectUsers($overrideExisting = true)
    {
        if (null !== $this->collProjectUsers && !$overrideExisting) {
            return;
        }
        $this->collProjectUsers = new ObjectCollection();
        $this->collProjectUsers->setModel('\Roadmap\Model\ProjectUser');
    }

    /**
     * Gets an array of ChildProjectUser objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProjectUser[] List of ChildProjectUser objects
     * @throws PropelException
     */
    public function getProjectUsers($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectUsersPartial && !$this->isNew();
        if (null === $this->collProjectUsers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjectUsers) {
                // return empty collection
                $this->initProjectUsers();
            } else {
                $collProjectUsers = ChildProjectUserQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProjectUsersPartial && count($collProjectUsers)) {
                        $this->initProjectUsers(false);

                        foreach ($collProjectUsers as $obj) {
                            if (false == $this->collProjectUsers->contains($obj)) {
                                $this->collProjectUsers->append($obj);
                            }
                        }

                        $this->collProjectUsersPartial = true;
                    }

                    $collProjectUsers->getInternalIterator()->rewind();

                    return $collProjectUsers;
                }

                if ($partial && $this->collProjectUsers) {
                    foreach ($this->collProjectUsers as $obj) {
                        if ($obj->isNew()) {
                            $collProjectUsers[] = $obj;
                        }
                    }
                }

                $this->collProjectUsers = $collProjectUsers;
                $this->collProjectUsersPartial = false;
            }
        }

        return $this->collProjectUsers;
    }

    /**
     * Sets a collection of ProjectUser objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $projectUsers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildUser The current object (for fluent API support)
     */
    public function setProjectUsers(Collection $projectUsers, ConnectionInterface $con = null)
    {
        $projectUsersToDelete = $this->getProjectUsers(new Criteria(), $con)->diff($projectUsers);


        $this->projectUsersScheduledForDeletion = $projectUsersToDelete;

        foreach ($projectUsersToDelete as $projectUserRemoved) {
            $projectUserRemoved->setUser(null);
        }

        $this->collProjectUsers = null;
        foreach ($projectUsers as $projectUser) {
            $this->addProjectUser($projectUser);
        }

        $this->collProjectUsers = $projectUsers;
        $this->collProjectUsersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProjectUser objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProjectUser objects.
     * @throws PropelException
     */
    public function countProjectUsers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectUsersPartial && !$this->isNew();
        if (null === $this->collProjectUsers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjectUsers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProjectUsers());
            }

            $query = ChildProjectUserQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collProjectUsers);
    }

    /**
     * Method called to associate a ChildProjectUser object to this object
     * through the ChildProjectUser foreign key attribute.
     *
     * @param    ChildProjectUser $l ChildProjectUser
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function addProjectUser(ChildProjectUser $l)
    {
        if ($this->collProjectUsers === null) {
            $this->initProjectUsers();
            $this->collProjectUsersPartial = true;
        }

        if (!in_array($l, $this->collProjectUsers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProjectUser($l);
        }

        return $this;
    }

    /**
     * @param ProjectUser $projectUser The projectUser object to add.
     */
    protected function doAddProjectUser($projectUser)
    {
        $this->collProjectUsers[]= $projectUser;
        $projectUser->setUser($this);
    }

    /**
     * @param  ProjectUser $projectUser The projectUser object to remove.
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeProjectUser($projectUser)
    {
        if ($this->getProjectUsers()->contains($projectUser)) {
            $this->collProjectUsers->remove($this->collProjectUsers->search($projectUser));
            if (null === $this->projectUsersScheduledForDeletion) {
                $this->projectUsersScheduledForDeletion = clone $this->collProjectUsers;
                $this->projectUsersScheduledForDeletion->clear();
            }
            $this->projectUsersScheduledForDeletion[]= clone $projectUser;
            $projectUser->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related ProjectUsers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProjectUser[] List of ChildProjectUser objects
     */
    public function getProjectUsersJoinProject($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProjectUserQuery::create(null, $criteria);
        $query->joinWith('Project', $joinBehavior);

        return $this->getProjectUsers($query, $con);
    }

    /**
     * Clears out the collProjectActivities collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProjectActivities()
     */
    public function clearProjectActivities()
    {
        $this->collProjectActivities = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProjectActivities collection loaded partially.
     */
    public function resetPartialProjectActivities($v = true)
    {
        $this->collProjectActivitiesPartial = $v;
    }

    /**
     * Initializes the collProjectActivities collection.
     *
     * By default this just sets the collProjectActivities collection to an empty array (like clearcollProjectActivities());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjectActivities($overrideExisting = true)
    {
        if (null !== $this->collProjectActivities && !$overrideExisting) {
            return;
        }
        $this->collProjectActivities = new ObjectCollection();
        $this->collProjectActivities->setModel('\Roadmap\Model\ProjectActivity');
    }

    /**
     * Gets an array of ChildProjectActivity objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProjectActivity[] List of ChildProjectActivity objects
     * @throws PropelException
     */
    public function getProjectActivities($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectActivitiesPartial && !$this->isNew();
        if (null === $this->collProjectActivities || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjectActivities) {
                // return empty collection
                $this->initProjectActivities();
            } else {
                $collProjectActivities = ChildProjectActivityQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProjectActivitiesPartial && count($collProjectActivities)) {
                        $this->initProjectActivities(false);

                        foreach ($collProjectActivities as $obj) {
                            if (false == $this->collProjectActivities->contains($obj)) {
                                $this->collProjectActivities->append($obj);
                            }
                        }

                        $this->collProjectActivitiesPartial = true;
                    }

                    $collProjectActivities->getInternalIterator()->rewind();

                    return $collProjectActivities;
                }

                if ($partial && $this->collProjectActivities) {
                    foreach ($this->collProjectActivities as $obj) {
                        if ($obj->isNew()) {
                            $collProjectActivities[] = $obj;
                        }
                    }
                }

                $this->collProjectActivities = $collProjectActivities;
                $this->collProjectActivitiesPartial = false;
            }
        }

        return $this->collProjectActivities;
    }

    /**
     * Sets a collection of ProjectActivity objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $projectActivities A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildUser The current object (for fluent API support)
     */
    public function setProjectActivities(Collection $projectActivities, ConnectionInterface $con = null)
    {
        $projectActivitiesToDelete = $this->getProjectActivities(new Criteria(), $con)->diff($projectActivities);


        $this->projectActivitiesScheduledForDeletion = $projectActivitiesToDelete;

        foreach ($projectActivitiesToDelete as $projectActivityRemoved) {
            $projectActivityRemoved->setUser(null);
        }

        $this->collProjectActivities = null;
        foreach ($projectActivities as $projectActivity) {
            $this->addProjectActivity($projectActivity);
        }

        $this->collProjectActivities = $projectActivities;
        $this->collProjectActivitiesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProjectActivity objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProjectActivity objects.
     * @throws PropelException
     */
    public function countProjectActivities(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectActivitiesPartial && !$this->isNew();
        if (null === $this->collProjectActivities || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjectActivities) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProjectActivities());
            }

            $query = ChildProjectActivityQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collProjectActivities);
    }

    /**
     * Method called to associate a ChildProjectActivity object to this object
     * through the ChildProjectActivity foreign key attribute.
     *
     * @param    ChildProjectActivity $l ChildProjectActivity
     * @return   \Roadmap\Model\User The current object (for fluent API support)
     */
    public function addProjectActivity(ChildProjectActivity $l)
    {
        if ($this->collProjectActivities === null) {
            $this->initProjectActivities();
            $this->collProjectActivitiesPartial = true;
        }

        if (!in_array($l, $this->collProjectActivities->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProjectActivity($l);
        }

        return $this;
    }

    /**
     * @param ProjectActivity $projectActivity The projectActivity object to add.
     */
    protected function doAddProjectActivity($projectActivity)
    {
        $this->collProjectActivities[]= $projectActivity;
        $projectActivity->setUser($this);
    }

    /**
     * @param  ProjectActivity $projectActivity The projectActivity object to remove.
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeProjectActivity($projectActivity)
    {
        if ($this->getProjectActivities()->contains($projectActivity)) {
            $this->collProjectActivities->remove($this->collProjectActivities->search($projectActivity));
            if (null === $this->projectActivitiesScheduledForDeletion) {
                $this->projectActivitiesScheduledForDeletion = clone $this->collProjectActivities;
                $this->projectActivitiesScheduledForDeletion->clear();
            }
            $this->projectActivitiesScheduledForDeletion[]= clone $projectActivity;
            $projectActivity->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related ProjectActivities from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProjectActivity[] List of ChildProjectActivity objects
     */
    public function getProjectActivitiesJoinProject($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProjectActivityQuery::create(null, $criteria);
        $query->joinWith('Project', $joinBehavior);

        return $this->getProjectActivities($query, $con);
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
     * If this ChildUser is new, it will return
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
                    ->filterByUser($this)
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
     * @return   ChildUser The current object (for fluent API support)
     */
    public function setUserV2moms(Collection $userV2moms, ConnectionInterface $con = null)
    {
        $userV2momsToDelete = $this->getUserV2moms(new Criteria(), $con)->diff($userV2moms);


        $this->userV2momsScheduledForDeletion = $userV2momsToDelete;

        foreach ($userV2momsToDelete as $userV2momRemoved) {
            $userV2momRemoved->setUser(null);
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
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserV2moms);
    }

    /**
     * Method called to associate a ChildUserV2mom object to this object
     * through the ChildUserV2mom foreign key attribute.
     *
     * @param    ChildUserV2mom $l ChildUserV2mom
     * @return   \Roadmap\Model\User The current object (for fluent API support)
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
        $userV2mom->setUser($this);
    }

    /**
     * @param  UserV2mom $userV2mom The userV2mom object to remove.
     * @return ChildUser The current object (for fluent API support)
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
            $userV2mom->setUser(null);
        }

        return $this;
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
        $this->collProjectsPartial = null;
    }

    /**
     * Initializes the collProjects collection.
     *
     * By default this just sets the collProjects collection to an empty collection (like clearProjects());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initProjects()
    {
        $this->collProjects = new ObjectCollection();
        $this->collProjects->setModel('\Roadmap\Model\Project');
    }

    /**
     * Gets a collection of ChildProject objects related by a many-to-many relationship
     * to the current object by way of the project_user cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildProject[] List of ChildProject objects
     */
    public function getProjects($criteria = null, ConnectionInterface $con = null)
    {
        if (null === $this->collProjects || null !== $criteria) {
            if ($this->isNew() && null === $this->collProjects) {
                // return empty collection
                $this->initProjects();
            } else {
                $collProjects = ChildProjectQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collProjects;
                }
                $this->collProjects = $collProjects;
            }
        }

        return $this->collProjects;
    }

    /**
     * Sets a collection of Project objects related by a many-to-many relationship
     * to the current object by way of the project_user cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $projects A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return ChildUser The current object (for fluent API support)
     */
    public function setProjects(Collection $projects, ConnectionInterface $con = null)
    {
        $this->clearProjects();
        $currentProjects = $this->getProjects();

        $this->projectsScheduledForDeletion = $currentProjects->diff($projects);

        foreach ($projects as $project) {
            if (!$currentProjects->contains($project)) {
                $this->doAddProject($project);
            }
        }

        $this->collProjects = $projects;

        return $this;
    }

    /**
     * Gets the number of ChildProject objects related by a many-to-many relationship
     * to the current object by way of the project_user cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related ChildProject objects
     */
    public function countProjects($criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        if (null === $this->collProjects || null !== $criteria) {
            if ($this->isNew() && null === $this->collProjects) {
                return 0;
            } else {
                $query = ChildProjectQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collProjects);
        }
    }

    /**
     * Associate a ChildProject object to this object
     * through the project_user cross reference table.
     *
     * @param  ChildProject $project The ChildProjectUser object to relate
     * @return ChildUser The current object (for fluent API support)
     */
    public function addProject(ChildProject $project)
    {
        if ($this->collProjects === null) {
            $this->initProjects();
        }

        if (!$this->collProjects->contains($project)) { // only add it if the **same** object is not already associated
            $this->doAddProject($project);
            $this->collProjects[] = $project;
        }

        return $this;
    }

    /**
     * @param    Project $project The project object to add.
     */
    protected function doAddProject($project)
    {
        $projectUser = new ChildProjectUser();
        $projectUser->setProject($project);
        $this->addProjectUser($projectUser);
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$project->getUsers()->contains($this)) {
            $foreignCollection   = $project->getUsers();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a ChildProject object to this object
     * through the project_user cross reference table.
     *
     * @param ChildProject $project The ChildProjectUser object to relate
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeProject(ChildProject $project)
    {
        if ($this->getProjects()->contains($project)) {
            $this->collProjects->remove($this->collProjects->search($project));

            if (null === $this->projectsScheduledForDeletion) {
                $this->projectsScheduledForDeletion = clone $this->collProjects;
                $this->projectsScheduledForDeletion->clear();
            }

            $this->projectsScheduledForDeletion[] = $project;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->email = null;
        $this->name = null;
        $this->picture = null;
        $this->created_at = null;
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
            if ($this->collProjectUsers) {
                foreach ($this->collProjectUsers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProjectActivities) {
                foreach ($this->collProjectActivities as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserV2moms) {
                foreach ($this->collUserV2moms as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProjects) {
                foreach ($this->collProjects as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collProjectUsers instanceof Collection) {
            $this->collProjectUsers->clearIterator();
        }
        $this->collProjectUsers = null;
        if ($this->collProjectActivities instanceof Collection) {
            $this->collProjectActivities->clearIterator();
        }
        $this->collProjectActivities = null;
        if ($this->collUserV2moms instanceof Collection) {
            $this->collUserV2moms->clearIterator();
        }
        $this->collUserV2moms = null;
        if ($this->collProjects instanceof Collection) {
            $this->collProjects->clearIterator();
        }
        $this->collProjects = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserTableMap::DEFAULT_STRING_FORMAT);
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
