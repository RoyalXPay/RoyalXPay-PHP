<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Services;

class EmailService
{
    protected Email $email;

    /**
     * Initializes the email service if it's not already initialized.
     */
    protected function initializeEmail(): void
    {
        if (!isset($this->email)) {
            $this->email = Services::email();
            $this->initializeConfig();
        }
    }

    /**
     * Sends an email with the specified parameters.
     */
    public function sendEmail(string $toEmail, string $subject, string $message, array $attachments = [], string $cc = '', string $bcc = ''): array
    {
        // Ensure the email service is initialized before using it
        $this->initializeEmail();

        // Set the sender's email address and name
        $this->email->setFrom('no-reply@royalxpay.com', 'RoyalXPay');
        // Set the recipient's email address
        $this->email->setTo($toEmail);
        // Set the email subject
        $this->email->setSubject($subject);
        // Set the email body message
        $this->email->setMessage($message);

        // Set CC (Carbon Copy) if provided
        if (!empty($cc)) {
            $this->email->setCC($cc);
        }

        // Set BCC (Blind Carbon Copy) if provided
        if (!empty($bcc)) {
            $this->email->setBCC($bcc);
        }

        // Handle attachments
        foreach ($attachments as $attachment) {
            // Check if the attachment file exists
            if (file_exists($attachment)) {
                $this->email->attach($attachment); // Attach the file to the email
            } else {
                // If the file does not exist, throw an exception
                throw new \RuntimeException("Attachment file does not exist: $attachment");
            }
        }

        // Send the email and check for success
        if ($this->email->send()) {
            return ['success' => true]; // Return success response
        } else {
                // Log and throw full debug info
            $debug = $this->email->printDebugger(['headers', 'subject', 'body']);
            log_message('error', "SMTP Email Error: " . $debug);
            throw new \RuntimeException("Email failed to send. Debug info: " . $debug);
        }
    }

    /**
     * Initializes the email configuration.
     */
    protected function initializeConfig(): void
    {
       

    // $config = [
    //     'protocol'    => 'smtp',
    //     'smtp_host'   => 'smtp.gmail.com',
    //     'smtp_user'   => 'Info.royalxpay@gmail.com', // full mailbox
    //     'smtp_pass'   => 'uzfg zweu zbpf vhhe',     // ⚠️ App Password, NOT your Gmail password
    //     'smtp_port'   => 465,
    //     'smtp_crypto' => 'ssl',                    // use 'ssl' if you switch to port 465
    //     'mailType'    => 'html',
    //     'charset'     => 'utf-8',
    //     'newline'     => "\r\n",
    //     'crlf'        => "\r\n",
    //     'wordWrap'    => true,
    //     'validate'    => true,
    // ];

     $config = [
        'protocol'    => 'smtp',
        'smtp_host'   => 'smtp.office365.com',
        'smtp_user'   => 'no-reply@royalxpay.com', // full mailbox
        'smtp_pass'   => 'kcsqmnkrhjzppqxq',     // ⚠️ App Password, NOT your Gmail password
        'smtp_port'   => 587,
        'smtp_crypto' => 'tls',                    // use 'ssl' if you switch to port 465
        'mailType'    => 'html',
        'charset'     => 'utf-8',
        'newline'     => "\r\n",
        'crlf'        => "\r\n",
        'wordWrap'    => true,
        'validate'    => true,
    ];

        // Initialize the email service with the configuration
        $this->email->initialize($config);


        
    }
}
