Feature:
  Remove person controller tests

  Scenario: User updates the person successfully
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    When I open "PATCH" page "/api/v1/people/example-person-2341235" with
    """
    {
      "name": "Michal 2"
    }
    """
    Then the response with code "200" should be received
    And the response should looks like
    """
    {
      "id": "example-person-2341235",
      "name": "Michal 2"
    }
    """

  Scenario: User tries to update person who not exist and he gets error
    When I open "PATCH" page "/api/v1/people/non-existed-person-2132345" with
    """
    {
      "name": "Michal"
    }
    """
    Then the response with code "404" should be received
    And the response should contains message "Person `non-existed-person-2132345` does not exist"

  Scenario: User tries to add a person without name and he gets bad request code in response with errors
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    When I open "PATCH" page "/api/v1/people/example-person-2341235" with
    """
    {
      "position": 1
    }
    """
    Then the response with code "400" should be received
    And the response should looks like
    """
    {
      "name": "Missing `name` parameter"
    }
    """

  Scenario: User tries to add a person without sending content in request and he gets bad request code in response with errors
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    When I open "PATCH" page "/api/v1/people/example-person-2341235"
    Then the response with code "400" should be received
    And the response should looks like
    """
    {
      "name": "Missing `name` parameter"
    }
    """

  Scenario: User tries to add a person with empty name and he gets non acceptable code in response with errors
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    When I open "PATCH" page "/api/v1/people/example-person-2341235" with
    """
    {
      "name": ""
    }
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    {
      "name": "Name cannot be empty"
    }
    """

  Scenario: User tries to add a person with too long name and he gets non acceptable code in response with errors
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    When I open "PATCH" page "/api/v1/people/example-person-2341235" with
    """
    {
      "name": "too long name too long name too long name too long name too long name"
    }
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    {
      "name": "Name cannot be longer than 50 characters"
    }
    """