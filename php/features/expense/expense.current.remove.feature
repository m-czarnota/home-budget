Feature: Remove current expense controller tests

  Scenario: User removes existed current expense successfully
    Given there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1"
      }
    ]
    """
    And there are exist current expenses with
    """
    [
      {
        "id": "example-current-expense-to-delete",
        "name": "Example current expense to delete",
        "category": "category-1"
      }
    ]
    """
    When I open "DELETE" page "api/v1/expenses/current/example-current-expense-to-delete"
    Then the response with code "204" should be received

  Scenario: User tries to remove non-existed current expense and he gets not found code in response
    When I open "DELETE" page "api/v1/expenses/current/non-example-current-expense-to-delete"
    Then the response with code "404" should be received