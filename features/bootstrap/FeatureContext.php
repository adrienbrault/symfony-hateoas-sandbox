<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Behat\Event\StepEvent;
use Behat\CommonContexts;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Behat\Context\Step;
use Sanpi\Behatch\Context\BehatchContext;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends RawMinkContext implements KernelAwareInterface
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

    /**
     * Wrap any exceptions into an ExpectationException so that behat shows the last response in verbose mode
     *
     * @AfterStep
     */
    public function after(StepEvent $event)
    {
        if (StepEvent::FAILED == $event->getResult() && !$event->getException() instanceof ExpectationException) {
            $eventReflection = new \ReflectionClass(get_class($event));
            $exceptionPropertyReflection = $eventReflection->getProperty('exception');
            $exceptionPropertyReflection->setAccessible(true);
            $exceptionPropertyReflection->setValue($event, new ExpectationException(null, $this->getSession(), $event->getException()));
        }
    }

    /**
     * @Given /^there are (\d+) tasks?$/
     */
    public function thereAreTasks($count)
    {
        $steps = array();

        for ($i = 0; $i < $count; $i++) {
            $steps[] = new Step\When(
                sprintf('I create a task named "task %d" described by:', $i),
                new PyStringNode(sprintf('Task description %d', $i))
            );
        }

        return $steps;
    }

    /**
     * @When /^I create a task named "([^"]*)" described by:$/
     */
    public function iCreateATaskNamedDescribedBy($name, PyStringNode $descriptionString)
    {
        return array(
            new Step\When('I am on the root endpoint'),
            new Step\When('I follow the "tasks/create" link'),
            new Step\When('I start filling the rel="create" form'),
            new Step\When(sprintf('I fill id="title" with "%s"', $name)),
            new Step\When('I fill id="description" with:', $descriptionString),
            new Step\When('I submit the form'),
        );
    }
}
