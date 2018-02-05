<?php

namespace Inchoo\EmailSend\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\TransportInterface;
use Zend_Mail;
use Zend_Mail_Transport_Smtp;


class SaveControllerObserver implements ObserverInterface
{
    protected $transportBuilder;
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $faq = $observer->getEvent()->getDataByKey('data');

        /**
         * Mailtrap.io - send mail
         */

        /*
        $options = array(
            'auth'     => 'plain',
            'username' => 'b8130b394d6ebf',
            'password' => 'c82066df4c31a8',
            'port' => 2525
        );
        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.mailtrap.io', $options);
        Zend_Mail::setDefaultTransport($mailTransport);

        $mail = new Zend_Mail();
        $mail->addTo('admin@magento.test');
        $mail->setSubject('New submitted F.A.Q.');
        $mail->setBodyHtml(
            '<h1>Hello Admin!</h1>'
                .'You have new F.A.Q. submitted on product with id <b>#' . $faq->getProductId() . '</b><br><br><hr><br>'
                .'Question: <b>' . $faq->getQuestion() . '</b><br><br>'
                .'Customer email: <b>' . $faq->getCustomerEmail() . '</b><br><br>'
                .'Customer Id: <b>#' . $faq->getCustomerId() . '</b><br><br>'
                .'<hr><br>'
                .'My Webshop &reg; http://magento.test'
        );
        $mail->setFrom($faq->getCustomerEmail());

        $mail->send();
    */

        /**
         * Mailcatcher - send mail
         */

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $transport = $this->transportBuilder->setTemplateIdentifier(
                    $this->scopeConfig->getValue('productfaq/emailsend/email_template', $storeScope)
                )
              ->setTemplateModel(\Magento\Email\Model\BackendTemplate::class)
              ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $faq->getStoreId()])
              ->setTemplateVars(['faq' => $faq])
              ->setFrom(['email' => $faq->getCustomerEmail(), 'name' => $faq->getCustomerId()])
              ->addTo($this->scopeConfig->getValue('productfaq/emailsend/email_to', $storeScope), 'Admin')
              ->getTransport();

        $transport->sendMessage();

        return;
    }
}