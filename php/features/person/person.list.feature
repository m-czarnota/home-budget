Feature:
  Person list controller tests

  Scenario: User gets list of person successfully
    When I open "GET" page "/api/person"
    Then the response with code "200" should be received