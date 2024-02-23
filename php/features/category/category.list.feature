Feature:
  List categories controller tests

  Scenario: User gets all categories
    When I open "GET" page "/api/v1/categories"
    Then the response with code "200" should be received