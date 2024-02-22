Feature: Update irregular expenses controller tests

  Scenario: User updates irregular expenses and he gets success response code
    Given there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1"
      },
      {
        "id": "category-2",
        "name": "Category 2"
      }
    ]
    """
    When I open "PUT" page "/api/expense/irregular" with
    """
    [
      {
        "name": "Wakacje w Budapeszcie",
        "cost": 5200,
        "category": "category-1",
        "position": 0,
        "isWish": true,
        "plannedYear": 2024
      },
      {
        "name": "Prezenty na święta",
        "cost": 1000,
        "category": "category-2",
        "position": 1,
        "isWish": true,
        "plannedYear": 2024
      }
    ]
    """
    Then the response with code "200" should be received

  Scenario: The user updates irregular expenses by submitting 2 existing ones, 1 new one, and skipping 1 that already exists. He gets success in response code and in db there are only irregular expenses received by him in response
    Given there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1"
      },
      {
        "id": "category-2",
        "name": "Category 2"
      }
    ]
    """
    And there are exist irregular expenses with
    """
    [
      {
        "id": "irregular-expense-423",
        "name": "Irregular Expense 423",
        "category": "category-2"
      },
      {
        "id": "irregular-expense-75642",
        "name": "Irregular Expense 75642",
        "category": "category-1"
      },
      {
        "id": "irregular-expense-2",
        "name": "Irregular Expense 2",
        "category": "category-1"
      }
    ]
    """
    When I open "PUT" page "/api/expense/irregular" with
    """
    [
      {
        "id": "irregular-expense-423",
        "name": "Irregular Expense 423",
        "cost": 1000,
        "category": "category-2",
        "position": 0,
        "isWish": true,
        "plannedYear": 2025
      },
      {
        "id": "irregular-expense-2",
        "name": "Irregular Expense 2",
        "cost": 1200,
        "category": "category-2",
        "position": 1,
        "isWish": false,
        "plannedYear": 2025
      },
      {
        "name": "Prezenty na dla rodziców",
        "cost": 1000,
        "category": "category-2",
        "position": 2,
        "isWish": true,
        "plannedYear": 2024
      }
    ]
    """
    Then the response with code "200" should be received
    And in db there are only irregular expenses received in response

  Scenario: User tries to update irregular expenses with empty request content and he gets bad request code in response and info
    When I open "PUT" page "/api/expense/irregular"
    Then the response with code "400" should be received
    And the response should contains message "Sent request has not content"

  Scenario: User tries to update irregular expenses with missing parameters in request content and he gets bad request code in response and errors
    When I open "PUT" page "/api/expense/irregular" with
    """
    [
      {
        "name": "name",
        "position": 0
      },
      {},
      {
        "name": "name",
        "position": 0,
        "category": "1",
        "cost": 1000
      },
      {
        "cost": 1000,
        "category": "1"
      }
    ]
    """
    Then the response with code "400" should be received
    And the response should looks like
    """
    {
      "isError": true,
      "errors": [
        {
          "cost": "Missing `cost` parameter",
          "category": "Missing `category` parameter"
        },
        {
          "name": "Missing `name` parameter",
          "cost": "Missing `cost` parameter",
          "category": "Missing `category` parameter",
          "position": "Missing `position` parameter"
        },
        {},
        {
          "name": "Missing `name` parameter",
          "position": "Missing `position` parameter"
        }
      ]
    }
    """

  Scenario: User tries to update irregular expenses with invalid data and he gets bad request in response code and errors
    Given there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1"
      }
    ]
    """
    When I open "PUT" page "/api/expense/irregular" with
    """
    [
      {
        "name": "",
        "cost": -1000,
        "category": "category-1",
        "position": 0,
        "isWish": false,
        "plannedYear": 2024
      },
      {
        "name": "irregular expense",
        "cost": 1000,
        "category": "category-1",
        "position": 0,
        "isWish": false,
        "plannedYear": 2027
      },
      {
        "name": "too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name",
        "cost": 0,
        "category": "category-1",
        "position": -3,
        "isWish": false,
        "plannedYear": 2023
      },
      {
        "name": "",
        "cost": -1000,
        "category": "category-non-existed",
        "position": 0,
        "isWish": false,
        "plannedYear": 2024
      }
    ]
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    [
      {
        "name": "Name cannot be empty",
        "cost": "Cost cannot be negative"
      },
      {},
      {
        "name": "Name cannot be longer than 255 characters",
        "cost": "Cost cannot be 0",
        "position": "Position cannot be negative",
        "plannedYear": "Planned year cannot be earlier than current year $currentYear"
      },
      {
        "category": "Category `category-non-existed` does not exist"
      }
    ]
    """