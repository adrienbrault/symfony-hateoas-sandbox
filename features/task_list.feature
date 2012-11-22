Feature: List the tasks
  In order to see all the tasks
  As a visitor
  I need to be able to navigate through the pages

  Background:
    Given there are 20 tasks

  Scenario: Navigate through the pages
    Given I am on the root endpoint
      And I follow the "tasks" link

     Then "/*" node attribute "page" should be "1"
      And there should be 0 "/*/link[@rel='previous']" node

     When I follow the "next" link
     Then "/*" node attribute "page" should be "2"

     When I follow the "first" link
     Then "/*" node attribute "page" should be "1"

     When I follow the "last" link
     Then "/*" node attribute "page" should be "2"
      And there should be 0 "/*/link[@rel='next']" node

     When I follow the "previous" link
     Then "/*" node attribute "page" should be "1"

  Scenario: Go to a specific page
    Given I am on the root endpoint
      And I follow the "tasks" link

     When I start filling the rel="pagination" form
      And I fill id="page" with "2"
      And I submit the form

     Then "/*" node attribute "page" should be "2"

  Scenario: Use a custom pagination limit
    Given I am on the root endpoint
      And I follow the "tasks" link

     When I start filling the rel="pagination" form
      And I fill id="limit" with "5"
      And I submit the form

     Then "/*" node attribute "limit" should be "5"
