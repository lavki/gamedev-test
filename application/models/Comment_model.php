<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 27.01.2020
 * Time: 10:10
 */
class Comment_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'comment';


    /** @var int */
    protected $user_id;
    /** @var int */
    protected $assing_id;
    /** @var string */
    protected $text;
    /** @var int */
    protected $parent_id; // who is parent (default null)

    /** @var string */
    protected $time_created;
    /** @var string */
    protected $time_updated;

    // generated
    protected $comments;
    protected $likes;
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
     *
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
    public function get_assing_id(): int
    {
        return $this->assign_id;
    }

    /**
     * @param int $assing_id
     * @return bool
     */
    public function set_assing_id(int $assing_id)
    {
        $this->assing_id = $assing_id;
        return $this->save('assign_id', $assing_id);
    }


    /**
     * @return string
     */
    public function get_text(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    public function set_text(string $text)
    {
        $this->text = $text;
        return $this->save('text', $text);
    }

    /**
     * @return int|null
     */
    public function get_parent_id():? int
    {
        return $this->parent_id;
    }

    /**
     * @param int $parent_id
     * @return bool
     */
    public function set_parent_id(int $parent_id)
    {
        $this->parent_id = $parent_id;
        return $this->save('parent_id', $parent_id);
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
     *
     * @return bool
     */
    public function set_time_created(string $time_created)
    {
        $this->time_created = $time_created;
        return $this->save('time_created', $time_created);
    }

    /**
     * @return string
     */
    public function get_time_updated(): string
    {
        return $this->time_updated;
    }

    /**
     * @param int $time_updated
     * @return bool
     */
    public function set_time_updated(int $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }

    // generated

    /**
     * Get all likes by assing_id (belong or for post or for comment)
     * @return mixed
     */
    public function get_likes()
    {
        $this->is_loaded(TRUE);

        if (empty($this->likes)) {
            $this->likes = Like_model::get_all_by_relation_id($this->get_id());
        }

        return $this->likes;
    }

    /**
     * @return mixed
     */
    public function get_comments()
    {
        return $this->comments;
    }

    /**
     * @return User_model
     */
    public function get_user(): User_model
    {
        if (is_null($this->user)) {
            try {
                $this->user = new User_model($this->get_user_id());
            } catch (Exception $exception) {
                $this->user = new User_model();
            }
        }

        return $this->user;
    }

    function __construct($id = NULL)
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');


        $this->set_id($id);
    }

    public function reload(bool $for_update = FALSE)
    {
        parent::reload($for_update);

        return $this;
    }

    public static function create(array $data)
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();
        return new static(App::get_ci()->s->get_insert_id());
    }

    public function delete()
    {
        $this->is_loaded(TRUE);
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }

    /**
     * @param int $assting_id
     * @return self[]
     * @throws Exception
     */
    public static function get_all_by_assign_id(int $assting_id)
    {
        $data = App::get_ci()->s->from(self::CLASS_TABLE)->where(['assign_id' => $assting_id])->orderBy('time_created','ASC')->many();

        return self::get_tree_of_comments($data);
    }

    /**
     * Build multi-comments tree (recursive method)
     * @param array $data
     * @param int|null $parent_id
     * @return array
     */
    private static function get_tree_of_comments(array $data, int $parent_id = null): array
    {
        $result = [];
        $counter = 0;

        foreach ($data as $comment) {
            if ($comment['parent_id'] == $parent_id) {

                $result[$counter] = (new self())->set($comment);
                $result[$counter]->comments = (self::get_tree_of_comments($data, $comment['id']));

                $counter++;
            }
        }

        return $result;
    }

    /**
     * @param self|self[] $data
     * @param string $preparation
     * @return stdClass|stdClass[]
     * @throws Exception
     */
    public static function preparation($data, $preparation = 'default')
    {
        switch ($preparation) {
            case 'full_info':
                return self::_preparation_full_info($data);
            default:
                throw new Exception('undefined preparation type');
        }
    }


    /**
     * Class for front side
     * @param array $data
     * @return array
     */
    private static function _preparation_full_info(array $data): array
    {
        $result = [];
        $counter = 0;

        foreach ($data as $comment) {
            $stdClass = new stdClass();
            $stdClass->id   = $comment->get_id();
            $stdClass->assing_id = $comment->get_assing_id();
            $stdClass->text = $comment->get_text();
            $stdClass->user  = User_model::preparation($comment->get_user(),'main_page');
            $stdClass->likes = Like_model::like_counter($comment->get_id(),Like_model::COMMENT_LIKE);
            $stdClass->time_created = $comment->get_time_created();
            $stdClass->time_updated = $comment->get_time_updated();

            $result[$counter] = $stdClass;
            $result[$counter]->comments = self::_preparation_full_info($comment->get_comments());

            $counter++;
        }

        return $result;
    }

    /**
     * Prepare array before store the data into DB
     * @param int $post_id
     * @param string $message
     * @return array
     */
    public static function prepareData(int $post_id, string $message): array
    {
        return [
            'user_id'   => User_model::get_session_id(),
            'assign_id' => $post_id,
            'text'      => urldecode($message),
        ];
    }
}
