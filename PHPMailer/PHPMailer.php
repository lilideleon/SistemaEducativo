<?php
namespace PHPMailer\PHPMailer;

class PHPMailer
{
    const CHARSET_ASCII = 'us-ascii';
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';

    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED = 'multipart/related';

    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_BINARY = 'binary';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    const ENCRYPTION_STARTTLS = 'tls';
    const ENCRYPTION_SMTPS = 'ssl';

    protected $Priority;
    protected $CharSet = self::CHARSET_ISO88591;
    protected $ContentType = self::CONTENT_TYPE_PLAINTEXT;
    protected $Encoding = self::ENCODING_8BIT;
    protected $ErrorInfo = '';
    protected $From = 'root@localhost';
    protected $FromName = 'Root User';
    protected $Sender = '';
    protected $Subject = '';
    protected $Body = '';
    protected $AltBody = '';
    protected $Mailer = 'mail';
    protected $WordWrap = 0;
    protected $Hostname = '';
    protected $MessageID = '';
    protected $MessageDate = '';
    protected $Host = 'localhost';
    protected $Port = 25;
    protected $Username = '';
    protected $Password = '';
    protected $AuthType = '';
    protected $Timeout = 300;
    protected $SMTPDebug = 0;
    protected $Debugoutput = 'echo';
    protected $SMTPAuth = false;
    protected $SMTPSecure = '';
    protected $AllowEmpty = false;

    protected $to = [];
    protected $cc = [];
    protected $bcc = [];
    protected $ReplyTo = [];
    protected $attachments = [];
    protected $CustomHeader = [];

    public function __construct($exceptions = null)
    {
        //Set default values
    }

    public function isSMTP()
    {
        $this->Mailer = 'smtp';
    }

    public function setFrom($address, $name = '', $auto = true)
    {
        $this->From = $address;
        $this->FromName = $name;
    }

    public function addAddress($address, $name = '')
    {
        $this->to[] = [$address, $name];
    }

    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this->ContentType = self::CONTENT_TYPE_TEXT_HTML;
        } else {
            $this->ContentType = self::CONTENT_TYPE_PLAINTEXT;
        }
    }

    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }
            return $this->postSend();
        } catch (Exception $exc) {
            $this->mailHeader = '';
            $this->setError($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
            return false;
        }
    }

    protected function preSend()
    {
        try {
            $this->mailHeader = '';
            return true;
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            return false;
        }
    }

    protected function postSend()
    {
        return true;
    }

    protected function setError($msg)
    {
        $this->ErrorInfo = $msg;
    }

    public function getError()
    {
        return $this->ErrorInfo;
    }
}