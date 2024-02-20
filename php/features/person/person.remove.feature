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
    When I open "DELETE" page "api/person/example-person-2341235"
    Then the response with code "204" should be received

  Scenario: User tries to remove person who not exist and he gets error
    When I open "DELETE" page "/api/person/non-existed-person-2132345"
    Then the response with code "406" should be received
    And the response should contains message "Person `non-existed-person-2132345` does not exist"
