<?php

namespace Catrobat\AppBundle\Features\Flavor\Context;

use Behat\Gherkin\Node\TableNode;
use Catrobat\AppBundle\Entity\RudeWord;
use Catrobat\AppBundle\Features\Helpers\BaseContext;
use Catrobat\AppBundle\Entity\User;
use Catrobat\AppBundle\Entity\Program;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Catrobat\AppBundle\Services\TokenGenerator;
use Catrobat\AppBundle\Services\CatrobatFileCompressor;
use Catrobat\AppBundle\Entity\FeaturedProgram;
use Catrobat\AppBundle\Entity\ProgramManager;

require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Feature context.
 */
class FeatureContext extends BaseContext
{

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// Support Functions

  private function getStandardProgramFile()
  {
    $filepath = self::FIXTUREDIR . "test.catrobat";
    assertTrue(file_exists($filepath), "File not found");
    return new UploadedFile($filepath, "test.catrobat");
  }

  private function getKodeyProgramFile()
  {
    $filepath = $this->generateProgramFileWith(array('applicationName' => 'Pocket Kodey'));
    assertTrue(file_exists($filepath), "File not found");
    return new UploadedFile($filepath, "program_generated.catrobat");
  }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////

    /**
     * @When /^I upload a catrobat program with the kodey app$/
     */
    public function iUploadACatrobatProgramWithTheKodeyApp()
    {
        $user = $this->insertUser();
        $program = $this->getKodeyProgramFile();
        $response = $this->upload($program, $user);
        assertEquals(200, $response->getStatusCode(), "Wrong response code. " . $response->getContent());
    }

    /**
     * @Then /^the program should be flagged as kodey$/
     */
    public function theProgramShouldBeFlaggedAsKodey()
    {
        $program_manager = $this->getProgramManger();
        $program = $program_manager->find(1);
        assertNotNull($program, "No program added");
        assertEquals("pocketkodey", $program->getFlavor(), "Program is NOT flagged a kodey");
    }

    /**
     * @When /^I upload a standard catrobat program$/
     */
    public function iUploadAStandardCatrobatProgram()
    {
        $user = $this->insertUser();
        $program = $this->getStandardProgramFile();
        $response = $this->upload($program, $user);
        assertEquals(200, $response->getStatusCode(), "Wrong response code. " . $response->getContent());
    }

    /**
     * @Then /^the program should not be flagged as kodey$/
     */
    public function theProgramShouldNotBeFlaggedAsKodey()
    {
        $program_manager = $this->getProgramManger();
        $program = $program_manager->find(1);
        assertNotNull($program, "No program added");
        assertNotEquals("pocketkodey", $program->getFlavor(), "Program is flagged a kodey");
    }

    /**
     * @When /^I get the recent programs with "([^"]*)"$/
     * @When /^I get the most downloaded programs with "([^"]*)"$/
     * @When /^I get the most viewed programs with "([^"]*)"$/
     *
     */
    public function iGetTheMostProgramsWith($url)
    {
        $this->getClient()->request('GET', $url);
    }

    /**
     * @Then /^I should get following programs:$/
     */
    public function iShouldGetFollowingPrograms(TableNode $table)
    {
      $response = $this->getClient()->getResponse();
      assertEquals(200, $response->getStatusCode());
      $responseArray = json_decode($response->getContent(), true);
      $returned_programs = $responseArray['CatrobatProjects'];
      $expected_programs = $table->getHash();
      assertEquals(count($expected_programs), count($returned_programs), "Wrong number of returned programs");
      for($i = 0; $i < count($returned_programs); $i ++)
      {
        assertEquals($expected_programs[$i]["name"], $returned_programs[$i]["ProjectName"], "Wrong order of results");
      }
    }

    /**
     * @Given /^there are programs:$/
     */
    public function thereArePrograms(TableNode $table)
    {
      $programs = $table->getHash();
      for($i = 0; $i < count($programs); $i ++)
      {

        $config = array(
          'name' => $programs[$i]['name'],
          'flavor' => $programs[$i]['flavor']
        );

        $this->insertProgram(null, $config);
      }
    }

    /**
     * @Given /^All programs are from the same user$/
     */
    public function allProgramsAreFromTheSameUser()
    {
      ///
    }

    /**
     * @When /^I get the user\'s programs with "([^"]*)"$/
     */
    public function iGetTheUserSProgramsWith($url)
    {
      $this->getClient()->request('GET', $url, array('user_id' => 1));
    }
}
