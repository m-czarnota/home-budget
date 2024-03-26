Feature:
  Update categories controller tests

  Scenario: User successfully creates/updates categories
    When I open "PUT" page "/api/v1/categories" with
    """
      [
        {
          "name": "Zakupy spożywcze",
          "position": 0,
          "subCategories": [
            {
              "name": "Jedzenie",
              "position": 0
            },
            {
              "name": "Picie",
              "position": 1
            },
            {
              "name": "Przekąski",
              "position": 2
            }
          ]
        },
        {
          "name": "Opłaty",
          "position": 1,
          "subCategories": [
            {
              "name": "Mieszkanie",
              "position": 0
            },
            {
              "name": "Media",
              "position": 1
            },
            {
              "name": "Serwisy streamingowe",
              "position": 2
            }
          ]
        }
      ]
    """
    Then the response with code "200" should be received
    And all elements should have id

  Scenario: User successfully updates categories, when he deletes one of the categories that has a sub category that is in use
    Given there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1",
        "subCategories": [
          {
            "id": "sub-category-1-1",
            "name": "Sub Category 1.1"
          },
          {
            "id": "sub-category-1-2",
            "name": "Sub Category 1.2"
          }
        ]
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
        "id": "irregular-expense-related-to-subcategory-1-1",
        "name": "Irregular Expense related to subcategory 1.1",
        "category": "sub-category-1-1"
      }
    ]
    """
    When I open "PUT" page "/api/v1/categories" with
    """
      [
        {
          "name": "Zakupy spożywcze",
          "position": 0,
          "subCategories": [
            {
              "name": "Jedzenie",
              "position": 0
            },
            {
              "name": "Picie",
              "position": 1
            },
            {
              "name": "Przekąski",
              "position": 2
            }
          ]
        },
        {
          "name": "Opłaty",
          "position": 1,
          "subCategories": [
            {
              "name": "Mieszkanie",
              "position": 0
            },
            {
              "name": "Media",
              "position": 1
            },
            {
              "name": "Serwisy streamingowe",
              "position": 2
            }
          ]
        }
      ]
    """
    Then the response with code "200" should be received
    And all elements should have id
    And in db there is exist category "sub-category-1-1" marked as deleted
    And in db there is exist category "category-1" marked as deleted
    And in db there is not exist category "sub-category-1-2"

  Scenario: User successfully updates categories. He deletes one of subcategories that is in use
    Given there are exist categories with
    """
    [
      {
        "id": "category-1",
        "name": "Category 1",
        "subCategories": [
          {
            "id": "sub-category-1-1",
            "name": "Sub Category 1.1"
          },
          {
            "id": "sub-category-1-2",
            "name": "Sub Category 1.2"
          }
        ]
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
        "id": "irregular-expense-related-to-subcategory-1-1",
        "name": "Irregular Expense related to subcategory 1.1",
        "category": "sub-category-1-1"
      }
    ]
    """
    When I open "PUT" page "/api/v1/categories" with
    """
      [
        {
          "name": "Zakupy spożywcze",
          "position": 0,
          "subCategories": [
            {
              "name": "Jedzenie",
              "position": 0
            },
            {
              "name": "Picie",
              "position": 1
            },
            {
              "name": "Przekąski",
              "position": 2
            }
          ]
        },
        {
          "name": "Opłaty",
          "position": 1,
          "subCategories": [
            {
              "name": "Mieszkanie",
              "position": 0
            },
            {
              "name": "Media",
              "position": 1
            },
            {
              "name": "Serwisy streamingowe",
              "position": 2
            }
          ]
        },
        {
          "id": "category-1",
          "name": "Category 1",
          "position": 2,
          "subCategories": [
            {
              "id": "sub-category-1-1",
              "name": "Sub Category 1.1",
              "position": 0
            }
          ]
        }
      ]
    """
    Then the response with code "200" should be received
    And all elements should have id
    And in db there is exist category "sub-category-1-1" marked as deleted
    And in db there is exist category "category-1" marked as deleted
    And in db there is not exist category "sub-category-1-2"


  Scenario: User sends bad request with plain fields instead of objects in array and he gets bad request code in response
    When I open "PUT" page "/api/v1/categories" with
    """
    {
      "something not allowed": "random value",
      "categories": [
        {
          "name": "Jedzenie",
          "position": 0,
          "subCategories": []
        }
      ]
    }
    """
    Then the response with code "400" should be received

  Scenario: User sends incomplete request with missing fields and he gets bad request code in response and errors data
    When I open "PUT" page "/api/v1/categories" with
    """
    [
      {
        "name": "Zakupy spożywcze",
        "subCategories": [
          {
            "position": 0
          },
          {
            "name": "Picie",
            "position": 1
          },
          {
            "name": "Przekąski"
          }
        ]
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
          "name": null,
          "position": "Missing `position` parameter",
          "subCategories": [
            {
              "name": "Missing `name` parameter",
              "position": null
            },
            {
              "name": null,
              "position": null
            },
            {
              "name": null,
              "position": "Missing `position` parameter"
            }
          ]
        }
      ]
    }
    """

  Scenario: User sends request with not valid data and he gets not acceptable code in response and errors data
    When I open "PUT" page "/api/v1/categories" with
    """
    [
      {
          "name": "",
          "position": 0,
          "subCategories": [
            {
              "name": "Jedzenie",
              "position": -2
            },
            {
              "name": "Picie",
              "position": 1
            },
            {
              "name": "",
              "position": 2
            }
          ]
        },
        {
          "name": "Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy Opłaty i brak pieniędzy",
          "position": 1,
          "subCategories": [
            {
              "name": "Mieszkanie",
              "position": 0
            },
            {
              "name": "Media",
              "position": 1
            },
            {
              "name": "Serwisy streamingowe",
              "position": 2
            }
          ]
        }
    ]
    """
    Then the response with code "406" should be received
    And the response should looks like
    """
    [
      {
        "name": "Name cannot be empty",
        "subCategories": [
          {
            "position": "Position cannot be negative"
          },
          {},
          {
            "name": "Name cannot be empty"
          }
        ]
      },
      {
        "name": "Name cannot be longer than 255 characters",
        "subCategories": []
      }
    ]
    """