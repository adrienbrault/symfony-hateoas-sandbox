<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\CommonContexts;
use Behat\Behat\Context\Step;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

class HateoasFormContext extends RawMinkContext
{
    protected $formCrawler;
    protected $form;

    /**
     * @return Client
     */
    protected function getClient()
    {
        return $this->getMink()->getSession()->getDriver()->getClient();
    }

    /**
     * @return Crawler
     */
    protected function getCrawler()
    {
        return $this->getClient()->getCrawler();
    }

    /**
     * @When /^I start filling the rel="([^"]*)" form$/
     * @When /^I start filling the form$/
     */
    public function iStartFillingTheForm($rel = null)
    {
        if (null !== $rel) {
            $formCrawler = $this->getCrawler()->filterXPath(sprintf("//form[@rel='%s']", $rel));
        } else {
            $formCrawler = $this->getCrawler()->filterXPath(sprintf("//form", $rel));
        }
        $this->throwExceptionIfInvalidCount($formCrawler, $rel, 'rel');

        $this->formCrawler = $formCrawler;
        $this->form = $this->formCrawler->form();
    }

    /**
     * @When /^I submit the form$/
     */
    public function iSubmitTheForm()
    {
        $this->getClient()->followRedirects(false);
        $this->getClient()->submit($this->form);
        $this->getClient()->followRedirects(true);

        $this->formCrawler = null;
        $this->form = null;
    }

    /**
     * @When /^I fill id="([^"]*)" with "([^"]*)"$/
     */
    public function iFillWith($id, $value)
    {
        return new Step\When(sprintf('I fill id="%s" with:', $id), new PyStringNode($value));
    }

    /**
     * @When /^I fill id="([^"]*)" with:$/
     */
    public function iFillWithText($id, PyStringNode $value)
    {
        $field = $this->formCrawler->filterXPath(sprintf("//*[@id='%s']", $id));
        $this->throwExceptionIfInvalidCount($field, $id);

        $this->form[$field->attr('name')] = $value;
    }

    /**
     * @When /^I select "([^"]*)" in id="([^"]*)"$/
     */
    public function iSelectIn($value, $id)
    {
        $select = $this->formCrawler->filterXPath(sprintf("//select[@id='%s']", $id));
        $this->throwExceptionIfInvalidCount($select, $id, 'id', 'select');
        $selectedOption = $select->filterXPath(sprintf("//option[contains(., '%s')]", $value));

        $this->form[$select->attr('name')]->select($selectedOption->attr('value'));
    }

    /**
     * @When /^I check id="([^"]*)"$/
     */
    public function iCheck($id)
    {
        $this->setChecked($id, true);
    }

    /**
     * @When /^I uncheck id="([^"]*)"$/
     */
    public function iUnCheck($id)
    {
        $this->setChecked($id, false);
    }

    protected function setChecked($id, $checked = true)
    {
        $input = $this->formCrawler->filterXPath(sprintf("//input[@type='checkbox' and @id='%s']", $id))->first();
        $this->throwExceptionIfInvalidCount($input, $id, 'id', 'checkbox');

        $field = $this->form[$input->attr('name')];

        if ($checked) {
            $field->tick();
        } else {
            $field->untick();
        }
    }

    protected function throwExceptionIfInvalidCount($crawler, $id, $idName = 'id', $nodeName = 'field')
    {
        if (1 > $crawler->count()) {
            throw new \RuntimeException(sprintf('No %s with %s "%s" found.', $nodeName, $idName, $id));
        } else if (1 < $crawler->count()) {
            throw new \RuntimeException(sprintf('More than 1 %s with %s "%s" found.', $nodeName, $idName, $id));
        }
    }
}
