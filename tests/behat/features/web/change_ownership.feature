@homepage
Feature: Change ownership for arbitrary game-app

  Background:
    Given there are users:
      | name     | password | token      | email               |
      | Catrobat | 123456   | cccccccccc | dev1@pocketcode.org |
      | probe    | 123456   | cccccccccc | user@pocketcode.org |
    And there are programs:
      | id | name    | description | owned by | downloads | apk_downloads | views | upload time      | version |
      | 1  | Minions | p1          | Catrobat | 3         | 2             | 12    | 01.01.2013 12:00 | 0.8.5   |
      | 2  | Galaxy  | p2          | Catrobat | 10        | 12            | 13    | 01.02.2013 12:00 | 0.8.5   |
      | 3  | Alone   | p3          | Catrobat | 5         | 55            | 2     | 01.03.2013 12:00 | 0.8.5   |
      | 4  | Trolol  | p5          | Catrobat | 5         | 1             | 1     | 01.03.2013 12:00 | 0.8.5   |
      | 5  | Nothing | p6          | Catrobat | 5         | 1             | 1     | 01.03.2013 12:00 | 0.8.5   |
  Scenario: User clicks on steal button
    Given I log in as "Catrobat" with the password "123456"
    When I am on "/pocketcode/profile"
    Then the element ".ownership" should be visible

  Scenario: User "probe" should be able to take any program from "Catrobat" as his own
    Given I log in as "probe" with the password "123456"
    And I am on "/pocketcode/profile"
    When I click ".ownership"
    Then I wait 500 milliseconds
    Then I should see 1 "#myprofile-programs .program"







