Feature: List irregular expenses controller tests
  
  Scenario: User tries to gets list of irregular expenses and he gets success in response
    When I open "GET" page "/api/v1/expenses/irregular"
    Then the response with code "200" should be received