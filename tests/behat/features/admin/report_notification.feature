@admin @disabled @wip
Feature: Admin Report Notification
  In order to get informed of an upload
  As a subscribed admin
  I want to get an email when a program is uploaded or reported

  Scenario: Email subscribed admins directly after report
    Given there are users:
      | name     | email           | id | password |
      | Catrobat | admin@catrob.at |  1 | 123456   |
      | User1    | dog@catrob.at   |  2 | 123456   |
      | User2    | dog2@catrob.at  |  3 | 123456   |
    And there are programs:
      | id | name      |
      | 1  | program 1 |
      | 2  | program 2 |
      | 3  | program 3 |
    And there are notifications:
      | user     | upload | report | summary |
      | Catrobat | 1      | true   | 1       |
      | User1    | 1      | 0      | 0       |
      | User2    | 0      | true   | 0       |
    And I activate the Profiler

    When I log in as "Catrobat" with the password "123456"
    And I report program 1 with category "spam" and note "Bad Program" in Browser
    Then I should see 2 outgoing emails
    And I should see a email with recipient "admin@catrob.at"
    And I should see a email with recipient "dog2@catrob.at"
