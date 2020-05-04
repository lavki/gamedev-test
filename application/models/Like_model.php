<?php

/**
 * Class Like_model
 */
class Like_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'likes';

    /** @var int */
    protected $user_id;
    /** @var int */
    protected $relation_id;
    /** @var string*/
    protected $type;

    /** @var string */
    protected $time_created;

    // generated
    protected $user;

    /**
     * @return int
     */
    public function get_user_id(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function set_user_id(int $user_id)
    {
        $this->user_id = $user_id;
        return $this->save('user_id', $user_id);
    }

    /**
     * @return int
     */
    public function get_relation_id(): int
    {
        return $this->relation_id;
    }

    /**
     * @param int $relation_id
     * @return bool
     */
    public function set_relation_id(int $relation_id)
    {
        $this->relation_id = $relation_id;
        return $this->save('relation_id', $relation_id);
    }

    /**
     * @return string
     */
    public function get_type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function set_type(string $type)
    {
        $this->type = $type;
        return $this->save('type', $type);
    }

    /**
     * @return string
     */
    public function get_time_created(): string
    {
        return $this->time_created;
    }

    /**
     * @param string $time_created
     * @return bool
     */
    public function set_time_created(string $time_created)
    {
        $this->time_created = $time_created;
        return $this->save('time_created', $time_created);
    }

    /**
     * @return User_model
     */
    public function get_user():User_model
    {
        $this->is_loaded(TRUE);

        if (empty($this->user)) {
            try {
                $this->user = new User_model($this->get_user_id());
            } catch (Exception $exception) {
                $this->user = new User_model();
            }
        }

        return $this->user;
    }

    /**
     * Like_model constructor.
     * @param null $id
     */
    function __construct($id = NULL)
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');

        $this->set_id($id);
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data)
    {
        if (User_model::can_liked()) {
            App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();

            $insert_id = App::get_ci()->s->get_insert_id();

            if ($insert_id > 0) {
                User_model::spent_like();

                return new static($insert_id);
            }
        }
    }

    /**
     * @param int $relation_id
     * @param string $type
     * @return array
     */
    public static function get_all_by_relation_id(int $relation_id, string $type = 'post')
    {
        $result = [];

        $data = App::get_ci()->s
            ->from(self::CLASS_TABLE)
            ->where(['relation_id' => $relation_id, 'type' => $type])
            ->orderBy('time_created','ASC')
            ->many();

        foreach ($data as $item) {
            $result[] = (new self())->set($item);
        }

        return $result;
    }

    /**
     * @param int $relation_id
     * @param string $type
     * @return int
     */
    public static function like_counter(int $relation_id, string $type = 'post')
    {
        return count(self::get_all_by_relation_id($relation_id, $type));
    }

    /**
     * @param int $relation_id
     * @param string $type
     * @return array
     */
    public static function prepareData(int $relation_id, string $type)
    {
        return [
            'user_id'     => User_model::get_session_id(),
            'relation_id' => $relation_id,
            'type'        => $type,
        ];
    }
}