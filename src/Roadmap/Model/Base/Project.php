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
use Roadmap\Model\Project as ChildProject;
use Roadmap\Model\ProjectActivity as ChildProjectActivity;
use Roadmap\Model\ProjectActivityQuery as ChildProjectActivityQuery;
use Roadmap\Model\ProjectQuery as ChildProjectQuery;
use Roadmap\Model\ProjectUser as ChildProjectUser;
use Roadmap\Model\ProjectUserQuery as ChildProjectUserQuery;
use Roadmap\Model\User as ChildUser;
use Roadmap\Model\UserQuery as ChildUserQuery;
use Roadmap\Model\Map\ProjectTableMap;

abstract class Project implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Roadmap\\Model\\Map\\ProjectTableMap';


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
     * The value for the account_id field.
     * @var        int
     */
    protected $account_id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the slug field.
     * @var        string
     */
    protected $slug;

    /**
     * The value for the state field.
     * Note: this column has a database default value of: 'new'
     * @var        string
     */
    protected $state;

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
     * @var        Account
     */
    protected $aAccount;

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
    protected $projectUsersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $projectActivitiesScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->state = 'new';
    }

    /**
     * Initializes internal state of Roadmap\Model\Base\Project object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
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
     * Compares this with another <code>Project</code> instance.  If
     * <code>obj</code> is an instance of <code>Project</code>, delegates to
     * <code>equals(Project)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Project The current object, for fluid interface
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
     * @return Project The current object, for fluid interface
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
     * Get the [account_id] column value.
     *
     * @return   int
     */
    public function getAccountId()
    {

        return $this->account_id;
    }

    /**
     * Get the [title] column value.
     *
     * @return   string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [slug] column value.
     *
     * @return   string
     */
    public function getSlug()
    {

        return $this->slug;
    }

    /**
     * Get the [state] column value.
     *
     * @return   string
     */
    public function getState()
    {

        return $this->state;
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
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ProjectTableMap::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [account_id] column.
     *
     * @param      int $v new value
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setAccountId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->account_id !== $v) {
            $this->account_id = $v;
            $this->modifiedColumns[] = ProjectTableMap::ACCOUNT_ID;
        }

        if ($this->aAccount !== null && $this->aAccount->getId() !== $v) {
            $this->aAccount = null;
        }


        return $this;
    } // setAccountId()

    /**
     * Set the value of [title] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = ProjectTableMap::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [slug] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setSlug($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slug !== $v) {
            $this->slug = $v;
            $this->modifiedColumns[] = ProjectTableMap::SLUG;
        }


        return $this;
    } // setSlug()

    /**
     * Set the value of [state] column.
     *
     * @param      string $v new value
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setState($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->state !== $v) {
            $this->state = $v;
            $this->modifiedColumns[] = ProjectTableMap::STATE;
        }


        return $this;
    } // setState()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[] = ProjectTableMap::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[] = ProjectTableMap::UPDATED_AT;
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
            if ($this->state !== 'new') {
                return false;
            }

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ProjectTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ProjectTableMap::translateFieldName('AccountId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->account_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ProjectTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ProjectTableMap::translateFieldName('Slug', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slug = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ProjectTableMap::translateFieldName('State', TableMap::TYPE_PHPNAME, $indexType)];
            $this->state = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ProjectTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ProjectTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = ProjectTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Roadmap\Model\Project object", 0, $e);
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
        if ($this->aAccount !== null && $this->account_id !== $this->aAccount->getId()) {
            $this->aAccount = null;
        }
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
            $con = Propel::getServiceContainer()->getReadConnection(ProjectTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildProjectQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAccount = null;
            $this->collProjectUsers = null;

            $this->collProjectActivities = null;

            $this->collUsers = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Project::setDeleted()
     * @see Project::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildProjectQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // sluggable behavior

            if ($this->isColumnModified(ProjectTableMap::SLUG) && $this->getSlug()) {
                $this->setSlug($this->makeSlugUnique($this->getSlug()));
            } elseif (!$this->getSlug()) {
                $this->setSlug($this->createSlug());
            }
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(ProjectTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ProjectTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ProjectTableMap::UPDATED_AT)) {
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
                ProjectTableMap::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAccount !== null) {
                if ($this->aAccount->isModified() || $this->aAccount->isNew()) {
                    $affectedRows += $this->aAccount->save($con);
                }
                $this->setAccount($this->aAccount);
            }

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

                    ProjectUserQuery::create()
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

        $this->modifiedColumns[] = ProjectTableMap::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjectTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjectTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(ProjectTableMap::ACCOUNT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ACCOUNT_ID';
        }
        if ($this->isColumnModified(ProjectTableMap::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE';
        }
        if ($this->isColumnModified(ProjectTableMap::SLUG)) {
            $modifiedColumns[':p' . $index++]  = 'SLUG';
        }
        if ($this->isColumnModified(ProjectTableMap::STATE)) {
            $modifiedColumns[':p' . $index++]  = 'STATE';
        }
        if ($this->isColumnModified(ProjectTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(ProjectTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO project (%s) VALUES (%s)',
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
                    case 'ACCOUNT_ID':
                        $stmt->bindValue($identifier, $this->account_id, PDO::PARAM_INT);
                        break;
                    case 'TITLE':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'SLUG':
                        $stmt->bindValue($identifier, $this->slug, PDO::PARAM_STR);
                        break;
                    case 'STATE':
                        $stmt->bindValue($identifier, $this->state, PDO::PARAM_STR);
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
        $pos = ProjectTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getAccountId();
                break;
            case 2:
                return $this->getTitle();
                break;
            case 3:
                return $this->getSlug();
                break;
            case 4:
                return $this->getState();
                break;
            case 5:
                return $this->getCreatedAt();
                break;
            case 6:
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
        if (isset($alreadyDumpedObjects['Project'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Project'][$this->getPrimaryKey()] = true;
        $keys = ProjectTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getAccountId(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getSlug(),
            $keys[4] => $this->getState(),
            $keys[5] => $this->getCreatedAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aAccount) {
                $result['Account'] = $this->aAccount->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collProjectUsers) {
                $result['ProjectUsers'] = $this->collProjectUsers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProjectActivities) {
                $result['ProjectActivities'] = $this->collProjectActivities->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ProjectTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setAccountId($value);
                break;
            case 2:
                $this->setTitle($value);
                break;
            case 3:
                $this->setSlug($value);
                break;
            case 4:
                $this->setState($value);
                break;
            case 5:
                $this->setCreatedAt($value);
                break;
            case 6:
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
        $keys = ProjectTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setAccountId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSlug($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setState($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCreatedAt($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUpdatedAt($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProjectTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ProjectTableMap::ID)) $criteria->add(ProjectTableMap::ID, $this->id);
        if ($this->isColumnModified(ProjectTableMap::ACCOUNT_ID)) $criteria->add(ProjectTableMap::ACCOUNT_ID, $this->account_id);
        if ($this->isColumnModified(ProjectTableMap::TITLE)) $criteria->add(ProjectTableMap::TITLE, $this->title);
        if ($this->isColumnModified(ProjectTableMap::SLUG)) $criteria->add(ProjectTableMap::SLUG, $this->slug);
        if ($this->isColumnModified(ProjectTableMap::STATE)) $criteria->add(ProjectTableMap::STATE, $this->state);
        if ($this->isColumnModified(ProjectTableMap::CREATED_AT)) $criteria->add(ProjectTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(ProjectTableMap::UPDATED_AT)) $criteria->add(ProjectTableMap::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(ProjectTableMap::DATABASE_NAME);
        $criteria->add(ProjectTableMap::ID, $this->id);

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
     * @param      object $copyObj An object of \Roadmap\Model\Project (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setAccountId($this->getAccountId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setSlug($this->getSlug());
        $copyObj->setState($this->getState());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

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
     * @return                 \Roadmap\Model\Project Clone of current object.
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
     * Declares an association between this object and a ChildAccount object.
     *
     * @param                  ChildAccount $v
     * @return                 \Roadmap\Model\Project The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccount(ChildAccount $v = null)
    {
        if ($v === null) {
            $this->setAccountId(NULL);
        } else {
            $this->setAccountId($v->getId());
        }

        $this->aAccount = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildAccount object, it will not be re-added.
        if ($v !== null) {
            $v->addProject($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildAccount object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildAccount The associated ChildAccount object.
     * @throws PropelException
     */
    public function getAccount(ConnectionInterface $con = null)
    {
        if ($this->aAccount === null && ($this->account_id !== null)) {
            $this->aAccount = ChildAccountQuery::create()->findPk($this->account_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccount->addProjects($this);
             */
        }

        return $this->aAccount;
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
     * If this ChildProject is new, it will return
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
                    ->filterByProject($this)
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
     * @return   ChildProject The current object (for fluent API support)
     */
    public function setProjectUsers(Collection $projectUsers, ConnectionInterface $con = null)
    {
        $projectUsersToDelete = $this->getProjectUsers(new Criteria(), $con)->diff($projectUsers);


        $this->projectUsersScheduledForDeletion = $projectUsersToDelete;

        foreach ($projectUsersToDelete as $projectUserRemoved) {
            $projectUserRemoved->setProject(null);
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
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collProjectUsers);
    }

    /**
     * Method called to associate a ChildProjectUser object to this object
     * through the ChildProjectUser foreign key attribute.
     *
     * @param    ChildProjectUser $l ChildProjectUser
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
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
        $projectUser->setProject($this);
    }

    /**
     * @param  ProjectUser $projectUser The projectUser object to remove.
     * @return ChildProject The current object (for fluent API support)
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
            $projectUser->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related ProjectUsers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProjectUser[] List of ChildProjectUser objects
     */
    public function getProjectUsersJoinUser($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProjectUserQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

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
     * If this ChildProject is new, it will return
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
                    ->filterByProject($this)
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
     * @return   ChildProject The current object (for fluent API support)
     */
    public function setProjectActivities(Collection $projectActivities, ConnectionInterface $con = null)
    {
        $projectActivitiesToDelete = $this->getProjectActivities(new Criteria(), $con)->diff($projectActivities);


        $this->projectActivitiesScheduledForDeletion = $projectActivitiesToDelete;

        foreach ($projectActivitiesToDelete as $projectActivityRemoved) {
            $projectActivityRemoved->setProject(null);
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
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collProjectActivities);
    }

    /**
     * Method called to associate a ChildProjectActivity object to this object
     * through the ChildProjectActivity foreign key attribute.
     *
     * @param    ChildProjectActivity $l ChildProjectActivity
     * @return   \Roadmap\Model\Project The current object (for fluent API support)
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
        $projectActivity->setProject($this);
    }

    /**
     * @param  ProjectActivity $projectActivity The projectActivity object to remove.
     * @return ChildProject The current object (for fluent API support)
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
            $projectActivity->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related ProjectActivities from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProjectActivity[] List of ChildProjectActivity objects
     */
    public function getProjectActivitiesJoinUser($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProjectActivityQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getProjectActivities($query, $con);
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
     * to the current object by way of the project_user cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProject is new, it will return
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
                    ->filterByProject($this)
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
     * to the current object by way of the project_user cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $users A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return ChildProject The current object (for fluent API support)
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
     * to the current object by way of the project_user cross-reference table.
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
                    ->filterByProject($this)
                    ->count($con);
            }
        } else {
            return count($this->collUsers);
        }
    }

    /**
     * Associate a ChildUser object to this object
     * through the project_user cross reference table.
     *
     * @param  ChildUser $user The ChildProjectUser object to relate
     * @return ChildProject The current object (for fluent API support)
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
        $projectUser = new ChildProjectUser();
        $projectUser->setUser($user);
        $this->addProjectUser($projectUser);
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$user->getProjects()->contains($this)) {
            $foreignCollection   = $user->getProjects();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a ChildUser object to this object
     * through the project_user cross reference table.
     *
     * @param ChildUser $user The ChildProjectUser object to relate
     * @return ChildProject The current object (for fluent API support)
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
        $this->account_id = null;
        $this->title = null;
        $this->slug = null;
        $this->state = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
            if ($this->collUsers) {
                foreach ($this->collUsers as $o) {
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
        if ($this->collUsers instanceof Collection) {
            $this->collUsers->clearIterator();
        }
        $this->collUsers = null;
        $this->aAccount = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProjectTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildProject The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = ProjectTableMap::UPDATED_AT;

        return $this;
    }

    // sluggable behavior

    /**
     * Create a unique slug based on the object
     *
     * @return string The object slug
     */
    protected function createSlug()
    {
        $slug = $this->createRawSlug();
        $slug = $this->limitSlugSize($slug);
        $slug = $this->makeSlugUnique($slug);

        return $slug;
    }

    /**
     * Create the slug from the appropriate columns
     *
     * @return string
     */
    protected function createRawSlug()
    {
        return '' . $this->cleanupSlugPart($this->getTitle()) . '';
    }

    /**
     * Cleanup a string to make a slug of it
     * Removes special characters, replaces blanks with a separator, and trim it
     *
     * @param     string $slug        the text to slugify
     * @param     string $replacement the separator used by slug
     * @return    string               the slugified text
     */
    protected static function cleanupSlugPart($slug, $replacement = '-')
    {
        // transliterate
        if (function_exists('iconv')) {
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }

        // lowercase
        if (function_exists('mb_strtolower')) {
            $slug = mb_strtolower($slug);
        } else {
            $slug = strtolower($slug);
        }

        // remove accents resulting from OSX's iconv
        $slug = str_replace(array('\'', '`', '^'), '', $slug);

        // replace non letter or digits with separator
        $slug = preg_replace('/\W+/', $replacement, $slug);

        // trim
        $slug = trim($slug, $replacement);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }


    /**
     * Make sure the slug is short enough to accommodate the column size
     *
     * @param    string $slug            the slug to check
     *
     * @return string                        the truncated slug
     */
    protected static function limitSlugSize($slug, $incrementReservedSpace = 3)
    {
        // check length, as suffix could put it over maximum
        if (strlen($slug) > (128 - $incrementReservedSpace)) {
            $slug = substr($slug, 0, 128 - $incrementReservedSpace);
        }

        return $slug;
    }


    /**
     * Get the slug, ensuring its uniqueness
     *
     * @param    string $slug            the slug to check
     * @param    string $separator the separator used by slug
     * @return string                        the unique slug
     */
    protected function makeSlugUnique($slug, $separator = '-', $increment = 0)
    {
        $slug2 = empty($increment) ? $slug : $slug . $separator . $increment;
        $slugAlreadyExists = ChildProjectQuery::create()
            ->filterBySlug($slug2)
            ->prune($this)
            ->count();
        if ($slugAlreadyExists) {
            return $this->makeSlugUnique($slug, $separator, ++$increment);
        } else {
            return $slug2;
        }
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
