<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\CommonContexts;
use Behat\Behat\Context\Step;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

class HateoasXmlContext extends RawMinkContext
{
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
     * @Given /^I follow the "([^"]*)" link$/
     */
    public function iFollowTheLink($rel)
    {
        $hrefAttributeNode = $this->getCrawler()->filterXPath(sprintf("//link[@rel='%s']/@href", $rel));

        if (1 > $hrefAttributeNode->count()) {
            throw new \RuntimeException(sprintf('No link found with rel "%s"', $rel));
        } else if (1 < $hrefAttributeNode->count()) {
            throw new \RuntimeException(sprintf('More than 1 link found with rel "%s"', $rel));
        }

        $href = $hrefAttributeNode->text();

        return new Step\When(sprintf('I send a GET request on "%s"', $href));
    }

    /**
     * @Then /^there should be (\d+) "([^"]*)" nodes?$/
     */
    public function thereShouldBeNodes($expectedCount, $xpath)
    {
        $domXPath = $this->createXPath();
        $nodeList = $domXPath->query($xpath);

        if ($expectedCount != $nodeList->length) {
            throw new \RuntimeException(sprintf('There are %s nodes instead of %s matching xpath "%s".', $nodeList->length, $expectedCount, $xpath));
        }
    }

    /**
     * @Then /^(?:the )?"([^"]*)" node value should be "([^"]*)"$/
     */
    public function nodeValueShouldBe($xpath, $expectedValue)
    {
        return new Step\Then(sprintf('"%s" node value should be:', $xpath), new PyStringNode($expectedValue));
    }

    /**
     * @Then /^(?:the )?"([^"]*)" node value should be:$/
     */
    public function nodeValueShouldBeText($xpath, PyStringNode $expectedValue)
    {
        $expectedValue = $expectedValue->getRaw();

        $domXPath = $this->createXPath();
        $nodeList = $domXPath->query($xpath);

        if (1 > $nodeList->length) {
            throw new \RuntimeException(sprintf('No node found matching xpath "%s".', $xpath));
        } else if (1 > $nodeList->length) {
            throw new \RuntimeException(sprintf('More than 1 node found matching xpath "%s".', $xpath));
        }

        $nodeValue = $nodeList->item(0)->nodeValue;
        if ($nodeValue != $expectedValue) {
            throw new \RuntimeException(sprintf('Node matching xpath "%s", value is "%s" instead of "%s".', $xpath, $nodeValue, $expectedValue));
        }
    }

    /**
     * We directly use the DOMXPath object because the DOMCrawler wraps the dom inside a root element ...
     *
     * @return DOMXPath
     */
    protected function createXPath()
    {
        $nodes = iterator_to_array($this->getCrawler());

        return new \DOMXPath($nodes[0]->ownerDocument);
    }
}
