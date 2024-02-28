Feature: Add current expense controller tests

  Scenario: User adds new current expense successfully
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    And there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1"
      }
    ]
    """
    When I open "POST" page "/api/v1/expenses/current" with
    """
    {
      "name": "Pepsi 2-pak",
      "cost": 16.99,
      "category": "category-1",
      "isWish": true,
      "dateOfExpense": "2024-01-16 12:53:32",
      "people": ["example-person-2341235"]
    }
    """
    Then the response with code 201 should be received

  Scenario: User tries to add new current expense with empty request content and he gets errors in response
    When I open "POST" page "/api/v1/expenses/current"
    Then the response with code "400" should be received
    And the response should looks like
    """
    {
      "name": "Missing `name` parameter",
      "cost": "Missing `cost` parameter",
      "category": "Missing `category` parameter",
      "isWish": "Missing `isWish` parameter",
      "dateOfExpense": "Missing `dateOfExpense` parameter",
      "people": "Missing `people` parameter"
    }
    """

  Scenario: User tries to add new current expense with non-existed category and he gets not acceptable code in response with error
    When I open "POST" page "/api/v1/expenses/current" with
    """
    {
      "name": "Pepsi 2-pak",
      "cost": 16.99,
      "category": "category-1324324-non-existed",
      "isWish": true,
      "dateOfExpense": "2024-01-16 12:53:32",
      "people": ["example-person-2341235-non-existed", "example-person-2344352-non-existed"]
    }
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    {
      "category": "Category `category-1324324-non-existed` does not exist"
    }
    """

  Scenario: User tries to add new current expense with non-existed people and he gets not acceptable code in response with error
    When I open "POST" page "/api/v1/expenses/current" with
    """
    {
      "name": "Pepsi 2-pak",
      "cost": 16.99,
      "category": "category-1",
      "isWish": true,
      "dateOfExpense": "2024-01-16 12:53:32",
      "people": ["example-person-2341235-non-existed", "example-person-2344352-non-existed"]
    }
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    {
      "people": [
        "Person `example-person-2341235-non-existed` does not exist",
        "Person `example-person-2344352-non-existed` does not exist"
      ]
    }
    """

  Scenario: User tries to add new current expense with wrong format of date of expense and he gets not acceptable code in response with error
    When I open "POST" page "/api/v1/expenses/current" with
    """
    {
      "name": "Pepsi 2-pak",
      "cost": 16.99,
      "category": "category-1",
      "isWish": true,
      "dateOfExpense": "wrong date of expense",
      "people": ["example-person-2341235"]
    }
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    {
      "dateOfExpense": "Date of expense `wrong date of expense` is not valid date time"
    }
    """

  Scenario: User tries to add new current expense with empty people and he gets not acceptable code in response with error
    Given there is exist person with
    """
    {
      "id": "example-person-2341235",
      "name": "Michal",
      "lastModified": "2024-02-13 12:53:46"
    }
    """
    And there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1"
      }
    ]
    """
    When I open "POST" page "/api/v1/expenses/current" with
    """
    {
      "name": "Pepsi 2-pak",
      "cost": 16.99,
      "category": "category-1",
      "isWish": true,
      "dateOfExpense": "2024-01-16 12:53:32",
      "people": []
    }
    """
    Then the response with code 406 should be received
    And the response should contains message "The current expense must have at least one person assigned to it"