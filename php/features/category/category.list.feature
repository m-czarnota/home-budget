Feature:
  List categories controller tests

  Scenario: User gets all categories
    When I open "GET" page "/api/v1/categories"
    Then the response with code "200" should be received

  Scenario: User gets all categories sorted by their position
    Given there are exist categories with
    """
      [
        {
          "id": "category-1",
          "name": "Category 1",
          "position": 1,
          "subCategories": [
            {
              "id": "sub-category-1-1",
              "name": "Sub Category 1.1",
              "position": 1
            },
            {
              "id": "sub-category-1-2",
              "name": "Sub Category 1.2",
              "position": 0
            }
          ]
        },
        {
          "id": "category-2",
          "name": "Category 2",
          "position": 0
        }
      ]
    """
    When I open "GET" page "/api/v1/categories"
    Then the response with code "200" should be received
    And categories received in response are sorted by ascending position
    And categories received in response are not deleted