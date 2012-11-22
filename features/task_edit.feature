Feature: Editing task
  As a visitor
  I want to edit existing tasks
  In order to update my progress

  Background:
    Given I create a task named "Improve FSCHateoasBundle" described by:
      """
      1. Something
      2. Something else
      """
    Then I should be redirected

  Scenario: Successfully edit a task
     When I start filling the rel="edit" form
      And I check id="isDone"
      And I submit the form

     Then the response status code should be 202
      And I should be redirected
      And "/task/isDone" node value should be "true"
