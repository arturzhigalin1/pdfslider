<?php
class RestCest{

    
    public function checkJsonNoImg(\FunctionalTester $I)
    {
       $I->amOnPage('/index.php?r=site/getimages');
       $I->seeResponseCodeIs(200); 
       $I->see('"status":2');
    }
    
    public function checkJsonWithImg(\FunctionalTester $I)
    {
     $I->amOnPage("/index.php?r=site/getimages&id=aa09141d19b72df4218c296533ef47e8");
       $I->seeResponseCodeIs(200); 
       $I->see('"status":1');
       $I->see('"images"');
    }
}

