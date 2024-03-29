Feature:
  Remove person controller tests

  Scenario: User removes person successfully
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    When I open "DELETE" page "/api/v1/people/example-person-2341235"
    Then the response with code "204" should be received

  Scenario: User tries to remove person who not exist and he gets error
    When I open "DELETE" page "/api/v1/people/non-existed-person-2132345"
    Then the response with code "404" should be received
    And the response should contains message "Person `non-existed-person-2132345` does not exist"
