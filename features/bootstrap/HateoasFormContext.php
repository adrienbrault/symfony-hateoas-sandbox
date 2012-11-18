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
     * @When /^I start filling the "([^"]*)" form$/
     */
    public function iStartFillingTheForm($rel)
    {
        $formCrawler = $this->getCrawler()->filterXPath(sprintf("//form[@rel='%s']", $rel))->first();

        if (1 > $formCrawler->count()) {
            throw new \RuntimeException(sprintf('No form with the rel "%s" found.', $rel));
        }

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
     * @When /^I fill "([^"]*)" with "([^"]*)"$/
     */
    public function iFillWith($id, $value)
    {
        return new Step\When(sprintf('I fill "%s" with:', $id), new PyStringNode($value));
    }

    /**
     * @When /^I fill "([^"]*)" with:$/
     */
    public function iFillWithText($id, PyStringNode $value)
    {
        $field = $this->formCrawler->filterXPath(sprintf("//*[@id='%s']", $id))->first();

        if (1 > $field->count()) {
            throw new \RuntimeException(sprintf('No field with id "%s" found.', $id));
        } else if (1 > $field->count()) {
            throw new \RuntimeException(sprintf('More than 1 field with id "%s" found.', $id));
        }

        $this->form[$field->attr('name')] = $value;
    }

    /**
     * @When /^I select "([^"]*)" in "([^"]*)"$/
     */
    public function iSelectIn($value, $id)
    {
        $select = $this->formCrawler->filterXPath(sprintf("//select[@id='%s']", $id))->first();
        $selectedOption = $select->filterXPath(sprintf("//option[contains(., '%s')]", $value));

        $this->form[$select->attr('name')]->select($selectedOption->attr('value'));
    }
}
