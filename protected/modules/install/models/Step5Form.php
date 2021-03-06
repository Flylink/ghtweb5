<?php

/**
 * Class Step5Form
 *
 * @property string $name
 * @property string $ip
 * @property int $port
 * @property string $db_host
 * @property int $db_port
 * @property string $db_user
 * @property string $db_pass
 * @property string $db_name
 * @property string $version
 */
class Step5Form extends CFormModel
{
    /**
     * Название сервера
     * @var string
     */
    public $name;

    /**
     * IP сервера
     * @var string
     */
    public $ip;

    /**
     * Порт сервера
     * @var int
     */
    public $port;

    /**
     * Mysql host
     * @var string
     */
    public $db_host;

    /**
     * Mysql port
     * @var int
     */
    public $db_port;

    /**
     * Mysql user
     * @var string
     */
    public $db_user;

    /**
     * Mysql pass
     * @var string
     */
    public $db_pass;

    /**
     * Mysql name
     * @var string
     */
    public $db_name;

    /**
     * Версия сервера
     * @var string
     */
    public $version;

    /**
     * Зарпещенные символы в пароле
     * @var array
     */
    private $_db_pass_denied_chars = array("'", "\\");



    public function rules()
    {
        return array(
            array('name, ip, port, db_host, db_port, db_user, db_pass, db_name, version', 'filter', 'filter' => 'trim'),
            array('name, ip, port, db_host, db_port, db_user, db_name, version', 'required'),
            array('version', 'in', 'range' => array_keys(app()->params['server_versions'])),
            array('db_pass', 'checkPassChars'),
            array('db_pass', 'checkConnect'),
        );
    }

    public function checkPassChars($attribute)
    {
        if($this->db_pass != '')
        {
            foreach($this->_db_pass_denied_chars as $char)
            {
                if(strpos($this->db_pass, $char) !== FALSE)
                {
                    $this->addError($attribute, Yii::t('install', 'В пароле не должно быть <b>:char</b> символа', array(':char' => $char)));
                }
            }
        }
    }

    public function checkConnect($attribute)
    {
        if(!$this->hasErrors())
        {
            try
            {
                $db = new PDO('mysql:host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_name, $this->db_user, $this->db_pass);
                $db = NULL;
            }
            catch(PDOException $e)
            {
                $msg = $e->getMessage();
                $msg = (mb_detect_encoding($msg) == 'UTF-8' ? iconv('cp1251', 'UTF-8', $msg) : $e->getMessage());

                $this->addError($attribute, $msg);
            }
        }
    }

    public function attributeLabels()
    {
        return array(
            'name'          => Yii::t('install', 'Название игрового сервера'),
            'ip'            => Yii::t('install', 'IP сервера'),
            'port'          => Yii::t('install', 'Порт сервера'),
            'db_host'       => Yii::t('install', 'Mysql host'),
            'db_port'       => Yii::t('install', 'Mysql port'),
            'db_user'       => Yii::t('install', 'Mysql user'),
            'db_pass'       => Yii::t('install', 'Mysql pass'),
            'db_name'       => Yii::t('install', 'Mysql name'),
            'version'       => Yii::t('install', 'Версия сервера'),
        );
    }
}
 