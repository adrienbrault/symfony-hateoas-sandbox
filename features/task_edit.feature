Feature: Creating task
  As a visitor
  I want to edit existing tasks
  In order to update my progress

  Background:
    Given I am on the root endpoint
      And I follow the "tasks/create" link
      And I start filling the rel="create" form
      And I fill id="title" with "Improve FSCHateoasBundle"
      And I fill id="description" with:
        """
        1. Something
        2. Something else
        """
      And I submit the form
      And I should be redirected

  Scenario: Successfully edit a task
     When I start filling the rel="edit" form
      And I check id="isDone"
      And I submit the form

     Then the response status code should be 202
      And I should be redirected
      And "/task/isDone" node value should be "true"
