<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 27.01.2020
 * Time: 10:10
 */
class User_model extends CI_Emerald_Model {
    const CLASS_TABLE = 'user';


    /** @var string */
    protected $email;
    /** @var string */
    protected $password;
    /** @var string */
    protected $personaname;
    /** @var string */
    protected $profileurl;
    /** @var string */
    protected $avatarfull;
    /** @var int */
    protected $rights;
    /** @var float */
    protected $wallet_balance;
    /** @var float */
    protected $wallet_total_refilled;
    /** @var float */
    protected $wallet_total_withdrawn;
    /** @var integer */
    protected $likes;
    /** @var string */
    protected $time_created;
    /** @var string */
    protected $time_updated;


    private static $_current_user;

    /**
     * @return string
     */
    public function get_email(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function set_email(string $email)
    {
        $this->email = $email;
        return $this->save('email', $email);
    }

    /**
     * @return string|null
     */
    public function get_password(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function set_password(string $password)
    {
        $this->password = $password;
        return $this->save('password', $password);
    }

    /**
     * @return string
     */
    public function get_personaname(): string
    {
        return $this->personaname;
    }

    /**
     * @param string $personaname
     *
     * @return bool
     */
    public function set_personaname(string $personaname)
    {
        $this->personaname = $personaname;
        return $this->save('personaname', $personaname);
    }

    /**
     * @return string
     */
    public function get_avatarfull(): string
    {
        return $this->avatarfull;
    }

    /**
     * @param string $avatarfull
     *
     * @return bool
     */
    public function set_avatarfull(string $avatarfull)
    {
        $this->avatarfull = $avatarfull;
        return $this->save('avatarfull', $avatarfull);
    }

    /**
     * @return int
     */
    public function get_rights(): int
    {
        return $this->rights;
    }

    /**
     * @param int $rights
     *
     * @return bool
     */
    public function set_rights(int $rights)
    {
        $this->rights = $rights;
        return $this->save('rights', $rights);
    }

    /**
     * @return float
     */
    public function get_wallet_balance(): float
    {
        return $this->wallet_balance;
    }

    /**
     * @param float $wallet_balance
     *
     * @return bool
     */
    public function set_wallet_balance(float $wallet_balance)
    {
        $this->wallet_balance = $wallet_balance;
        return $this->save('wallet_balance', $wallet_balance);
    }

    /**
     * @return float
     */
    public function get_wallet_total_refilled(): float
    {
        return $this->wallet_total_refilled;
    }

    /**
     * @param float $wallet_total_refilled
     *
     * @return bool
     */
    public function set_wallet_total_refilled(float $wallet_total_refilled)
    {
        $this->wallet_total_refilled = $wallet_total_refilled;
        return $this->save('wallet_total_refilled', $wallet_total_refilled);
    }

    /**
     * @return float
     */
    public function get_wallet_total_withdrawn(): float
    {
        return $this->wallet_total_withdrawn;
    }

    /**
     * @param float $wallet_total_withdrawn
     *
     * @return bool
     */
    public function set_wallet_total_withdrawn(float $wallet_total_withdrawn)
    {
        $this->wallet_total_withdrawn = $wallet_total_withdrawn;
        return $this->save('wallet_total_withdrawn', $wallet_total_withdrawn);
    }

    /**
     * @return int
     */
    public function get_likes(): int
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     * @return bool
     */
    public function set_likes(int $likes): bool
    {
        $this->likes = $likes;
        return $this->save('likes', $likes);
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
     * @param string $time_updated
     *
     * @return bool
     */
    public function set_time_updated(string $time_updated)
    {
        $this->time_updated = $time_updated;
        return $this->save('time_updated', $time_updated);
    }


    function __construct($id = NULL)
    {
        parent::__construct();
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
     * @return self[]
     * @throws Exception
     */
    public static function get_all()
    {

        $data = App::get_ci()->s->from(self::CLASS_TABLE)->many();
        $ret = [];
        foreach ($data as $i)
        {
            $ret[] = (new self())->set($i);
        }
        return $ret;
    }


    /**
     * @param User_model|User_model[] $data
     * @param string $preparation
     * @return stdClass|stdClass[]
     * @throws Exception
     */
    public static function preparation($data, $preparation = 'default')
    {
        switch ($preparation)
        {
            case 'main_page':
                return self::_preparation_main_page($data);
            case 'default':
                return self::_preparation_default($data);
            default:
                throw new Exception('undefined preparation type');
        }
    }

    /**
     * @param User_model $data
     * @return stdClass
     */
    private static function _preparation_main_page($data)
    {
        $o = new stdClass();

        $o->id = $data->get_id();

        $o->personaname = $data->get_personaname();
        $o->avatarfull = $data->get_avatarfull();

        $o->time_created = $data->get_time_created();
        $o->time_updated = $data->get_time_updated();


        return $o;
    }


    /**
     * @param User_model $data
     * @return stdClass
     */
    private static function _preparation_default($data)
    {
        $o = new stdClass();

        if (!$data->is_loaded())
        {
            $o->id = NULL;
        } else {
            $o->id = $data->get_id();

            $o->personaname = $data->get_personaname();
            $o->avatarfull = $data->get_avatarfull();

            $o->time_created = $data->get_time_created();
            $o->time_updated = $data->get_time_updated();
        }

        return $o;
    }


    /**
     * Getting id from session
     * @return integer|null
     */
    public static function get_session_id(): ?int
    {
        return App::get_ci()->session->userdata('id');
    }

    /**
     * @return bool
     */
    public static function is_logged()
    {
        $steam_id = intval(self::get_session_id());
        return $steam_id > 0;
    }



    /**
     * Returns current user or empty model
     * @return User_model
     */
    public static function get_user()
    {
        if (! is_null(self::$_current_user)) {
            return self::$_current_user;
        }
        if ( ! is_null(self::get_session_id()))
        {
            self::$_current_user = new self(self::get_session_id());
            return self::$_current_user;
        } else
        {
            return new self();
        }
    }

    /**
     * Find user by giving email address
     * @param string $email
     * @return array
     */
    public static function findByEmail(string $email): array
    {
        return App::get_ci()->s->from(self::CLASS_TABLE)->where('email', $email)->one();
    }

    /**
     * Authentication user
     * On db password stored with hashed
     * @param string $email
     * @param string $password
     * @return int
     */
    public static function authenticate(string $email, string $password)
    {
        // password for admin@niceadminmail.pl = password
        // password for simpleuser@niceadminmail.pl = secret
        $user = self::findByEmail($email);

        if (!empty($user) && password_verify($password, $user['password'])) {
            return $user['id'];
        }

        return 0;
    }

    /**
     * Add money to the wallet and wallet_total_refilled
     * @param float $sum
     * @return float
     */
    public function add_money(float $sum): float
    {
        $this->set_wallet_balance($this->incoming_sum_with_balance($sum));
        $this->set_wallet_total_refilled($this->incoming_sum_wallet_total_refilled($sum));

        return $this->get_wallet_balance();
    }

    /**
     * It is the sum witch will be store on user wallet
     * @param float $sum
     * @return float
     */
    private function incoming_sum_with_balance(float $sum)
    {
        return $sum + $this->get_wallet_balance();
    }

    /**
     * It is a sum witch will be store on DB (all user refilled)
     * @param float $sum
     * @return float
     */
    private function incoming_sum_wallet_total_refilled(float $sum)
    {
        return $sum + $this->get_wallet_total_refilled();
    }

    /**
     * Counter for take money from user wallet and put winning likes to their wallet
     * @param User_model $user
     * @param Boosterpack_model $boosterPack
     * @param int $winLikes
     * @throws Exception
     */
    public static function spent_money_for_boosterpack_and_add_likes(self $user, Boosterpack_model $boosterPack, int $winLikes)
    {
        try {
            $user->set_wallet_balance($user->get_wallet_balance() - $boosterPack->get_price());
            $user->set_wallet_total_withdrawn($user->get_wallet_total_withdrawn() + $boosterPack->get_price());
            $user->set_likes($user->get_likes() + $winLikes);
        } catch (Exception $exception) {
            throw new Exception('Something wrong with User model');
        }
    }

    /**
     * We schould to check if user has likes on the bank
     * @return bool
     */
    public static function can_liked(): bool
    {
        return (self::get_user()->get_likes() >= 1);
    }

    /**
     * Take like from user wallet likes
     * @return bool
     */
    public static function spent_like()
    {
        return self::get_user()->set_likes(self::get_user()->get_likes() - 1);
    }
}
