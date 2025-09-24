<?php
namespace PHPMailer\PHPMailer;

class SMTP
{
    const VERSION = '6.8.1';
    const CRLF = "\r\n";
    const DEFAULT_PORT = 25;
    const MAX_LINE_LENGTH = 998;
    const DEBUG_OFF = 0;
    const DEBUG_CLIENT = 1;
    const DEBUG_SERVER = 2;
    const DEBUG_CONNECTION = 3;
    const DEBUG_LOWLEVEL = 4;

    public $do_debug = self::DEBUG_OFF;
    public $Debugoutput = 'echo';
    public $do_verp = false;
    public $Timeout = 300;
    public $Timelimit = 300;
    public $Host = 'localhost';
    public $Port = self::DEFAULT_PORT;
    public $Helo = '';
    public $Username = '';
    public $Password = '';
    public $AuthType = '';
    public $Realm = '';
    public $Workstation = '';
    public $Secure = '';
    protected $smtp_conn;
    protected $error = [];
    protected $last_reply = '';
    protected $hello_string = '';

    public function connect($host, $port = null, $timeout = 30, $options = [])
    {
        $this->setError('');
        if ($this->connected()) {
            $this->setError('Already connected to a server');
            return false;
        }
        if (empty($port)) {
            $port = self::DEFAULT_PORT;
        }
        $this->smtp_conn = @fsockopen(
            $host,
            $port,
            $errno,
            $errstr,
            $timeout
        );
        if (empty($this->smtp_conn)) {
            $this->setError('Failed to connect to server');
            return false;
        }
        return true;
    }

    public function authenticate($username, $password, $authtype = null, $realm = '', $workstation = '', $oauth_token = null)
    {
        if (!$this->connected()) {
            return false;
        }
        return true;
    }

    public function connected()
    {
        return !empty($this->smtp_conn);
    }

    protected function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = ['error' => $message, 'detail' => $detail, 'smtp_code' => $smtp_code, 'smtp_code_ex' => $smtp_code_ex];
    }

    public function getError()
    {
        return $this->error;
    }

    public function getLastReply()
    {
        return $this->last_reply;
    }
}