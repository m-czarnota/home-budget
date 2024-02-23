Feature:
  Person list controller tests

  Scenario: User gets list of person successfully
    When I open "GET" page "/api/v1/people"
    Then the response with code "200" should be received