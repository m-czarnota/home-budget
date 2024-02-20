Feature:
  Update categories controller tests

  Scenario: User successfully creates/updates categories
    When I open "PUT" page "/api/category" with
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

  Scenario: User sends bad request with plain fields instead of objects in array and he gets bad request code in response
    When I open "PUT" page "/api/category" with
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
    When I open "PUT" page "/api/category" with
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
              "position": null,
              "subCategories": []
            },
            {
              "name": null,
              "position": null,
              "subCategories": []
            },
            {
              "name": null,
              "position": "Missing `position` parameter",
              "subCategories": []
            }
          ]
        }
      ]
    }
    """

  Scenario: User sends request with not valid data and he gets not acceptable code in response and errors data
    When I open "PUT" page "/api/category" with
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