Feature: Creating task
  As a visitor
  I want to create tasks
  In order to share it with others

  Background:
    Given I am on the root endpoint
    And I follow the "tasks/create" link
    And I start filling the "create" form

  Scenario: Successfully create a task
     When I fill "title" with "Improve FSCHateoasBundle"
      And I fill "description" with:
        """
        1. Something
        2. Something else
        """
      And I submit the form

     Then the response status code should be 201
      And I should be redirected
      And there should be 1 "/task" node
      And "/task/title" node value should be "Improve FSCHateoasBundle"
      And "/task/description" node value should be:
        """
        1. Something
        2. Something else
        """
      And "/task/isDone" node value should be "false"

  Scenario: Trying to create a task with invalid data
     When I fill "title" with "aaa"
      And I submit the form

     Then the response status code should be 400
      And "//form[@name='title']/errors/entry" node value should be "This value is too short. It should have 5 characters or more."

  Scenario: Trying to create a task with no data
     When I fill "title" with "aaa"
      And I submit the form

     Then the response status code should be 400
      And "//form[@name='title']/errors/entry" node value should be "This value is too short. It should have 5 characters or more."
