Feature:
  Add person controllers test

  Scenario: User adds a person successfully
    When I open "POST" page "/api/person" with
    """
    {
      "name": "Michal"
    }
    """
    Then the response with code "201" should be received

  Scenario: User tries to add a person without name and he gets bad request code in response with errors
    When I open "POST" page "/api/person" with
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
    When I open "POST" page "/api/person"
    Then the response with code "400" should be received
    And the response should looks like
    """
    {
      "name": "Missing `name` parameter"
    }
    """

  Scenario: User tries to add a person with empty name and he gets non acceptable code in response with errors
    When I open "POST" page "/api/person" with
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
    When I open "POST" page "/api/person" with
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
