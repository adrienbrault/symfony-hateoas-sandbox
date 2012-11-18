<?php

use Behat\Behat\Context\BehatContext;
use Behat\CommonContexts;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Behat\Context\Step;
use Sanpi\Behatch\Context\BehatchContext;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends BehatContext implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct()
    {
        $this->useContext('mink', new MinkContext());
        $this->useContext('mink_extra', new CommonContexts\MinkExtraContext());
        $this->useContext('mink_redirect', new CommonContexts\MinkRedirectContext());
        $this->useContext('symfony_doctrine', new CommonContexts\SymfonyDoctrineContext());
        $this->useContext('symfony_mailer', new CommonContexts\SymfonyMailerContext());
        $this->useContext('behatch', new BehatchContext());
        $this->useContext('hateoas_xml', new HateoasXmlContext());
        $this->useContext('hateoas_form', new HateoasFormContext());
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I am on the root endpoint$/
     */
    public function iAmOnTheRootEndpoint()
    {
        return new Step\Given('I am on homepage');
    }
}
